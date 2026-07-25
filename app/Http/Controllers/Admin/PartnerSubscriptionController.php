<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartnerSubscription;
use Illuminate\Http\Request;

class PartnerSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = PartnerSubscription::with(['user', 'package'])->latest();
        
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        $subscriptions = $query->paginate(20);
        
        return view('admin.partner_subscriptions.index', compact('subscriptions'));
    }

    public function update(Request $request, PartnerSubscription $partner_subscription)
    {
        $request->validate([
            'status' => 'required|in:pending,active,expired,rejected',
            'rejection_note' => 'nullable|string'
        ]);

        $oldStatus = $partner_subscription->status;
        $partner_subscription->update([
            'status' => $request->status,
            'rejection_note' => $request->rejection_note
        ]);
        
        if ($request->status == 'active' && $oldStatus != 'active') {
            $package = \App\Models\PartnerPackage::find($partner_subscription->partner_package_id);
            if ($package) {
                $partner_subscription->update([
                    'starts_at' => now(),
                    'ends_at' => now()->addDays($package->duration_days)
                ]);
                
                // Set user listing quota
                $user = $partner_subscription->user;
                $userQuota = \App\Models\UserQuota::firstOrCreate(['user_id' => $user->id]);
                if ($package->listing_quota != -1) {
                    $userQuota->listing_quota += $package->listing_quota;
                } else {
                    $userQuota->listing_quota = -1;
                }
                $userQuota->save();
            }
        }

        return back()->with('success', 'Status Langganan Partner berhasil diperbarui!');
    }

    public function destroy(PartnerSubscription $partner_subscription)
    {
        $partner_subscription->delete();
        return back()->with('success', 'Transaksi langganan dihapus!');
    }
}
