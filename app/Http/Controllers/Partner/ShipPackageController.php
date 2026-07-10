<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreShipPackageRequest;
use App\Http\Requests\Partner\UpdateShipPackageRequest;
use App\Models\ShipCategory;
use App\Models\ShipPackage;
use App\Models\ShipPackageTier;
use Illuminate\Support\Facades\Storage;

class ShipPackageController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->partner_type === 'agency_kapal', 403);

        $categories = ShipCategory::orderBy('name')->get();

        $q = trim((string)request('q', ''));
        $categoryId = request('category_id');
        $active = request('active'); // '1' / '0' / null

        $packagesQuery = ShipPackage::with('category')
            ->where('created_by_partner_id', auth()->id());

        if ($q !== '') {
            $packagesQuery->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%");
            });
        }

        if (!empty($categoryId)) {
            $packagesQuery->where('category_id', $categoryId);
        }

        if ($active === '1' || $active === '0') {
            $packagesQuery->where('is_active', (int)$active);
        }

        $packages = $packagesQuery
            ->latest()
            ->paginate(12)
            ->appends(request()->query());

        return view('partner.ship-packages.index', compact('packages', 'categories', 'q', 'categoryId', 'active'));
    }


    public function create()
    {
        abort_unless(auth()->user()->partner_type === 'agency_kapal', 403);

        $categories = ShipCategory::query()
            ->where(function ($q) {
                $q->whereNull('created_by_partner_id')
                    ->orWhere('created_by_partner_id', auth()->id());
            })
            ->orderBy('name')
            ->get();
        return view('partner.ship-packages.create', compact('categories'));
    }

    public function store(StoreShipPackageRequest $request)
    {
        abort_unless(auth()->user()->partner_type === 'agency_kapal', 403);

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

            // wajib partner
            'is_active' => 0,
            'created_by_partner_id' => auth()->id(),
            'partner_review_status' => 'pending',

            'features' => $this->normalizeFeatures($data['features'] ?? []),
            'long_description' => $data['long_description'] ?? null,
            'seo_title' => $data['seo_title'] ?? null,
            'seo_keywords' => $data['seo_keywords'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'rating_value' => $data['rating_value'] ?? 5,
            'rating_count' => $data['rating_count'] ?? 0,
        ]);

        $pkg->forceFill([
            'created_by_partner_id' => auth()->id(),
            'partner_review_status' => 'pending',
            'is_active' => 0,
        ])->save();


        $this->syncTiers($pkg, $data['tiers'] ?? []);
        \App\Jobs\Translate\ShipPackageToEn::dispatch($pkg->id)
            ->onQueue('translations')
            ->afterCommit();
        return redirect()->route('partner.ship-packages.index')
            ->with('success', 'Paket sewa kapal berhasil dibuat dan menunggu review admin.');
    }

    public function edit(ShipPackage $ship_package)
    {
        abort_unless(auth()->user()->partner_type === 'agency_kapal', 403);
        abort_unless((int)$ship_package->created_by_partner_id === (int)auth()->id(), 403);

        $package = $ship_package->load('tiers');
        $categories = ShipCategory::query()
            ->where(function ($q) {
                $q->whereNull('created_by_partner_id')
                    ->orWhere('created_by_partner_id', auth()->id());
            })
            ->orderBy('name')
            ->get();

        return view('partner.ship-packages.edit', compact('package', 'categories'));
    }

    public function update(UpdateShipPackageRequest $request, ShipPackage $ship_package)
    {
        abort_unless(auth()->user()->partner_type === 'agency_kapal', 403);
        abort_unless((int)$ship_package->created_by_partner_id === (int)auth()->id(), 403);

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

            'features' => $this->normalizeFeatures($data['features'] ?? []),
            'long_description' => $data['long_description'] ?? null,
            'seo_title' => $data['seo_title'] ?? null,
            'seo_keywords' => $data['seo_keywords'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'rating_value' => $data['rating_value'] ?? $ship_package->rating_value,
            'rating_count' => $data['rating_count'] ?? $ship_package->rating_count,
        ])->save();

        // paksa pending lagi
        $ship_package->forceFill([
            'is_active' => 0,
            'partner_review_status' => 'pending',
            'partner_review_note'  => null,
            'partner_reviewed_by'  => null,
            'partner_reviewed_at'  => null,
        ])->save();


        $this->syncTiers($ship_package, $data['tiers'] ?? []);
        \App\Jobs\Translate\ShipPackageToEn::dispatch($ship_package->id)
            ->onQueue('translations')
            ->afterCommit();
        return redirect()->route('partner.ship-packages.index')
            ->with('success', 'Paket sewa kapal berhasil diupdate dan menunggu review admin.');
    }

    public function destroy(ShipPackage $ship_package)
    {
        abort_unless(auth()->user()->partner_type === 'agency_kapal', 403);
        abort_unless((int)$ship_package->created_by_partner_id === (int)auth()->id(), 403);

        if ($ship_package->thumbnail_path) {
            Storage::disk('public')->delete($ship_package->thumbnail_path);
        }

        $ship_package->delete();

        return back()->with('success', 'Paket sewa kapal berhasil dihapus.');
    }
    public function show(ShipPackage $ship_package)
    {
        abort_unless(auth()->user()->partner_type === 'agency_kapal', 403);
        abort_unless((int)$ship_package->created_by_partner_id === (int)auth()->id(), 403);

        // Karena tidak ada halaman detail "show", arahkan ke halaman edit
        return redirect()->route('partner.ship-packages.edit', $ship_package->id);
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
