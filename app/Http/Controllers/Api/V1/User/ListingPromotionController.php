<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Listing;
use App\Models\ListingPackage;
use App\Models\ListingTransaction;
use App\Models\OfflinePaymentMethod;
use Illuminate\Support\Facades\Storage;

class ListingPromotionController extends Controller
{
    public function packages(Request $request, $id)
    {
        $listing = Listing::where('user_id', $request->user()->id)->findOrFail($id);

        $query = ListingPackage::where('is_active', true)->orderBy('price', 'asc');
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        $packages = $query->get();
        $offlineMethods = OfflinePaymentMethod::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'listing' => $listing,
                'packages' => $packages,
                'offline_payment_methods' => $offlineMethods
            ]
        ]);
    }

    public function checkout(Request $request, $id, $package_id)
    {
        $listing = Listing::where('user_id', $request->user()->id)->findOrFail($id);
        $package = ListingPackage::findOrFail($package_id);

        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $methodParts = explode('|', $request->payment_method);
        $type = $methodParts[0];
        $methodIdOrCode = $methodParts[1] ?? null;

        if ($type == 'offline') {
            $transaction = ListingTransaction::create([
                'user_id' => $request->user()->id,
                'listing_id' => $listing->id,
                'listing_package_id' => $package->id,
                'offline_payment_method_id' => $methodIdOrCode,
                'amount' => $package->price,
                'payment_method' => 'offline',
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Checkout offline successful.',
                'data' => $transaction
            ], 201);
        } else if ($type == 'pg') {
            $provider = str_starts_with($methodIdOrCode, 'XENDIT_') ? 'xendit' : 'tripay';
            $channelCode = str_replace('XENDIT_', '', $methodIdOrCode);

            $transaction = ListingTransaction::create([
                'user_id' => $request->user()->id,
                'listing_id' => $listing->id,
                'listing_package_id' => $package->id,
                'amount' => $package->price,
                'payment_method' => $methodIdOrCode,
                'status' => 'pending'
            ]);

            $merchantRef = 'PROMO-' . $transaction->id . '-' . time();
            $transaction->update(['payment_reference' => $merchantRef]);

            return response()->json([
                'success' => true,
                'message' => 'Checkout online initialized.',
                'data' => $transaction
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid payment method.'
        ], 400);
    }

    public function uploadProof(Request $request, $transaction_id)
    {
        $transaction = ListingTransaction::where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->findOrFail($transaction_id);

        $request->validate([
            'payment_proof' => 'required|image|max:10240'
        ]);

        $path = $request->file('payment_proof')->store('payments', 'public');
        
        $transaction->update([
            'payment_proof' => Storage::url($path),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment proof uploaded successfully. Waiting for admin confirmation.',
            'data' => $transaction
        ]);
    }
}
