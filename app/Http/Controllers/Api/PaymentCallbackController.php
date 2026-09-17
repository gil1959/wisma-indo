<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentCallbackController extends Controller
{
    private function processTransactionSuccess($merchantRef)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($merchantRef) {
        $current = $this->getTransaction($merchantRef);
        if (!$current || $current->payment_method === 'offline') return false;
        \App\Models\User::whereKey($current->user_id)->lockForUpdate()->firstOrFail();
        if (str_starts_with($merchantRef, 'PARTNER-')) {
            $transaction = \App\Models\PartnerSubscription::where('payment_reference', $merchantRef)->lockForUpdate()->first();
            if ($transaction && $transaction->status !== 'success' && $transaction->status !== 'active') {
                $transaction->update(['status' => 'active']);
                $package = $transaction->package;
                $transaction->update([
                    'starts_at' => now(),
                    'ends_at' => now()->addDays($package->duration_days)
                ]);
                
                $user = $transaction->user;
                $userQuota = \App\Models\UserQuota::firstOrCreate(['user_id' => $user->id]);
                if ($package->listing_quota != -1 && $userQuota->listing_quota != -1) {
                    $userQuota->listing_quota += $package->listing_quota;
                } else {
                    $userQuota->listing_quota = -1;
                }
                $userQuota->save();
                return true;
            }
        } else if (str_starts_with($merchantRef, 'PROMO-')) {
            $transaction = \App\Models\ListingTransaction::where('payment_reference', $merchantRef)->lockForUpdate()->first();
            if ($transaction && $transaction->status !== 'success') {
                $transaction->update(['status' => 'success']);
                
                $listing = $transaction->listing;
                $package = $transaction->listingPackage;
                
                if ($listing && $package) {
                    if ($package->type == 'premium') {
                        $listing->update(['is_premium' => true]);
                    } else {
                        $listing->increment('bump_count', $package->amount ?? 1);
                        $listing->update(['bumped_at' => now()]);
                    }
                }
                return true;
            }
        } else {
            $transaction = \App\Models\TopupTransaction::where('payment_reference', $merchantRef)->lockForUpdate()->first();
            if ($transaction && $transaction->status !== 'success') {
                $transaction->update(['status' => 'success']);
                
                $user = $transaction->user;
                $package = $transaction->topupPackage;
                
                $totalBonus = $package->bonus ?? 0;
                $quota = $user->quota;
                if ($quota) {
                    if ($quota->listing_quota != -1) $quota->increment('listing_quota', $package->amount + $totalBonus);
                } else {
                    \App\Models\UserQuota::create([
                        'user_id' => $user->id,
                        'listing_quota' => $package->amount + $totalBonus
                    ]);
                }
                return true;
            }
        }
        return false;
        }, 3);
    }

    private function processTransactionFailed($merchantRef)
    {
        if (str_starts_with($merchantRef, 'PARTNER-')) {
            \App\Models\PartnerSubscription::where('payment_reference', $merchantRef)->whereNotIn('status', ['success', 'active'])->update(['status' => 'failed']);
        } else if (str_starts_with($merchantRef, 'PROMO-')) {
            \App\Models\ListingTransaction::where('payment_reference', $merchantRef)->whereNotIn('status', ['success', 'active'])->update(['status' => 'failed']);
        } else {
            \App\Models\TopupTransaction::where('payment_reference', $merchantRef)->whereNotIn('status', ['success', 'active'])->update(['status' => 'failed']);
        }
    }

    private function getTransaction($merchantRef)
    {
        if (str_starts_with($merchantRef, 'PARTNER-')) {
            return \App\Models\PartnerSubscription::where('payment_reference', $merchantRef)->first();
        } else if (str_starts_with($merchantRef, 'PROMO-')) {
            return \App\Models\ListingTransaction::where('payment_reference', $merchantRef)->first();
        } else {
            return \App\Models\TopupTransaction::where('payment_reference', $merchantRef)->first();
        }
    }

    public function tripayCallback(Request $request)
    {
        $callbackSignature = $request->server('HTTP_X_CALLBACK_SIGNATURE');
        $json = $request->getContent();
        
        $tripayPrivateKey = \App\Models\Setting::where('key', 'tripay_private_key')->value('value');
        if (!$tripayPrivateKey) return response()->json(['success' => false], 503);
        $signature = hash_hmac('sha256', $json, $tripayPrivateKey);

        if (!hash_equals($signature, (string) $callbackSignature)) {
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
        if (JSON_ERROR_NONE !== json_last_error() || !is_object($data) || !isset($data->merchant_ref, $data->status)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data',
            ], 400);
        }

        $transaction = $this->getTransaction($data->merchant_ref);
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        if ($transaction->status === 'success' || $transaction->status === 'active') {
            return response()->json([
                'success' => true,
                'message' => 'Transaction already processed',
            ]);
        }

        if ($data->status === 'PAID' || $data->status === 'SETTLED') {
            $this->processTransactionSuccess($data->merchant_ref);
        } else if ($data->status === 'EXPIRED' || $data->status === 'FAILED') {
            $this->processTransactionFailed($data->merchant_ref);
        }

        return response()->json(['success' => true]);
    }

    public function xenditCallback(Request $request)
    {
        $data = $request->all();
        $callbackToken = $request->server('HTTP_X_CALLBACK_TOKEN');
        
        $xenditToken = \App\Models\Setting::where('key', 'xendit_callback_token')->value('value');
        
        // Verifikasi token jika dikonfigurasi di pengaturan
        if (empty($xenditToken) || !hash_equals((string) $xenditToken, (string) $callbackToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Callback Token',
            ], 403);
        }
        if (!isset($data['external_id'], $data['status'], $data['id']) || !is_string($data['id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data',
            ], 400);
        }

        $transaction = $this->getTransaction($data['external_id']);
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        if ($transaction->status === 'success' || $transaction->status === 'active') {
            return response()->json([
                'success' => true,
                'message' => 'Transaction already processed',
            ]);
        }

        if ($data['status'] === 'PAID' || $data['status'] === 'SETTLED') {
            // Verify via Xendit API to prevent spoofing
            $xenditApiKey = \App\Models\Setting::where('key', 'xendit_api_key')->value('value');
            $response = \Illuminate\Support\Facades\Http::withBasicAuth($xenditApiKey, '')
                    ->timeout(20)->get('https://api.xendit.co/v2/invoices/' . rawurlencode($data['id']));
                    
            if ($response->successful()) {
                $invoice = $response->json();
                $expectedAmount = $transaction instanceof \App\Models\TopupTransaction ? $transaction->total_amount : $transaction->amount;
                if (($invoice['external_id'] ?? null) !== $data['external_id'] || (float) ($invoice['amount'] ?? -1) !== (float) $expectedAmount) return response()->json(['success' => false], 422);
                if (in_array($invoice['status'] ?? '', ['PAID', 'SETTLED'])) {
                    $this->processTransactionSuccess($data['external_id']);
                } else return response()->json(['success' => false], 409);
            } else return response()->json(['success' => false], 502);
        } else if ($data['status'] === 'EXPIRED') {
            $this->processTransactionFailed($data['external_id']);
        }

        return response()->json(['success' => true]);
    }
}
