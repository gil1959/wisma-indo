<?php

namespace App\Http\Controllers\Api\V1\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PartnerPackage;
use App\Models\PartnerSubscription;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function packages()
    {
        $packages = PartnerPackage::where('is_active', true)->orderBy('price', 'asc')->get();
        return response()->json([
            'success' => true,
            'data' => $packages
        ]);
    }

    public function checkout(Request $request, $package_id)
    {
        $request->validate([
            'payment_method' => 'required|in:online,offline',
            'payment_channel' => 'required_if:payment_method,offline',
        ]);

        $package = PartnerPackage::findOrFail($package_id);
        $partner = $request->user();

        $subscription = new PartnerSubscription();
        $subscription->user_id = $partner->id;
        $subscription->partner_package_id = $package->id;
        $subscription->transaction_number = 'SUB-' . time() . '-' . Str::random(5);
        $subscription->amount = $package->price;
        $subscription->duration_days = $package->duration_days;
        
        $subscription->payment_method = $request->payment_method;
        if ($request->payment_method == 'offline') {
            $subscription->payment_channel = $request->payment_channel;
            $subscription->status = 'waiting_payment';
        } else {
            $subscription->status = 'pending';
        }

        $subscription->save();

        return response()->json([
            'success' => true,
            'message' => 'Subscription checkout successful',
            'data' => $subscription
        ], 201);
    }

    public function uploadProof(Request $request, $transaction_id)
    {
        $request->validate([
            'proof_of_payment' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $subscription = PartnerSubscription::where('user_id', $request->user()->id)
            ->where('status', 'waiting_payment')
            ->findOrFail($transaction_id);

        $path = $request->file('proof_of_payment')->store('subscription_proofs', 'public');
        
        $subscription->proof_of_payment = $path;
        $subscription->status = 'waiting_verification';
        $subscription->save();

        return response()->json([
            'success' => true,
            'message' => 'Proof of payment uploaded successfully',
            'data' => $subscription
        ]);
    }

    public function history(Request $request)
    {
        $partner = $request->user();
        $history = PartnerSubscription::with('package')
            ->where('user_id', $partner->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return response()->json([
            'success' => true,
            'data' => $history->items(),
            'meta' => [
                'current_page' => $history->currentPage(),
                'last_page' => $history->lastPage(),
                'total' => $history->total(),
            ]
        ]);
    }
}
