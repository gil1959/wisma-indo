<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreShipPackageRequest;
use App\Http\Requests\Admin\UpdateShipPackageRequest;
use App\Models\ShipCategory;
use App\Models\ShipPackage;
use App\Models\ShipPackageTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ShipPackageController extends Controller
{
    public function index(Request $request)
    {
        $q        = trim((string) $request->query('q', ''));
        $category = $request->query('category');
        $status   = $request->query('status');

        $query = ShipPackage::query()->with('category');

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

        $packages = $query->latest()->paginate(12)->withQueryString();
        $categories = \App\Models\ShipCategory::orderBy('name')->get();

        return view('admin.ship-packages.index', compact('packages', 'categories', 'q', 'category', 'status'));
    }


    public function create()
    {
        $categories = ShipCategory::orderBy('name')->get();
        return view('admin.ship-packages.create', compact('categories'));
    }

    public function store(StoreShipPackageRequest $request)
    {
        $data = $request->validated();

        $thumbPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbPath = $request->file('thumbnail')->store('ship-packages', 'public');
        }

        $pkg = ShipPackage::create([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'label' => $data['label'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'thumbnail_path' => $thumbPath,
            'is_active' => (int)$data['is_active'],
            'features' => $this->normalizeFeatures($data['features'] ?? []),
            'long_description' => $data['long_description'] ?? null,
            'seo_title' => $data['seo_title'] ?? null,
            'seo_keywords' => $data['seo_keywords'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'rating_value' => $data['rating_value'] ?? 5,
            'rating_count' => $data['rating_count'] ?? 0,
        ]);

        $this->syncTiers($pkg, $data['tiers'] ?? []);
        \App\Jobs\Translate\ShipPackageToEn::dispatch($pkg->id)
            ->onQueue('translations')
            ->afterCommit();
        return redirect()->route('admin.ship-packages.index')
            ->with('success', 'Paket sewa kapal berhasil dibuat.');
    }

    public function edit(ShipPackage $ship_package)
    {
        $package = $ship_package->load('tiers');
        $categories = ShipCategory::orderBy('name')->get();
        return view('admin.ship-packages.edit', compact('package', 'categories'));
    }
    public function show(ShipPackage $ship_package)
    {

        return redirect()->route('admin.ship-packages.edit', $ship_package->id);
    }

    public function update(UpdateShipPackageRequest $request, ShipPackage $ship_package)
    {
        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            if ($ship_package->thumbnail_path) {
                Storage::disk('public')->delete($ship_package->thumbnail_path);
            }
            $ship_package->thumbnail_path = $request->file('thumbnail')->store('ship-packages', 'public');
        }

        $ship_package->fill([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'label' => $data['label'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'is_active' => (int)$data['is_active'],
            'features' => $this->normalizeFeatures($data['features'] ?? []),
            'long_description' => $data['long_description'] ?? null,
            'seo_title' => $data['seo_title'] ?? null,
            'seo_keywords' => $data['seo_keywords'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'rating_value' => $data['rating_value'] ?? $ship_package->rating_value,
            'rating_count' => $data['rating_count'] ?? $ship_package->rating_count,
        ])->save();

        $this->syncTiers($ship_package, $data['tiers'] ?? []);
        \App\Jobs\Translate\ShipPackageToEn::dispatch($ship_package->id)
            ->onQueue('translations')
            ->afterCommit();
        return redirect()->route('admin.ship-packages.index')
            ->with('success', 'Paket sewa kapal berhasil diupdate.');
    }

    public function destroy(ShipPackage $ship_package)
    {
        if ($ship_package->thumbnail_path) {
            Storage::disk('public')->delete($ship_package->thumbnail_path);
        }

        $ship_package->delete();

        return back()->with('success', 'Paket sewa kapal berhasil dihapus.');
    }

    private function normalizeFeatures(array $features): array
    {
        $out = [];
        foreach ($features as $f) {
            $name = trim((string)($f['name'] ?? ''));
            if ($name === '') continue;

            $out[] = [
                'name' => $name,
                'available' => !empty($f['available']),
            ];
        }
        return $out;
    }

    private function syncTiers(ShipPackage $pkg, array $tiers): void
    {
        // wipe & recreate (simple + consistent)
        ShipPackageTier::where('ship_package_id', $pkg->id)->delete();

        $i = 0;
        foreach ($tiers as $row) {
            ShipPackageTier::create([
                'ship_package_id' => $pkg->id,
                'type' => $row['type'],
                'label_text' => $row['label_text'],
                'price' => (int)$row['price'],
                'sort_order' => $i++,
            ]);
        }
    }
}
