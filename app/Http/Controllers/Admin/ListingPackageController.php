<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListingPackage;
use Illuminate\Http\Request;

class ListingPackageController extends Controller
{
    public function index()
    {
        $packages = ListingPackage::latest()->get();
        return view('admin.listing_packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.listing_packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:sundul,premium',
            'amount' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'discount_label' => 'nullable|string',
            'is_active' => 'boolean',
            'original_price' => 'nullable|numeric|min:0',
            'button_text' => 'nullable|string',
            'benefits' => 'nullable|string',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        $validated['button_text'] = $request->button_text ?? 'Pilih Paket';

        if ($request->filled('benefits')) {
            $benefits = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $request->benefits))));
            $validated['benefits'] = array_values($benefits);
        } else {
            $validated['benefits'] = null;
        }

        ListingPackage::create($validated);

        return redirect()->route('admin.listing-packages.index')->with('success', 'Paket Promosi berhasil ditambahkan!');
    }

    public function edit(ListingPackage $listingPackage)
    {
        return view('admin.listing_packages.edit', compact('listingPackage'));
    }

    public function update(Request $request, ListingPackage $listingPackage)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:sundul,premium',
            'amount' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'discount_label' => 'nullable|string',
            'is_active' => 'boolean',
            'original_price' => 'nullable|numeric|min:0',
            'button_text' => 'nullable|string',
            'benefits' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['button_text'] = $request->button_text ?? 'Pilih Paket';

        if ($request->filled('benefits')) {
            $benefits = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $request->benefits))));
            $validated['benefits'] = array_values($benefits);
        } else {
            $validated['benefits'] = null;
        }

        $listingPackage->update($validated);

        return redirect()->route('admin.listing-packages.index')->with('success', 'Paket Promosi berhasil diperbarui!');
    }

    public function destroy(ListingPackage $listingPackage)
    {
        $listingPackage->delete();
        return redirect()->route('admin.listing-packages.index')->with('success', 'Paket Promosi berhasil dihapus!');
    }
}
