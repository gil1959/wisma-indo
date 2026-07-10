<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\RentCarCategory;
use Illuminate\Http\Request;

class RentCarCategoryController extends Controller
{
    private function guardTourPartner(): void
    {
        abort_unless(auth()->user()->partner_type === 'agency_rental_mobil', 403);
    }

    public function index()
    {
        $this->guardTourPartner();

        $categories = RentCarCategory::where('created_by_partner_id', auth()->id())
            ->orderBy('name')->get();

        return view('partner.rentcar-categories.index', compact('categories'));
    }

    public function create()
    {
        $this->guardTourPartner();
        return view('partner.rentcar-categories.create');
    }

    public function store(Request $request)
    {
        $this->guardTourPartner();

        $request->validate([
            'name' => ['required','string','max:190'],
            'slug' => ['required','string','max:190','unique:rent_car_categories,slug'],
        ]);

        RentCarCategory::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'created_by_partner_id' => auth()->id(),
        ]);

        return redirect()->route('partner.rent-car-categories.index')
            ->with('success', 'Kategori rental berhasil dibuat.');
    }

    public function edit(RentCarCategory $rent_car_category)
    {
        $this->guardTourPartner();
        if ((int) $rent_car_category->created_by_partner_id !== (int) auth()->id()) {
    return redirect()
        ->route('partner.rent-car-categories.index')
        ->with('error', 'Kamu tidak punya akses untuk edit kategori ini.');
}

        $category = $rent_car_category;
        return view('partner.rentcar-categories.edit', compact('category'));
    }

    public function update(Request $request, RentCarCategory $rent_car_category)
    {
        $this->guardTourPartner();
        if ((int) $rent_car_category->created_by_partner_id !== (int) auth()->id()) {
    return redirect()
        ->route('partner.rent-car-categories.index')
        ->with('error', 'Kamu tidak punya akses untuk update kategori ini.');
}

        $request->validate([
            'name' => ['required','string','max:190'],
            'slug' => ['required','string','max:190','unique:rent_car_categories,slug,' . $rent_car_category->id],
        ]);

        $rent_car_category->update($request->only('name','slug'));

        return redirect()->route('partner.rent-car-categories.index')
            ->with('success', 'Kategori rental berhasil diperbarui.');
    }

    public function destroy(RentCarCategory $rent_car_category)
    {
        $this->guardTourPartner();
        if ((int) $rent_car_category->created_by_partner_id !== (int) auth()->id()) {
    return redirect()
        ->route('partner.rent-car-categories.index')
        ->with('error', 'Kamu tidak punya akses untuk menghapus kategori ini.');
}

        if ($rent_car_category->packages()->exists()) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih digunakan paket rent car.');
        }

        $rent_car_category->delete();

        return redirect()->route('partner.rent-car-categories.index')
            ->with('success', 'Kategori rental berhasil dihapus.');
    }
}
