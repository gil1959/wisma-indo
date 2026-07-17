<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentCallbackController extends Controller
{
    public function tripayCallback(Request $request)
    {
        $callbackSignature = $request->server('HTTP_X_CALLBACK_SIGNATURE');
        $json = $request->getContent();
        
        $tripayPrivateKey = \App\Models\Setting::where('key', 'tripay_private_key')->value('value');
        $signature = hash_hmac('sha256', $json, $tripayPrivateKey);

        if ($signature !== (string) $callbackSignature) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature',
            ], 403);
        }

        if ('payment_status' !== $request->server('HTTP_X_CALLBACK_EVENT')) {
            return response()->json([
                'success' => false,
                'message' => 'Unrecognized event',
            ], 400);
        }

        $data = json_decode($json);
        if (JSON_ERROR_NONE !== json_last_error()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data',
            ], 400);
        }

        $transaction = \App\Models\TopupTransaction::where('payment_reference', $data->merchant_ref)->first();
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        if ($transaction->status === 'success') {
            return response()->json([
                'success' => true,
                'message' => 'Transaction already processed',
            ]);
        }

        if ($data->status === 'PAID' || $data->status === 'SETTLED') {
            // Proses Top Up
            $transaction->update(['status' => 'success']);
            
            $user = $transaction->user;
            $package = $transaction->topupPackage;
            
            $totalBonus = $package->bonus ?? 0;
            $quota = $user->quota;
            if ($quota) {
                $quota->increment('listing_quota', $package->amount + $totalBonus);
            } else {
                \App\Models\UserQuota::create([
                    'user_id' => $user->id,
                    'listing_quota' => $package->amount + $totalBonus
                ]);
            }
        } else if ($data->status === 'EXPIRED' || $data->status === 'FAILED') {
            $transaction->update(['status' => 'failed']);
        }

        return response()->json(['success' => true]);
    }

    public function xenditCallback(Request $request)
    {
        $data = $request->all();
        $callbackToken = $request->server('HTTP_X_CALLBACK_TOKEN');
        
        $xenditToken = \App\Models\Setting::where('key', 'xendit_callback_token')->value('value');
        
        // Verifikasi token jika dikonfigurasi di pengaturan
        if (!empty($xenditToken) && $xenditToken !== $callbackToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Callback Token',
            ], 403);
        }
        if (!isset($data['external_id']) || !isset($data['status'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data',
            ], 400);
        }

        $transaction = \App\Models\TopupTransaction::where('payment_reference', $data['external_id'])->first();
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        if ($transaction->status === 'success') {
            return response()->json([
                'success' => true,
                'message' => 'Transaction already processed',
            ]);
        }

        if ($data['status'] === 'PAID' || $data['status'] === 'SETTLED') {
            // Verify via Xendit API to prevent spoofing
            $xenditApiKey = \App\Models\Setting::where('key', 'xendit_api_key')->value('value');
            $response = \Illuminate\Support\Facades\Http::withBasicAuth($xenditApiKey, '')
                    ->get('https://api.xendit.co/v2/invoices/' . $data['id']);
                    
            if ($response->successful()) {
                $invoice = $response->json();
                if ($invoice['status'] === 'PAID' || $invoice['status'] === 'SETTLED') {
                    $transaction->update(['status' => 'success']);
                    
                    $user = $transaction->user;
                    $package = $transaction->topupPackage;
                    
                    $totalBonus = $package->bonus ?? 0;
                    $quota = $user->quota;
                    if ($quota) {
                        $quota->increment('listing_quota', $package->amount + $totalBonus);
                    } else {
                        \App\Models\UserQuota::create([
                            'user_id' => $user->id,
                            'listing_quota' => $package->amount + $totalBonus
                        ]);
                    }
                }
            }
        } else if ($data['status'] === 'EXPIRED') {
            $transaction->update(['status' => 'failed']);
        }

        return response()->json(['success' => true]);
    }
}
