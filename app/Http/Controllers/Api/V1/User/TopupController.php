<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TopupPackage;
use App\Models\TopupTransaction;
use App\Models\OfflinePaymentMethod;
use App\Http\Resources\TopupTransactionResource;
use Illuminate\Support\Str;

class TopupController extends Controller
{
    public function packages()
    {
        $packages = TopupPackage::where('is_active', true)->orderBy('price', 'asc')->get();
        $offlineMethods = OfflinePaymentMethod::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'packages' => $packages,
                'offline_payment_methods' => $offlineMethods,
            ]
        ]);
    }

    public function checkout(Request $request, $package_id)
    {
        $request->validate([
            'payment_method' => 'required|in:online,offline',
            'payment_channel' => 'required_if:payment_method,offline',
        ]);

        $package = TopupPackage::findOrFail($package_id);
        $user = $request->user();

        $transaction = new TopupTransaction();
        $transaction->user_id = $user->id;
        $transaction->topup_package_id = $package->id;
        $transaction->invoice_number = 'INV-TP-' . time() . '-' . Str::random(5);
        $transaction->amount = $package->price;
        $transaction->quota_amount = $package->quota_amount;
        $transaction->payment_method = $request->payment_method;
        
        if ($request->payment_method == 'offline') {
            $transaction->payment_channel = $request->payment_channel;
            // Generate unique code for offline transfer
            $transaction->unique_code = rand(1, 999);
            $transaction->status = 'waiting_payment';
        } else {
            $transaction->status = 'pending';
            // Integrate with payment gateway logic here
        }

        $transaction->save();

        return response()->json([
            'success' => true,
            'message' => 'Checkout successful',
            'data' => new TopupTransactionResource($transaction)
        ], 201);
    }

    public function uploadProof(Request $request, $transaction_id)
    {
        $request->validate([
            'proof_of_payment' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $transaction = TopupTransaction::where('user_id', $request->user()->id)
            ->where('status', 'waiting_payment')
            ->findOrFail($transaction_id);

        $path = $request->file('proof_of_payment')->store('payment_proofs', 'public');
        
        $transaction->proof_of_payment = $path;
        $transaction->status = 'waiting_verification';
        $transaction->save();

        return response()->json([
            'success' => true,
            'message' => 'Proof of payment uploaded successfully',
            'data' => new TopupTransactionResource($transaction)
        ]);
    }

    public function transactions(Request $request)
    {
        $transactions = TopupTransaction::with('package')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return TopupTransactionResource::collection($transactions)->additional([
            'success' => true
        ]);
    }
}
