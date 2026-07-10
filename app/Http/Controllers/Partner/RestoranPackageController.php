<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\RestoranPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RestoranPackageController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->partner_type === 'agency_restoran', 403);

        $packages = RestoranPackage::query()
            ->where('created_by_partner_id', auth()->id())
            ->latest()
            ->get();

        return view('partner.restoran.index', compact('packages'));
    }

    public function create()
    {
        abort_unless(auth()->user()->partner_type === 'agency_restoran', 403);
        return view('partner.restoran.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->partner_type === 'agency_restoran', 403);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'label' => 'nullable|string|max:50',
            'price_per_pax' => 'required|numeric|min:0',
            'thumbnail' => 'nullable|image|max:2048',
            'features' => 'nullable|array',
            'long_description' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ]);

        $data['slug'] = Str::slug($data['title']);
        $data['is_active'] = 0;
        $data['created_by_partner_id'] = auth()->id();
        $data['partner_review_status'] = 'pending';

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('restoran', 'public');
        }

        $cleanFeatures = [];
        foreach ($request->features ?? [] as $f) {
            $cleanFeatures[] = [
                'name' => $f['name'] ?? '',
                'available' => isset($f['available']) ? true : false,
            ];
        }
        $data['features'] = $cleanFeatures;

        RestoranPackage::create($data);

        return redirect()->route('partner.restoran-packages.index')
            ->with('success', 'Paket restoran berhasil dibuat dan menunggu persetujuan admin.');
    }

    public function edit(RestoranPackage $restoran_package)
    {
        abort_unless(auth()->user()->partner_type === 'agency_restoran', 403);
        abort_unless((int)$restoran_package->created_by_partner_id === (int)auth()->id(), 403);

        $package = $restoran_package;
        return view('partner.restoran.edit', compact('package'));
    }

    public function update(Request $request, RestoranPackage $restoran_package)
    {
        abort_unless(auth()->user()->partner_type === 'agency_restoran', 403);
        abort_unless((int)$restoran_package->created_by_partner_id === (int)auth()->id(), 403);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'label' => 'nullable|string|max:50',
            'price_per_pax' => 'required|numeric|min:0',
            'thumbnail' => 'nullable|image|max:2048',
            'features' => 'nullable|array',
            'long_description' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ]);

        $data['slug'] = Str::slug($data['title']);

        if ($request->hasFile('thumbnail')) {
            if ($restoran_package->thumbnail_path) {
                Storage::disk('public')->delete($restoran_package->thumbnail_path);
            }
            $data['thumbnail_path'] = $request->file('thumbnail')->store('restoran', 'public');
        }

        $cleanFeatures = [];
        foreach ($request->features ?? [] as $f) {
            $cleanFeatures[] = [
                'name' => $f['name'] ?? '',
                'available' => isset($f['available']) ? true : false,
            ];
        }
        $data['features'] = $cleanFeatures;

        $restoran_package->update($data);

        $restoran_package->update([
            'is_active'            => 0,
            'partner_review_status'=> 'pending',
            'partner_review_note'  => null,
            'partner_reviewed_by'  => null,
            'partner_reviewed_at'  => null,
        ]);

        return redirect()->route('partner.restoran-packages.index')
            ->with('success', 'Paket restoran berhasil diperbarui dan menunggu persetujuan admin.');
    }

    public function destroy(RestoranPackage $restoran_package)
    {
        abort_unless(auth()->user()->partner_type === 'agency_restoran', 403);
        abort_unless((int)$restoran_package->created_by_partner_id === (int)auth()->id(), 403);

        if ($restoran_package->thumbnail_path) {
            Storage::disk('public')->delete($restoran_package->thumbnail_path);
        }

        $restoran_package->delete();

        return redirect()->route('partner.restoran-packages.index')
            ->with('success', 'Paket restoran berhasil dihapus.');
    }
}
