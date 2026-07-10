<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMicePackageRequest;
use App\Http\Requests\Admin\UpdateMicePackageRequest;
use App\Models\MiceCategory;
use App\Models\MicePackage;
use App\Models\MicePackagePhoto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class MicePackageController extends Controller
{
    public function index(Request $request)
    {
        $q        = trim((string) $request->query('q', ''));
        $category = $request->query('category');
        $status   = $request->query('status');

        $query = MicePackage::query()->with('category')->latest();

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

        $packages = $query->paginate(20)->withQueryString();
        $categories = MiceCategory::orderBy('name')->get();

        return view('admin.mice-packages.index', compact('packages', 'categories', 'q', 'category', 'status'));
    }


    public function create()
    {
        $categories = MiceCategory::orderBy('name')->get();
        return view('admin.mice-packages.create', compact('categories'));
    }

    public function store(StoreMicePackageRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($request, $data) {
            $thumbPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbPath = $request->file('thumbnail')->store('mice/thumbnails', 'public');
            }

            $package = MicePackage::create([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'category_id' => $data['category_id'],

                'label' => $data['label'] ?? null,
                'rating_value' => $data['rating_value'] ?? null,
                'rating_count' => $data['rating_count'] ?? null,

                'destination' => $data['destination'] ?? null,
                'duration_text' => $data['duration_text'] ?? null,

                'long_description' => $data['long_description'] ?? null,
                'itinerary' => $data['itinerary'] ?? null,
                'include_text' => $data['include_text'] ?? null,
                'exclude_text' => $data['exclude_text'] ?? null,

                'thumbnail_path' => $thumbPath,
                'is_active' => (bool)$data['is_active'],

                'seo_title' => $data['seo_title'] ?? null,
                'seo_description' => $data['seo_description'] ?? null,
                'seo_keywords' => $data['seo_keywords'] ?? null,
            ]);

            // tiers
            $this->syncTiers($package, $data['tiers'] ?? []);

            // gallery
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $file) {
                    $path = $file->store('mice/gallery', 'public');
                    $package->photos()->create(['file_path' => $path]);
                }
            }
            \App\Jobs\Translate\MicePackageToEn::dispatch($package->id)
                ->onQueue('translations')
                ->afterCommit();
            return redirect()
                ->route('admin.mice-packages.index')
                ->with('success', 'Paket MICE berhasil dibuat.');
        });
    }

    public function edit(MicePackage $mice_package)
    {
        $package = $mice_package->load(['category', 'tiers', 'photos']);
        $categories = MiceCategory::orderBy('name')->get();

        return view('admin.mice-packages.edit', compact('package', 'categories'));
    }

    public function update(UpdateMicePackageRequest $request, MicePackage $mice_package)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($request, $data, $mice_package) {

            if ($request->hasFile('thumbnail')) {
                if ($mice_package->thumbnail_path) {
                    Storage::disk('public')->delete($mice_package->thumbnail_path);
                }
                $mice_package->thumbnail_path = $request->file('thumbnail')->store('mice/thumbnails', 'public');
            }

            $mice_package->fill([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'category_id' => $data['category_id'],

                'label' => $data['label'] ?? null,
                'rating_value' => $data['rating_value'] ?? null,
                'rating_count' => $data['rating_count'] ?? null,

                'destination' => $data['destination'] ?? null,
                'duration_text' => $data['duration_text'] ?? null,

                'long_description' => $data['long_description'] ?? null,
                'itinerary' => $data['itinerary'] ?? null,
                'include_text' => $data['include_text'] ?? null,
                'exclude_text' => $data['exclude_text'] ?? null,

                'is_active' => (bool)$data['is_active'],

                'seo_title' => $data['seo_title'] ?? null,
                'seo_description' => $data['seo_description'] ?? null,
                'seo_keywords' => $data['seo_keywords'] ?? null,
            ])->save();

            $this->syncTiers($mice_package, $data['tiers'] ?? []);

            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $file) {
                    $path = $file->store('mice/gallery', 'public');
                    $mice_package->photos()->create(['file_path' => $path]);
                }
            }
            \App\Jobs\Translate\MicePackageToEn::dispatch($mice_package->id)
                ->onQueue('translations')
                ->afterCommit();
            return redirect()
                ->route('admin.mice-packages.index')
                ->with('success', 'Paket MICE berhasil diupdate.');
        });
    }

    public function destroy(MicePackage $mice_package)
    {
        if ($mice_package->thumbnail_path) {
            Storage::disk('public')->delete($mice_package->thumbnail_path);
        }

        foreach ($mice_package->photos as $p) {
            Storage::disk('public')->delete($p->file_path);
        }

        $mice_package->delete();

        return redirect()
            ->route('admin.mice-packages.index')
            ->with('success', 'Paket MICE berhasil dihapus.');
    }

    public function deletePhoto($photo)
    {
        $photo = MicePackagePhoto::findOrFail($photo);

        Storage::disk('public')->delete($photo->file_path);
        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    private function syncTiers(MicePackage $package, array $tiersPayload): void
    {
        // payload: tiers[domestic][] + tiers[foreign][]
        $incoming = collect();

        foreach (['domestic', 'foreign'] as $type) {
            $rows = $tiersPayload[$type] ?? [];
            foreach ($rows as $row) {
                // skip foreign kosong total (opsional)
                if ($type === 'foreign' && (!isset($row['price']) || $row['price'] === '' || $row['price'] === null)) {
                    continue;
                }

                $incoming->push([
                    'id' => $row['id'] ?? null,
                    'type' => $type,
                    'label_text' => $row['label_text'] ?? null,
                    'price' => (int)($row['price'] ?? 0),
                    'sort_order' => (int)($row['sort_order'] ?? 0),
                ]);
            }
        }

        $keepIds = [];

        foreach ($incoming as $r) {
            if (!empty($r['id'])) {
                $tier = $package->tiers()->where('id', $r['id'])->first();
                if ($tier) {
                    $tier->update([
                        'type' => $r['type'],
                        'label_text' => $r['label_text'],
                        'price' => $r['price'],
                        'sort_order' => $r['sort_order'],
                    ]);
                    $keepIds[] = $tier->id;
                }
            } else {
                $tier = $package->tiers()->create([
                    'type' => $r['type'],
                    'label_text' => $r['label_text'],
                    'price' => $r['price'],
                    'sort_order' => $r['sort_order'],
                ]);
                $keepIds[] = $tier->id;
            }
        }

        // delete tiers yang tidak ada di payload
        $package->tiers()->whereNotIn('id', $keepIds)->delete();
    }
}
