<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUmrahPackageRequest;
use App\Http\Requests\Admin\UpdateUmrahPackageRequest;
use App\Models\UmrahCategory;
use App\Models\UmrahPackage;
use App\Models\UmrahPackagePhoto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class UmrahPackageController extends Controller
{
    public function index(Request $request)
    {
        $q        = trim((string) $request->query('q', ''));
        $category = $request->query('category');
        $status   = $request->query('status');

        $query = UmrahPackage::query()->with('category');

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%");
            });
        }

        if (!empty($category)) {
            $query->where('category_id', $category);
        }

        if ($status === 'active') {
            $query->where('is_active', 1);
        } elseif ($status === 'inactive') {
            $query->where('is_active', 0);
        }

        $packages = $query->latest()->paginate(20)->withQueryString();
        $categories = \App\Models\UmrahCategory::orderBy('name')->get();

        return view('admin.umrah-packages.index', compact('packages', 'categories', 'q', 'category', 'status'));
    }


    public function create()
    {
        $categories = UmrahCategory::orderBy('name')->get();
        return view('admin.umrah-packages.create', compact('categories'));
    }

    public function store(StoreUmrahPackageRequest $request)
    {
        DB::transaction(function () use ($request) {

            $package = UmrahPackage::create([
                'title'            => $request->title,
                'label'            => $request->label,
                'rating_value'     => $request->rating_value ?? 5,
                'rating_count'     => $request->rating_count ?? 0,
                'slug'             => $request->slug,
                'category_id'      => $request->category_id,
                'duration_text'    => $request->duration_text,
                'destination'      => $request->destination,
                'is_active' => (int) $request->input('is_active', 1),

                'long_description' => $request->long_description,
                'itinerary'        => $request->itinerary,
                'include_text'     => $request->include_text,
                'exclude_text'     => $request->exclude_text,

                'seo_title'        => $request->seo_title,
                'seo_description'  => $request->seo_description,
                'seo_keywords'     => $request->seo_keywords,
            ]);

            // Thumbnail
            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('umrah/thumbnails', 'public');
                $package->update(['thumbnail_path' => $path]);
            }

            // Gallery
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $file) {
                    $path = $file->store('umrah/gallery', 'public');
                    $package->photos()->create(['file_path' => $path]);
                }
            }

            // Harga (tiers)
            $this->syncTiers($package, $request->tiers);

            \App\Jobs\Translate\UmrahPackageToEn::dispatch($package->id)
                ->onQueue('translations')
                ->afterCommit();
        });

        return redirect()->route('admin.umrah-packages.index')
            ->with('success', 'Paket Umrah berhasil dibuat.');
    }

    public function edit(UmrahPackage $umrahPackage)
    {
        $categories = UmrahCategory::orderBy('name')->get();
        $package = $umrahPackage->load(['photos', 'tiers', 'category']);
        return view('admin.umrah-packages.edit', compact('package', 'categories'));
    }

    public function update(UpdateUmrahPackageRequest $request, UmrahPackage $umrahPackage)
    {
        DB::transaction(function () use ($request, $umrahPackage) {

            $umrahPackage->update([
                'title'            => $request->title,
                'label'            => $request->label,
                'rating_value'     => $request->rating_value ?? 5,
                'rating_count'     => $request->rating_count ?? 0,
                'slug'             => $request->slug,
                'category_id'      => $request->category_id,
                'duration_text'    => $request->duration_text,
                'destination'      => $request->destination,
                'is_active' => (int) $request->input('is_active', 1),

                'long_description' => $request->long_description,
                'itinerary'        => $request->itinerary,
                'include_text'     => $request->include_text,
                'exclude_text'     => $request->exclude_text,

                'seo_title'        => $request->seo_title,
                'seo_description'  => $request->seo_description,
                'seo_keywords'     => $request->seo_keywords,
            ]);

            // Thumbnail replace
            if ($request->hasFile('thumbnail')) {
                if ($umrahPackage->thumbnail_path && Storage::disk('public')->exists($umrahPackage->thumbnail_path)) {
                    Storage::disk('public')->delete($umrahPackage->thumbnail_path);
                }
                $path = $request->file('thumbnail')->store('umrah/thumbnails', 'public');
                $umrahPackage->update(['thumbnail_path' => $path]);
            }

            // Gallery add
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $file) {
                    $path = $file->store('umrah/gallery', 'public');
                    $umrahPackage->photos()->create(['file_path' => $path]);
                }
            }

            $this->syncTiers($umrahPackage, $request->tiers);

            \App\Jobs\Translate\UmrahPackageToEn::dispatch($umrahPackage->id)
                ->onQueue('translations')
                ->afterCommit();
        });

        return redirect()->route('admin.umrah-packages.index')
            ->with('success', 'Paket Umrah berhasil diupdate.');
    }

    public function destroy(UmrahPackage $umrahPackage)
    {
        // delete thumbnail
        if ($umrahPackage->thumbnail_path && Storage::disk('public')->exists($umrahPackage->thumbnail_path)) {
            Storage::disk('public')->delete($umrahPackage->thumbnail_path);
        }

        // delete gallery files
        foreach ($umrahPackage->photos as $p) {
            if (Storage::disk('public')->exists($p->file_path)) {
                Storage::disk('public')->delete($p->file_path);
            }
        }

        $umrahPackage->delete();

        return back()->with('success', 'Paket Umrah berhasil dihapus.');
    }

    private function syncTiers(UmrahPackage $package, array $tiers)
    {
        $existing = $package->tiers()->pluck('id')->toArray();
        $submitted = [];

        $sort = 0;
        foreach ($tiers as $row) {
            $sort++;

            $payload = [
                'label_text' => $row['label_text'] ?? '',
                'price' => (int)($row['price'] ?? 0),
                'sort_order' => (int)($row['sort_order'] ?? $sort),
            ];

            if (!empty($row['id'])) {
                $submitted[] = (int)$row['id'];
                $package->tiers()->where('id', $row['id'])->update($payload);
            } else {
                $created = $package->tiers()->create($payload);
                $submitted[] = $created->id;
            }
        }

        $toDelete = array_diff($existing, $submitted);
        if ($toDelete) {
            $package->tiers()->whereIn('id', $toDelete)->delete();
        }
    }

    public function deletePhoto(UmrahPackagePhoto $photo)
    {
        if (Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }
        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}
