<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TopupTransaction;
use Illuminate\Http\Request;

class TopupController extends Controller
{
    public function index(Request $request)
    {
        $query = TopupTransaction::with('user')->latest();
        
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        $topups = $query->paginate(20);
        
        return view('admin.topups.index', compact('topups'));
    }

    public function update(Request $request, TopupTransaction $topup)
    {
        $request->validate([
            'status' => 'required|in:pending,success,failed',
            'note' => 'nullable|string'
        ]);

        $topup->update([
            'status' => $request->status,
            'note' => $request->note
        ]);
        
        // Asumsi: jika success, saldo ditambahkan. 
        // Namun, fitur ini biasanya sudah di-handle oleh payment gateway webhook atau service.
        // Jika manual, admin juga bisa memperbarui status.
        
        if ($request->status == 'success' && $topup->wasChanged('status')) {
            // Berikan saldo ke user (unified quota)
            $quota = $topup->user->quota;
            $package = $topup->topupPackage;
            $totalAmount = $topup->amount;
            if ($package && $package->bonus) {
                $totalAmount += $package->bonus;
            }
            
            if ($quota) {
                $quota->increment('listing_quota', $totalAmount);
            } else {
                \App\Models\UserQuota::create([
                    'user_id' => $topup->user_id,
                    'listing_quota' => $totalAmount
                ]);
            }
        }

        return back()->with('success', 'Status Top Up berhasil diperbarui!');
    }

    public function destroy(TopupTransaction $topup)
    {
        $topup->delete();
        return back()->with('success', 'Transaksi top up dihapus!');
    }
}
