<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ListingTransaction;
use App\Models\Listing;

class ListingPromotionController extends Controller
{
    public function index(Request $request)
    {
        $query = ListingTransaction::with(['user', 'listing', 'listingPackage'])->latest();
        
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        $transactions = $query->paginate(20);
        
        return view('admin.listing_promotions.index', compact('transactions'));
    }

    public function update(Request $request, ListingTransaction $listing_promotion)
    {
        $transaction = $listing_promotion; // renaming variable
        $request->validate([
            'status' => 'required|in:pending,success,failed',
            'note' => 'nullable|string'
        ]);

        $transaction->update([
            'status' => $request->status,
            'note' => $request->note
        ]);
        
        if ($request->status == 'success' && $transaction->wasChanged('status')) {
            // Apply the package to the listing
            $listing = $transaction->listing;
            $package = $transaction->listingPackage;
            
            if ($listing && $package) {
                if ($package->type == 'premium') {
                    $listing->update(['is_premium' => true]);
                } else {
                    $listing->increment('bump_count', $package->amount);
                    $listing->update(['bumped_at' => now()]);
                }
            }
        }

        return back()->with('success', 'Status Transaksi Promosi Iklan berhasil diperbarui!');
    }

    public function destroy(ListingTransaction $listing_promotion)
    {
        $listing_promotion->delete();
        return back()->with('success', 'Transaksi Promosi Iklan dihapus!');
    }
}
