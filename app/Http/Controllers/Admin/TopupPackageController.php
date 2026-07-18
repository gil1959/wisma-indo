<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TopupPackage;
use Illuminate\Http\Request;

class TopupPackageController extends Controller
{
    public function index()
    {
        $packages = TopupPackage::latest()->get();
        return view('admin.topup_packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.topup_packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'bonus' => 'nullable|integer|min:1',
            'discount_label' => 'nullable|string',
            'is_active' => 'boolean',
            'is_voucher' => 'boolean',
            'original_price' => 'nullable|numeric|min:0',
            'valid_until' => 'nullable|date',
            'button_text' => 'nullable|string',
            'benefits' => 'nullable|string',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        $validated['is_voucher'] = $request->has('is_voucher');
        $validated['button_text'] = $request->button_text ?? 'Beli Paket Ini';

        if ($request->filled('benefits')) {
            $benefits = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $request->benefits))));
            $validated['benefits'] = array_values($benefits);
        } else {
            $validated['benefits'] = null;
        }

        TopupPackage::create($validated);

        return redirect()->route('admin.topup-packages.index')->with('success', 'Paket Top Up berhasil ditambahkan!');
    }

    public function edit(TopupPackage $topupPackage)
    {
        return view('admin.topup_packages.edit', compact('topupPackage'));
    }

    public function update(Request $request, TopupPackage $topupPackage)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'bonus' => 'nullable|integer|min:1',
            'discount_label' => 'nullable|string',
            'is_active' => 'boolean',
            'is_voucher' => 'boolean',
            'original_price' => 'nullable|numeric|min:0',
            'valid_until' => 'nullable|date',
            'button_text' => 'nullable|string',
            'benefits' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_voucher'] = $request->has('is_voucher');
        $validated['button_text'] = $request->button_text ?? 'Beli Paket Ini';

        if ($request->filled('benefits')) {
            $benefits = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $request->benefits))));
            $validated['benefits'] = array_values($benefits);
        } else {
            $validated['benefits'] = null;
        }

        $topupPackage->update($validated);

        return redirect()->route('admin.topup-packages.index')->with('success', 'Paket Top Up berhasil diperbarui!');
    }

    public function destroy(TopupPackage $topupPackage)
    {
        $topupPackage->delete();
        return redirect()->route('admin.topup-packages.index')->with('success', 'Paket Top Up berhasil dihapus!');
    }
}
