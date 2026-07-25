<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartnerPackage;
use Illuminate\Http\Request;

class PartnerPackageController extends Controller
{
    public function index()
    {
        $freePackage = PartnerPackage::firstOrCreate(
            ['is_free' => true],
            [
                'name' => 'Paket Dasar (Free)',
                'description' => 'Paket bawaan untuk semua partner baru.',
                'price' => 0,
                'listing_quota' => 10,
                'duration_days' => 30,
            ]
        );
        $packages = PartnerPackage::where('is_free', false)->latest()->get();
        return view('admin.partner_packages.index', compact('packages', 'freePackage'));
    }

    public function updateFree(Request $request)
    {
        $request->validate([
            'listing_quota' => 'required|integer|min:-1',
        ]);

        $freePackage = PartnerPackage::where('is_free', true)->first();
        if ($freePackage) {
            $freePackage->update([
                'listing_quota' => $request->listing_quota
            ]);
        }
        return redirect()->route('admin.partner_packages.index')->with('success', 'Paket Free berhasil diupdate!');
    }

    public function create()
    {
        return view('admin.partner_packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'listing_quota' => 'required|integer|min:-1',
            'duration_days' => 'required|integer|min:1',
            'bonus' => 'nullable|integer|min:0',
            'discount_label' => 'nullable|string|max:50',
            'original_price' => 'nullable|numeric|min:0',
            'is_voucher' => 'nullable|boolean',
            'valid_until' => 'nullable|date',
            'benefits' => 'nullable|string',
            'button_text' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);
        
        $validated['is_free'] = false;
        $validated['is_voucher'] = $request->has('is_voucher');
        $validated['is_active'] = $request->has('is_active');
        
        // Handle benefits: Convert string (separated by newline) to array
        if (!empty($validated['benefits'])) {
            // Split by newline and remove empty lines/whitespace
            $benefitsArray = array_filter(array_map('trim', explode("\n", $validated['benefits'])));
            $validated['benefits'] = array_values($benefitsArray); // reset keys
        } else {
            $validated['benefits'] = null;
        }
        
        // Set default button text if empty
        if (empty($validated['button_text'])) {
            $validated['button_text'] = 'Beli Paket Ini';
        }

        PartnerPackage::create($validated);

        return redirect()->route('admin.partner_packages.index')->with('success', 'Paket Bulanan Partner berhasil ditambahkan!');
    }

    public function edit(PartnerPackage $partnerPackage)
    {
        if ($partnerPackage->is_free) {
            return redirect()->route('admin.partner_packages.index');
        }
        return view('admin.partner_packages.edit', compact('partnerPackage'));
    }

    public function update(Request $request, PartnerPackage $partnerPackage)
    {
        if ($partnerPackage->is_free) {
            return redirect()->route('admin.partner_packages.index');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'listing_quota' => 'required|integer|min:-1',
            'duration_days' => 'required|integer|min:1',
            'bonus' => 'nullable|integer|min:0',
            'discount_label' => 'nullable|string|max:50',
            'original_price' => 'nullable|numeric|min:0',
            'is_voucher' => 'nullable|boolean',
            'valid_until' => 'nullable|date',
            'benefits' => 'nullable|string',
            'button_text' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_voucher'] = $request->has('is_voucher');
        $validated['is_active'] = $request->has('is_active');
        
        // Handle benefits: Convert string (separated by newline) to array
        if (!empty($validated['benefits'])) {
            // Split by newline and remove empty lines/whitespace
            $benefitsArray = array_filter(array_map('trim', explode("\n", $validated['benefits'])));
            $validated['benefits'] = array_values($benefitsArray); // reset keys
        } else {
            $validated['benefits'] = null;
        }
        
        // Set default button text if empty
        if (empty($validated['button_text'])) {
            $validated['button_text'] = 'Beli Paket Ini';
        }

        $partnerPackage->update($validated);

        return redirect()->route('admin.partner_packages.index')->with('success', 'Paket Bulanan Partner berhasil diperbarui!');
    }

    public function destroy(PartnerPackage $partnerPackage)
    {
        if ($partnerPackage->is_free) {
            return redirect()->route('admin.partner_packages.index')->with('error', 'Paket Free tidak dapat dihapus!');
        }
        $partnerPackage->delete();
        return redirect()->route('admin.partner_packages.index')->with('success', 'Paket Bulanan Partner berhasil dihapus!');
    }
}
