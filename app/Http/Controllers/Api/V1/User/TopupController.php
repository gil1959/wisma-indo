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
        if (!$package->is_active) abort(404);

        $user = $request->user();

        $transaction = new TopupTransaction();
        $transaction->user_id = $user->id;
        $transaction->topup_package_id = $package->id;
        $transaction->amount = $package->amount;
        $transaction->price = $package->price;
        $transaction->payment_method = $request->payment_method == 'offline' ? 'offline' : $request->payment_channel;
        
        if ($request->payment_method == 'offline') {
            $uniqueCode = rand(1, 999);
            $transaction->unique_code = $uniqueCode;
            $transaction->total_amount = $package->price + $uniqueCode;
            $transaction->status = 'pending';
        } else {
            $transaction->total_amount = $package->price;
            $transaction->status = 'pending';
            // Integrate with payment gateway logic here if needed
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
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $transaction = TopupTransaction::where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->findOrFail($transaction_id);

        $path = $request->file('payment_proof')->store('payments', 'public');
        
        $transaction->payment_proof = \Illuminate\Support\Facades\Storage::url($path);
        $transaction->status = 'pending'; // Stays pending until admin approves
        $transaction->save();

        return response()->json([
            'success' => true,
            'message' => 'Proof of payment uploaded successfully',
            'data' => new TopupTransactionResource($transaction)
        ]);
    }

    public function transactions(Request $request)
    {
        $transactions = TopupTransaction::with('topupPackage')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return TopupTransactionResource::collection($transactions)->additional([
            'success' => true
        ]);
    }
}
