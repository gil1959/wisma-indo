<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListingCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ListingCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = ListingCategory::latest()->get();
        return view('admin.listing_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.listing_categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:property,goods,services',
            'photo' => 'nullable|image|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('public/categories');
            $validated['photo'] = Storage::url($path);
        }

        ListingCategory::create($validated);
        return redirect()->route('admin.listing-categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(ListingCategory $listingCategory)
    {
        return view('admin.listing_categories.edit', compact('listingCategory'));
    }

    public function update(Request $request, ListingCategory $listingCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:property,goods,services',
            'photo' => 'nullable|image|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('photo')) {
            if ($listingCategory->photo) {
                Storage::delete(str_replace('/storage/', 'public/', $listingCategory->photo));
            }
            $path = $request->file('photo')->store('public/categories');
            $validated['photo'] = Storage::url($path);
        }

        $listingCategory->update($validated);
        return redirect()->route('admin.listing-categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(ListingCategory $listingCategory)
    {
        if ($listingCategory->photo) {
            Storage::delete(str_replace('/storage/', 'public/', $listingCategory->photo));
        }
        $listingCategory->delete();
        return redirect()->route('admin.listing-categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
