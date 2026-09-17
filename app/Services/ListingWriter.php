<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\ListingCategory;
use App\Models\UserQuota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ListingWriter
{
    public function save(int $userId, array $data, Request $request, ?Listing $existing = null): Listing
    {
        $stored = [];
        $obsolete = [];
        try {
            $listing = DB::transaction(function () use ($userId, $data, $request, $existing, &$stored, &$obsolete) {
                \App\Models\User::whereKey($userId)->lockForUpdate()->firstOrFail();
                if ($existing) {
                    $listing = Listing::where('user_id', $userId)->lockForUpdate()->findOrFail($existing->id);
                } else {
                    $quota = UserQuota::where('user_id', $userId)->lockForUpdate()->first();
                    if (!$quota || ($quota->listing_quota <= 0 && (int) $quota->listing_quota !== -1)) {
                        throw ValidationException::withMessages(['listing_quota' => 'Kuota iklan habis. Silakan isi kuota terlebih dahulu.']);
                    }
                    $listing = new Listing();
                    $data['user_id'] = $userId;
                    $data['status'] = 'tersedia';
                }
                $category = ListingCategory::findOrFail($data['listing_category_id']);
                $data['type'] = $category->type;
                $data['category'] = $category->type;
                if ($existing && $listing->status === 'rejected') $data['status'] = 'tersedia';
                $deleteIds = $request->input('delete_images', []);
                $remove = $existing ? $listing->images()->whereIn('id', $deleteIds)->get() : collect();
                $currentCount = $existing ? $listing->images()->count() : 0;
                $uploads = $request->file('images', []);
                if ($currentCount - $remove->count() + count($uploads) > 18) {
                    throw ValidationException::withMessages(['images' => 'Galeri maksimal 18 foto. Hapus foto lama sebelum menambahkan foto baru.']);
                }
                unset($data['images'], $data['delete_images'], $data['cover_image']);
                foreach (['co_broke', 'negotiable', 'imb', 'pbb'] as $key) {
                    if (array_key_exists($key, $data)) $data[$key] = $request->boolean($key);
                }
                if ($request->hasFile('cover_image')) {
                    if ($listing->cover_image) $obsolete[] = $listing->cover_image;
                    $data['cover_image'] = $this->storeImage($request->file('cover_image'), $stored);
                }
                $listing->fill($data)->save();
                foreach ($remove as $image) {
                    $obsolete[] = $image->image_path;
                    $image->delete();
                }
                foreach ($uploads as $image) {
                    $listing->images()->create(['image_path' => $this->storeImage($image, $stored), 'is_primary' => false]);
                }
                $listing->images()->update(['is_primary' => false]);
                $first = $listing->images()->orderBy('id')->first();
                if ($first) $first->update(['is_primary' => true]);
                if (!$existing && (int) $quota->listing_quota !== -1) $quota->decrement('listing_quota');
                return $listing->load(['listingCategory', 'images']);
            });
        } catch (\Throwable $e) {
            foreach ($stored as $path) Storage::disk('public')->delete($path);
            throw $e;
        }
        foreach ($obsolete as $path) $this->deleteImage($path);
        return $listing;
    }

    public function delete(int $userId, int $id): void
    {
        $paths = DB::transaction(function () use ($userId, $id) {
            $listing = Listing::where('user_id', $userId)->lockForUpdate()->findOrFail($id);
            $paths = $listing->images()->pluck('image_path')->all();
            if ($listing->cover_image) $paths[] = $listing->cover_image;
            $listing->images()->delete();
            $listing->delete();
            // Web policy: deleting an advertisement does not refund its quota.
            return $paths;
        });
        foreach ($paths as $path) $this->deleteImage($path);
    }

    private function storeImage($file, array &$stored): string
    {
        $path = $file->store('listings', 'public');
        if (!$path) throw new \RuntimeException('Foto tidak dapat disimpan.');
        $stored[] = $path;
        $this->applyWatermark('public/' . $path);
        return '/storage/' . $path;
    }

    private function deleteImage(string $url): void
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        if (strpos($path, '/storage/listings/') === 0 && strpos($path, '..') === false) {
            try { Storage::disk('public')->delete(substr($path, strlen('/storage/'))); }
            catch (\Throwable $e) { report($e); }
        }
    }

    private function applyWatermark($path)
    {
        if (!class_exists(\Intervention\Image\ImageManager::class)) {
            \Illuminate\Support\Facades\Log::error("Watermark: ImageManager class not found.");
            return;
        }
        
        $siteLogo = \App\Models\Setting::where('key', 'site_logo')->first()->value ?? null;
        $brandName = \App\Models\Setting::where('key', 'brand_name')->first()->value ?? 'Wisma Indo';
        if (!$siteLogo) {
            \Illuminate\Support\Facades\Log::error("Watermark: site_logo setting not found.");
            return;
        }

        $logoPath = public_path(str_replace('/storage/', 'storage/', $siteLogo));
        if (!file_exists($logoPath)) {
            // Fallback for cPanel if symlink doesn't exist
            $logoPath = storage_path('app/public/' . str_replace('/storage/', '', $siteLogo));
        }
        
        if (!file_exists($logoPath)) {
            \Illuminate\Support\Facades\Log::error("Watermark: Logo file does not exist at path: " . $logoPath);
            return;
        }

        try {
            $manager = new \Intervention\Image\ImageManager(\Intervention\Image\Drivers\Gd\Driver::class);
            $image = $manager->decodePath(storage_path('app/' . $path));
            $watermark = $manager->decodePath($logoPath);
            
            // Layout seperti Navbar: Logo di kiri, Teks di kanan
            // Ukuran logo dibuat proporsional (8% dari tinggi gambar utama)
            $logoHeight = intval($image->height() * 0.08);
            if ($logoHeight < 20) $logoHeight = 20;
            $logoWidth = intval($watermark->width() * ($logoHeight / max($watermark->height(), 1)));
            
            $watermark->scale(height: $logoHeight);
            $watermark->sharpen(15); // Tambah ketajaman (HD)
            $watermark->grayscale();
            
            $fontSize = intval($logoHeight * 0.8);
            $approxTextWidth = strlen($brandName) * ($fontSize * 0.55);
            $padding = 15;
            $totalWidth = $logoWidth + $padding + $approxTextWidth;
            
            // Hitung posisi agar grup logo+teks berada pas di tengah gambar
            $startX = intval(($image->width() - $totalWidth) / 2);
            $startY = intval(($image->height() - $logoHeight) / 2);
            
            // Masukkan logo
            $image->insert($watermark, $startX, $startY, 'top-left', 0.85);
            
            // Masukkan teks di sebelah kanan logo
            $fontPath = public_path('fonts/arialbd.ttf'); // Menggunakan font dari project
            if (file_exists($fontPath)) {
                $textX = $startX + $logoWidth + $padding;
                $textY = $startY + intval($logoHeight * 0.85); // Baseline text
                $image->text($brandName, $textX, $textY, function($font) use ($fontPath, $fontSize) {
                    $font->file($fontPath);
                    $font->size($fontSize);
                    $font->color('rgba(128, 128, 128, 0.85)');
                    $font->align('left');
                });
            } else {
                \Illuminate\Support\Facades\Log::error("Watermark: Font file does not exist at path: " . $fontPath);
            }

            $image->scaleDown(width: 1200);
            $image->save(storage_path('app/' . $path), quality: 75);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Watermark failed: " . $e->getMessage());
        }
    }
}
