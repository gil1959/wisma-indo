<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\ShipCategory;
use Illuminate\Http\Request;

class ShipCategoryController extends Controller
{
    private function guardShipPartner(): void
    {
        abort_unless(auth()->user()->partner_type === 'agency_kapal', 403);
    }

    public function index()
    {
        $this->guardShipPartner();

        $categories = ShipCategory::where('created_by_partner_id', auth()->id())
            ->orderBy('name')->get();

        return view('partner.ship-categories.index', compact('categories'));
    }

    public function create()
    {
        $this->guardShipPartner();
        return view('partner.ship-categories.create');
    }

    public function store(Request $request)
    {
        $this->guardShipPartner();

        $request->validate([
            'name' => ['required','string','max:190'],
            'slug' => ['required','string','max:190','unique:ship_categories,slug'],
        ]);

        ShipCategory::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'created_by_partner_id' => auth()->id(),
        ]);

        return redirect()->route('partner.ship-categories.index')
            ->with('success', 'Kategori kapal berhasil dibuat.');
    }

    public function edit(ShipCategory $ship_category)
{
    $this->guardShipPartner();

    if ((int) $ship_category->created_by_partner_id !== (int) auth()->id()) {
        return redirect()
            ->route('partner.ship-categories.index')
            ->with('error', 'Kamu tidak punya akses untuk edit kategori ini.');
    }

    $category = $ship_category;
    return view('partner.ship-categories.edit', compact('category'));
}

    public function update(Request $request, ShipCategory $ship_category)
{
    $this->guardShipPartner();

    if ((int) $ship_category->created_by_partner_id !== (int) auth()->id()) {
        return redirect()
            ->route('partner.ship-categories.index')
            ->with('error', 'Kamu tidak punya akses untuk update kategori ini.');
    }

    $request->validate([
        'name' => ['required','string','max:190'],
        'slug' => ['required','string','max:190','unique:ship_categories,slug,' . $ship_category->id],
    ]);

    $ship_category->update($request->only('name','slug'));

    return redirect()->route('partner.ship-categories.index')
        ->with('success', 'Kategori kapal berhasil diperbarui.');
}


    public function destroy(ShipCategory $ship_category)
{
    $this->guardShipPartner();

    // Jangan lempar 403 page. Balikin user ke halaman sebelumnya + alert.
    if ((int) $ship_category->created_by_partner_id !== (int) auth()->id()) {
        return redirect()
            ->route('partner.ship-categories.index')
            ->with('error', 'Kamu tidak punya akses untuk menghapus kategori ini.');
    }

    // Jangan izinkan hapus kalau masih dipakai
    if ($ship_category->packages()->exists()) {
        return redirect()
            ->route('partner.ship-categories.index')
            ->with('error', 'Kategori tidak bisa dihapus karena masih digunakan paket kapal.');
    }

    $ship_category->delete();

    return redirect()
        ->route('partner.ship-categories.index');
        
}

}
