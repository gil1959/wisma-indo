<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TopupTransaction;
use App\Http\Resources\TopupTransactionResource;

class TopupController extends Controller
{
    public function index(Request $request)
    {
        $query = TopupTransaction::with(['user', 'package']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);
        return TopupTransactionResource::collection($transactions)->additional(['success' => true]);
    }

    public function approve(Request $request, $id)
    {
        $transaction = TopupTransaction::findOrFail($id);
        
        if ($transaction->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Transaction cannot be approved'], 400);
        }

        $transaction->status = 'success';
        $transaction->save();

        // Add quota to user
        $user = $transaction->user;
        $package = $transaction->topupPackage ?? $transaction->package;
        
        $totalBonus = $package ? ($package->bonus ?? 0) : 0;
        $quotaAmount = $transaction->amount + $totalBonus;

        $quota = $user->quota;
        if ($quota) {
            $quota->listing_quota += $quotaAmount;
            $quota->save();
        } else {
            \App\Models\UserQuota::create([
                'user_id' => $user->id,
                'listing_quota' => $quotaAmount
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Topup approved successfully'
        ]);
    }

    public function reject(Request $request, $id)
    {
        $transaction = TopupTransaction::findOrFail($id);
        
        if ($transaction->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Transaction cannot be rejected'], 400);
        }

        $transaction->status = 'failed';
        if ($request->has('note')) {
            $transaction->note = $request->note;
        }
        $transaction->save();

        return response()->json([
            'success' => true,
            'message' => 'Topup rejected successfully'
        ]);
    }
}
