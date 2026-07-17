<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfflinePaymentMethod;
use Illuminate\Http\Request;

class OfflinePaymentMethodController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'account_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'swift_code' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        OfflinePaymentMethod::create($validated);
        
        return redirect()->back()->with('success', 'Rekening pembayaran berhasil ditambahkan!');
    }

    public function destroy(OfflinePaymentMethod $offlinePaymentMethod)
    {
        $offlinePaymentMethod->delete();
        return redirect()->back()->with('success', 'Rekening pembayaran berhasil dihapus!');
    }
}
