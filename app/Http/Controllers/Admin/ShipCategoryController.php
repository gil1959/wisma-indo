<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShipCategory;
use Illuminate\Http\Request;

class ShipCategoryController extends Controller
{
    public function index()
    {
        $categories = ShipCategory::orderBy('name')->get();
        return view('admin.ship-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.ship-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:ship_categories,slug',
        ]);

        ShipCategory::create($request->only('name', 'slug'));

        return redirect()->route('admin.ship-categories.index')
            ->with('success', 'Kategori sewa kapal berhasil dibuat.');
    }

    public function edit(ShipCategory $ship_category)
    {
        return view('admin.ship-categories.edit', compact('ship_category'));
    }

    public function update(Request $request, ShipCategory $ship_category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:ship_categories,slug,' . $ship_category->id,
        ]);

        $ship_category->update($request->only('name', 'slug'));

        return redirect()->route('admin.ship-categories.index')
            ->with('success', 'Kategori sewa kapal berhasil diperbarui.');
    }

    public function destroy(ShipCategory $ship_category)
    {
        if ($ship_category->packages()->exists()) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih dipakai paket sewa kapal.');
        }

        $ship_category->delete();

        return redirect()->route('admin.ship-categories.index')
            ->with('success', 'Kategori sewa kapal berhasil dihapus.');
    }
}
