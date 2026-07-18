<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\ListingCategory;
use App\Models\ListingImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ListingApproved;
use App\Mail\ListingRejected;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $query = Listing::with('user')->latest();
        
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        $listings = $query->paginate(20);
        
        return view('admin.listings.index', compact('listings'));
    }

    public function create(Request $request)
    {
        $kategori = $request->query('kategori', 'properti');
        $categories = ListingCategory::where('type', $kategori == 'properti' ? 'property' : ($kategori == 'barang' ? 'goods' : 'services'))->get();
        return view('admin.listings.create', compact('kategori', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'listing_category_id' => 'required|exists:listing_categories,id',
            'transaction_type' => 'nullable|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'currency' => 'nullable|string',
            'rental_period' => 'nullable|string',
            'min_rental' => 'nullable|string',
            'price_type' => 'nullable|string',
            'co_broke' => 'nullable|boolean',
            'negotiable' => 'nullable|boolean',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'maps_url' => 'nullable|string',
            'property_type' => 'nullable|string',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'land_area' => 'nullable|integer',
            'building_area' => 'nullable|integer',
            'floors' => 'nullable|integer',
            'certificate' => 'nullable|string',
            'imb' => 'nullable|boolean',
            'pbb' => 'nullable|boolean',
            'electricity' => 'nullable|integer',
            'maid_bedrooms' => 'nullable|integer',
            'maid_bathrooms' => 'nullable|integer',
            'car_access' => 'nullable|string',
            'water_source' => 'nullable|string',
            'facing_direction' => 'nullable|string',
            'build_year' => 'nullable|string',
            'carport' => 'nullable|integer',
            'garage' => 'nullable|integer',
            'furnished_status' => 'nullable|string',
            'facilities' => 'nullable|array',
            'surroundings' => 'nullable|array',
            'condition' => 'nullable|string',
            'brand' => 'nullable|string',
            'service_area' => 'nullable|string',
            'phone' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'youtube_url' => 'nullable|string',
            'status' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'images.*' => 'nullable|image|max:2048',
        ]);

        $validated['user_id'] = Auth::id(); // Admin makes it for themselves by default or we could add a user select field.
        $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        $validated['co_broke'] = $request->has('co_broke');
        $validated['negotiable'] = $request->has('negotiable');
        $validated['imb'] = $request->has('imb');
        $validated['pbb'] = $request->has('pbb');
        
        $category = ListingCategory::find($validated['listing_category_id']);
        $validated['category'] = $category->type;
        $validated['type'] = $category->type;
        $validated['status'] = $validated['status'] ?? 'tersedia';

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('public/listings');
            $this->applyWatermark($path);
            $validated['cover_image'] = Storage::url($path);
        }

        $listing = Listing::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                if ($index >= 12) break; // Max 12 images
                $path = $image->store('public/listings');
                $this->applyWatermark($path);
                ListingImage::create([
                    'listing_id' => $listing->id,
                    'image_path' => Storage::url($path),
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return redirect()->route('admin.listings.index')->with('success', 'Iklan berhasil ditambahkan!');
    }

    public function edit(Listing $listing)
    {
        $kategori = $listing->type == 'property' ? 'properti' : ($listing->type == 'goods' ? 'barang' : 'jasa');
        $categories = ListingCategory::where('type', $listing->type)->get();
        return view('admin.listings.edit', compact('listing', 'categories', 'kategori'));
    }

    public function show(Listing $listing)
    {
        $listing->load('user', 'images');
        return view('admin.listings.show', compact('listing'));
    }

    public function update(Request $request, Listing $listing)
    {
        if ($request->has('only_status')) {
            $request->validate([
                'status' => 'required|in:pending,tersedia,terjual,tersewa,nonaktif,rejected',
                'rejection_note' => 'nullable|string'
            ]);
            
            $oldStatus = $listing->status;
            $newStatus = $request->status;
            
            $updateData = ['status' => $newStatus];
            if ($newStatus === 'rejected') {
                $updateData['rejection_note'] = $request->rejection_note;
            } else {
                $updateData['rejection_note'] = null; // Clear note if status changes from rejected
            }
            
            $listing->update($updateData);
            
            // Send email if status changes
            if ($oldStatus !== $newStatus && $listing->user) {
                if ($newStatus === 'tersedia') {
                    Mail::to($listing->user->email)->send(new ListingApproved($listing));
                } elseif ($newStatus === 'rejected') {
                    Mail::to($listing->user->email)->send(new ListingRejected($listing, $request->rejection_note));
                }
            }
            
            return back()->with('success', 'Status iklan berhasil diperbarui!');
        }

        $validated = $request->validate([
            'listing_category_id' => 'required|exists:listing_categories,id',
            'transaction_type' => 'nullable|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'currency' => 'nullable|string',
            'rental_period' => 'nullable|string',
            'min_rental' => 'nullable|string',
            'price_type' => 'nullable|string',
            'co_broke' => 'nullable|boolean',
            'negotiable' => 'nullable|boolean',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'maps_url' => 'nullable|string',
            'property_type' => 'nullable|string',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'land_area' => 'nullable|integer',
            'building_area' => 'nullable|integer',
            'floors' => 'nullable|integer',
            'certificate' => 'nullable|string',
            'imb' => 'nullable|boolean',
            'pbb' => 'nullable|boolean',
            'electricity' => 'nullable|integer',
            'maid_bedrooms' => 'nullable|integer',
            'maid_bathrooms' => 'nullable|integer',
            'car_access' => 'nullable|string',
            'water_source' => 'nullable|string',
            'facing_direction' => 'nullable|string',
            'build_year' => 'nullable|string',
            'carport' => 'nullable|integer',
            'garage' => 'nullable|integer',
            'furnished_status' => 'nullable|string',
            'facilities' => 'nullable|array',
            'surroundings' => 'nullable|array',
            'condition' => 'nullable|string',
            'brand' => 'nullable|string',
            'service_area' => 'nullable|string',
            'phone' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'youtube_url' => 'nullable|string',
            'status' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'images.*' => 'nullable|image|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        $validated['co_broke'] = $request->has('co_broke');
        $validated['negotiable'] = $request->has('negotiable');
        $validated['imb'] = $request->has('imb');
        $validated['pbb'] = $request->has('pbb');
        
        $category = ListingCategory::find($validated['listing_category_id']);
        $validated['category'] = $category->type;
        $validated['type'] = $category->type;

        if ($request->hasFile('cover_image')) {
            if ($listing->cover_image) {
                Storage::delete(str_replace('/storage/', 'public/', $listing->cover_image));
            }
            $path = $request->file('cover_image')->store('public/listings');
            $this->applyWatermark($path);
            $validated['cover_image'] = Storage::url($path);
        }

        $listing->update($validated);

        if ($request->has('delete_images')) {
            $imagesToDelete = \App\Models\ListingImage::whereIn('id', $request->delete_images)
                                ->where('listing_id', $listing->id)
                                ->get();
            foreach ($imagesToDelete as $img) {
                \Illuminate\Support\Facades\Storage::delete(str_replace('/storage/', 'public/', $img->image_path));
                $img->delete();
            }
        }

        if ($request->hasFile('images')) {
            $currentImages = $listing->images()->count();
            foreach ($request->file('images') as $image) {
                if ($currentImages >= 12) break;
                $path = $image->store('public/listings');
                $this->applyWatermark($path);
                ListingImage::create([
                    'listing_id' => $listing->id,
                    'image_path' => Storage::url($path),
                    'is_primary' => $currentImages === 0,
                ]);
                $currentImages++;
            }
        }

        return redirect()->route('admin.listings.index')->with('success', 'Iklan berhasil diperbarui!');
    }

    public function destroy(Listing $listing)
    {
        if ($listing->cover_image) {
            Storage::delete(str_replace('/storage/', 'public/', $listing->cover_image));
        }
        foreach($listing->images as $img) {
            Storage::delete(str_replace('/storage/', 'public/', $img->image_path));
        }
        
        $listing->delete();
        return redirect()->route('admin.listings.index')->with('success', 'Iklan berhasil dihapus!');
    }

    private function applyWatermark($path)
    {
        $siteLogo = \App\Models\Setting::where('key', 'site_logo')->first()->value ?? null;
        $brandName = \App\Models\Setting::where('key', 'brand_name')->first()->value ?? 'Wisma Indo';
        if (!$siteLogo) return;

        $logoPath = public_path(str_replace('/storage/', 'storage/', $siteLogo));
        if (!file_exists($logoPath)) return;

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
            $fontPath = 'C:\\Windows\\Fonts\\arialbd.ttf'; // Arial Bold agar tebal
            if (file_exists($fontPath)) {
                $textX = $startX + $logoWidth + $padding;
                $textY = $startY + intval($logoHeight * 0.85); // Baseline text
                $image->text($brandName, $textX, $textY, function($font) use ($fontPath, $fontSize) {
                    $font->file($fontPath);
                    $font->size($fontSize);
                    $font->color('rgba(128, 128, 128, 0.85)'); // Abu-abu semi transparan tapi jelas
                    $font->align('left');
                });
            }

            $image->save(storage_path('app/' . $path));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Watermark failed: " . $e->getMessage());
        }
    }
}
