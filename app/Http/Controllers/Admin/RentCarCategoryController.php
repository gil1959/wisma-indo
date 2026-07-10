<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentCarCategory;
use Illuminate\Http\Request;

class RentCarCategoryController extends Controller
{
    public function index()
    {
        $categories = RentCarCategory::orderBy('name')->get();
        return view('admin.rentcar-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.rentcar-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:rent_car_categories,slug',
        ]);

        RentCarCategory::create($request->only('name', 'slug'));

        return redirect()->route('admin.rent-car-categories.index')
            ->with('success', 'Kategori rental berhasil dibuat.');
    }

    public function edit(RentCarCategory $rent_car_category)
    {
        $category = $rent_car_category;
        return view('admin.rentcar-categories.edit', compact('category'));
    }

    public function update(Request $request, RentCarCategory $rent_car_category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:rent_car_categories,slug,' . $rent_car_category->id,
        ]);

        $rent_car_category->update($request->only('name', 'slug'));

        return redirect()->route('admin.rent-car-categories.index')
            ->with('success', 'Kategori rental berhasil diperbarui.');
    }

    public function destroy(RentCarCategory $rent_car_category)
    {
        if ($rent_car_category->packages()->exists()) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih dipakai paket rental.');
        }

        $rent_car_category->delete();

        return redirect()->route('admin.rent-car-categories.index')
            ->with('success', 'Kategori rental berhasil dihapus.');
    }
}
