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
        $allPackages = TopupPackage::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', now());
            })
            ->orderBy('price', 'asc')
            ->get();
        $packages = $allPackages->where('is_voucher', false)->values();
        $voucherPackages = $allPackages->where('is_voucher', true)->values();
        
        $offlineMethods = OfflinePaymentMethod::where('is_active', true)->get();
        $pgChannels = [];

        $tripayActive = \App\Models\Setting::where('key', 'tripay_active')->value('value');
        $tripayApiKey = \App\Models\Setting::where('key', 'tripay_api_key')->value('value');
        $tripayMode = \App\Models\Setting::where('key', 'tripay_mode')->value('value') ?: 'sandbox';
        
        $xenditActive = \App\Models\Setting::where('key', 'xendit_active')->value('value');
        $xenditApiKey = \App\Models\Setting::where('key', 'xendit_api_key')->value('value');
        
        if ($tripayActive == '1' && !empty($tripayApiKey)) {
            $tripayBaseUrl = $tripayMode == 'production' ? 'https://tripay.co.id/api/' : 'https://tripay.co.id/api-sandbox/';
            try {
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . trim($tripayApiKey)
                ])->timeout(5)->get($tripayBaseUrl . 'merchant/payment-channel');
                
                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['data']) && is_array($data['data'])) {
                        foreach ($data['data'] as $channel) {
                            if ($channel['active']) {
                                $pgChannels[] = [
                                    'code' => $channel['code'],
                                    'name' => $channel['name'],
                                    'type' => $channel['type'] ?? 'Tripay',
                                    'logo' => $channel['icon_url'],
                                    'provider' => 'tripay'
                                ];
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Tripay Get Channels Exception: ' . $e->getMessage());
            }
        }
        
        if (empty($pgChannels) && $xenditActive == '1' && !empty($xenditApiKey)) {
            try {
                $vaResponse = \Illuminate\Support\Facades\Http::withBasicAuth($xenditApiKey, '')
                    ->timeout(5)
                    ->get('https://api.xendit.co/available_virtual_account_banks');

                if ($vaResponse->successful()) {
                    $banks = $vaResponse->json();
                    if (is_array($banks)) {
                        foreach ($banks as $bank) {
                            $pgChannels[] = [
                                'code' => 'XENDIT_' . strtoupper($bank['bank_code'] ?? $bank['code'] ?? 'VA'),
                                'name' => ($bank['name'] ?? $bank['bank_code'] ?? 'Virtual Account') . ' (Xendit)',
                                'type' => 'Bank Transfer',
                                'logo' => null,
                                'provider' => 'xendit'
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Xendit Get Channels Exception: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'packages' => $packages,
                'voucher_packages' => $voucherPackages,
                'offline_payment_methods' => $offlineMethods,
                'pg_channels' => $pgChannels
            ]
        ]);
    }

    public function checkout(Request $request, $package_id)
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $package = TopupPackage::findOrFail($package_id);
        if (!$package->is_active) abort(404);

        $user = $request->user();

        $methodParts = explode('|', $request->payment_method);
        $type = $methodParts[0];
        $methodIdOrCode = $methodParts[1] ?? null;

        $transaction = new TopupTransaction();
        $transaction->user_id = $user->id;
        $transaction->topup_package_id = $package->id;
        $transaction->amount = $package->amount;
        $transaction->price = $package->price;
        $transaction->payment_method = $type == 'offline' ? 'offline' : $methodIdOrCode;

        if ($type == 'offline') {
            $uniqueCode = rand(1, 999);
            $transaction->unique_code = $uniqueCode;
            $transaction->total_amount = $package->price + $uniqueCode;
            $transaction->status = 'pending';
            $transaction->save();

            return response()->json([
                'success' => true,
                'message' => 'Checkout offline successful',
                'data' => new TopupTransactionResource($transaction)
            ], 201);
        } else if ($type == 'pg') {
            $provider = str_starts_with($methodIdOrCode, 'XENDIT_') ? 'xendit' : 'tripay';
            $channelCode = str_replace('XENDIT_', '', $methodIdOrCode);

            $transaction->total_amount = $package->price;
            $transaction->status = 'pending';
            $transaction->save();
            
            $merchantRef = 'TOPUP-' . $transaction->id . '-' . time();
            $transaction->payment_reference = $merchantRef;
            $transaction->save();

            if ($provider == 'tripay') {
                $tripayApiKey = trim(\App\Models\Setting::where('key', 'tripay_api_key')->value('value'));
                $tripayPrivateKey = trim(\App\Models\Setting::where('key', 'tripay_private_key')->value('value'));
                $tripayMerchantCode = trim(\App\Models\Setting::where('key', 'tripay_merchant_code')->value('value'));
                $tripayMode = \App\Models\Setting::where('key', 'tripay_mode')->value('value') ?: 'sandbox';
                $tripayBaseUrl = $tripayMode == 'production' ? 'https://tripay.co.id/api/' : 'https://tripay.co.id/api-sandbox/';

                $amount = (int)$package->price;
                $signature = hash_hmac('sha256', $tripayMerchantCode . $merchantRef . $amount, $tripayPrivateKey);

                $data = [
                    'method'         => $channelCode,
                    'merchant_ref'   => $merchantRef,
                    'amount'         => $amount,
                    'customer_name'  => $user->name,
                    'customer_email' => $user->email,
                    'customer_phone' => $user->phone ?? '081234567890',
                    'order_items'    => [
                        [
                            'sku'       => 'TOPUP-' . $package->id,
                            'name'      => 'Topup ' . $package->amount . ' Kuota - ' . $user->name,
                            'price'     => $amount,
                            'quantity'  => 1
                        ]
                    ],
                    'callback_url' => url('/api/webhooks/topup/tripay'),
                    'return_url'   => url('/api/v1/payments/return'),
                    'expired_time' => (time() + (24 * 60 * 60)),
                    'signature'    => $signature
                ];

                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . $tripayApiKey
                ])->post($tripayBaseUrl . 'transaction/create', $data);

                if ($response->successful()) {
                    $resData = $response->json();
                    if (isset($resData['success']) && $resData['success'] && isset($resData['data']['checkout_url'])) {
                        $transaction->payment_url = $resData['data']['checkout_url'];
                        $transaction->save();
                        return response()->json([
                            'success' => true,
                            'message' => 'Checkout online initialized.',
                            'data' => new TopupTransactionResource($transaction)
                        ], 201);
                    }
                } else {
                    \Illuminate\Support\Facades\Log::error('Tripay Create Transaction Error: ' . $response->body());
                }
                
                $transaction->status = 'failed';
                $transaction->save();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat transaksi Tripay. Cek log untuk detail.'
                ], 500);
                
            } else if ($provider == 'xendit') {
                $xenditApiKey = \App\Models\Setting::where('key', 'xendit_api_key')->value('value');
                
                $data = [
                    'external_id' => $merchantRef,
                    'amount' => $package->price,
                    'payer_email' => $user->email,
                    'description' => 'Topup ' . $package->amount . ' Kuota - ' . $user->name,
                    'payment_methods' => [$channelCode],
                    'success_redirect_url' => url('/api/v1/payments/return'),
                    'failure_redirect_url' => url('/api/v1/payments/return')
                ];

                $response = \Illuminate\Support\Facades\Http::withBasicAuth($xenditApiKey, '')
                    ->post('https://api.xendit.co/v2/invoices', $data);

                if ($response->successful()) {
                    $resData = $response->json();
                    if (isset($resData['invoice_url'])) {
                        $transaction->payment_url = $resData['invoice_url'];
                        $transaction->save();
                        return response()->json([
                            'success' => true,
                            'message' => 'Checkout online initialized.',
                            'data' => new TopupTransactionResource($transaction)
                        ], 201);
                    }
                } else {
                    \Illuminate\Support\Facades\Log::error('Xendit Create Invoice Error: ' . $response->body());
                }
                
                $transaction->status = 'failed';
                $transaction->save();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat transaksi Xendit. Cek log untuk detail.'
                ], 500);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid payment provider.'
            ], 400);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid payment method format.'
        ], 400);
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
        $transaction->status = 'pending';
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
