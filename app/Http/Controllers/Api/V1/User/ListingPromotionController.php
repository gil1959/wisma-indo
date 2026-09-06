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
                'listing' => $listing,
                'packages' => $packages,
                'offline_payment_methods' => $offlineMethods,
                'pg_channels' => $pgChannels
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
            
            $user = $request->user();

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
                            'sku'       => 'PROMO-' . $package->id,
                            'name'      => 'Promo ' . $package->name . ' - ' . $listing->title,
                            'price'     => $amount,
                            'quantity'  => 1
                        ]
                    ],
                    'return_url'   => url('/api/v1/user/listings'),
                    'expired_time' => (time() + (24 * 60 * 60)),
                    'signature'    => $signature
                ];

                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . $tripayApiKey
                ])->post($tripayBaseUrl . 'transaction/create', $data);

                if ($response->successful()) {
                    $resData = $response->json();
                    if (isset($resData['success']) && $resData['success'] && isset($resData['data']['checkout_url'])) {
                        $transaction->update(['payment_url' => $resData['data']['checkout_url']]);
                        return response()->json([
                            'success' => true,
                            'message' => 'Checkout online initialized.',
                            'data' => $transaction
                        ], 201);
                    }
                } else {
                    \Illuminate\Support\Facades\Log::error('Tripay Create Transaction Error: ' . $response->body());
                }
                
                $transaction->update(['status' => 'failed']);
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
                    'description' => 'Promo ' . $package->name . ' - ' . $listing->title,
                    'payment_methods' => [$channelCode],
                    'success_redirect_url' => url('/api/v1/user/listings'),
                    'failure_redirect_url' => url('/api/v1/user/listings')
                ];

                $response = \Illuminate\Support\Facades\Http::withBasicAuth($xenditApiKey, '')
                    ->post('https://api.xendit.co/v2/invoices', $data);

                if ($response->successful()) {
                    $resData = $response->json();
                    if (isset($resData['invoice_url'])) {
                        $transaction->update(['payment_url' => $resData['invoice_url']]);
                        return response()->json([
                            'success' => true,
                            'message' => 'Checkout online initialized.',
                            'data' => $transaction
                        ], 201);
                    }
                } else {
                    \Illuminate\Support\Facades\Log::error('Xendit Create Invoice Error: ' . $response->body());
                }
                
                $transaction->update(['status' => 'failed']);
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

    public function transactions(Request $request)
    {
        $transactions = ListingTransaction::with(['listing:id,title,slug,cover_image', 'listingPackage:id,name,type'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }
}
