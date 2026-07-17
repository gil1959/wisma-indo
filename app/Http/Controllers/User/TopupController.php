<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TopupController extends Controller
{
    public function index()
    {
        $regularPackages = \App\Models\TopupPackage::where('is_active', true)
            ->where('is_voucher', false)
            ->orderBy('amount', 'asc')
            ->get();
            
        $voucherPackages = \App\Models\TopupPackage::where('is_active', true)
            ->where('is_voucher', true)
            ->where(function($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>', now());
            })
            ->orderBy('amount', 'asc')
            ->get();

        return view('front.user.topup.index', compact('regularPackages', 'voucherPackages'));
    }

    public function checkout($id)
    {
        $package = \App\Models\TopupPackage::findOrFail($id);
        if (!$package->is_active) abort(404);
        if ($package->is_voucher && $package->valid_until && \Carbon\Carbon::parse($package->valid_until)->isPast()) {
            return redirect()->route('topup.index')->with('error', 'Mohon maaf, Voucher Promo tersebut sudah tidak berlaku.');
        }

        $offlineMethods = \App\Models\OfflinePaymentMethod::where('is_active', true)->get();
        
        $pgChannels = [];
        
        $tripayActive = \App\Models\Setting::where('key', 'tripay_active')->value('value');
        $tripayApiKey = \App\Models\Setting::where('key', 'tripay_api_key')->value('value');
        $tripayMode = \App\Models\Setting::where('key', 'tripay_mode')->value('value') ?: 'sandbox';
        
        $xenditActive = \App\Models\Setting::where('key', 'xendit_active')->value('value');
        $xenditApiKey = \App\Models\Setting::where('key', 'xendit_api_key')->value('value');
        
        // Try Tripay first if active
        if ($tripayActive == '1' && !empty($tripayApiKey)) {
            $tripayBaseUrl = $tripayMode == 'production' ? 'https://tripay.co.id/api/' : 'https://tripay.co.id/api-sandbox/';
            try {
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . $tripayApiKey
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
                // Tripay failed, will fall back to Xendit if active
            }
        }
        
        // If Tripay failed/empty or not active, try Xendit
        if (empty($pgChannels) && $xenditActive == '1' && !empty($xenditApiKey)) {
            try {
                // Fetch Virtual Accounts
                $vaResponse = \Illuminate\Support\Facades\Http::withBasicAuth($xenditApiKey, '')
                    ->timeout(5)
                    ->get('https://api.xendit.co/available_virtual_account_banks');

                if ($vaResponse->successful()) {
                    $banks = $vaResponse->json();
                    if (is_array($banks)) {
                        foreach ($banks as $bank) {
                            // Xendit usually returns bank_code
                            $pgChannels[] = [
                                'code' => 'XENDIT_' . strtoupper($bank['bank_code'] ?? $bank['code'] ?? 'VA'),
                                'name' => ($bank['name'] ?? $bank['bank_code'] ?? 'Virtual Account') . ' (Xendit)',
                                'type' => 'Bank Transfer',
                                'logo' => null, // Xendit API doesn't return logo URLs natively
                                'provider' => 'xendit'
                            ];
                        }
                    }
                }

                // E-wallets are usually hard to fetch dynamically from Xendit without hitting specific endpoints
                // But we will try to add generic ones if they use the new Payment Methods API
                // Since Xendit doesn't have a reliable 'available_ewallets' endpoint, we rely on the merchant's VA list
                // To avoid empty list if they only have ewallets, we can still add common e-wallets or just rely on VAs
            } catch (\Exception $e) {
                // Error fetching from Xendit
            }
        }

        return view('front.user.topup.checkout', compact('package', 'offlineMethods', 'pgChannels'));
    }

    public function process(Request $request, $id)
    {
        $package = \App\Models\TopupPackage::findOrFail($id);
        if (!$package->is_active) abort(404);
        if ($package->is_voucher && $package->valid_until && \Carbon\Carbon::parse($package->valid_until)->isPast()) {
            return redirect()->route('topup.index')->with('error', 'Mohon maaf, Voucher Promo tersebut sudah tidak berlaku.');
        }

        $request->validate([
            'payment_method' => 'required|string',
        ]);
        
        $methodParts = explode('|', $request->payment_method);
        $type = $methodParts[0];
        $methodIdOrCode = $methodParts[1] ?? null;

        if ($type == 'offline') {
            $minCode = \App\Models\Setting::where('key', 'offline_unique_code_min')->value('value') ?: 1;
            $maxCode = \App\Models\Setting::where('key', 'offline_unique_code_max')->value('value') ?: 999;
            $uniqueCode = rand((int)$minCode, (int)$maxCode);
            $totalAmount = $package->price + $uniqueCode;

            $transaction = \App\Models\TopupTransaction::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'topup_package_id' => $package->id,
                'amount' => $package->amount,
                'price' => $package->price,
                'unique_code' => $uniqueCode,
                'total_amount' => $totalAmount,
                'payment_method' => 'offline',
                'status' => 'pending'
            ]);
            return redirect()->route('topup.upload_proof', $transaction->id);
        } else if ($type == 'pg') {
            $provider = str_starts_with($methodIdOrCode, 'XENDIT_') ? 'xendit' : 'tripay';
            $channelCode = str_replace('XENDIT_', '', $methodIdOrCode);

            $transaction = \App\Models\TopupTransaction::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'topup_package_id' => $package->id,
                'amount' => $package->amount,
                'price' => $package->price,
                'payment_method' => $methodIdOrCode,
                'status' => 'pending'
            ]);

            $merchantRef = 'TU-' . $transaction->id . '-' . time();
            $transaction->update(['payment_reference' => $merchantRef]);
            
            $user = \Illuminate\Support\Facades\Auth::user();

            if ($provider == 'tripay') {
                $tripayApiKey = \App\Models\Setting::where('key', 'tripay_api_key')->value('value');
                $tripayPrivateKey = \App\Models\Setting::where('key', 'tripay_private_key')->value('value');
                $tripayMerchantCode = \App\Models\Setting::where('key', 'tripay_merchant_code')->value('value');
                $tripayMode = \App\Models\Setting::where('key', 'tripay_mode')->value('value') ?: 'sandbox';
                $tripayBaseUrl = $tripayMode == 'production' ? 'https://tripay.co.id/api/' : 'https://tripay.co.id/api-sandbox/';

                $amount = $package->price;
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
                            'name'      => 'Top Up ' . $package->amount . ' Listing',
                            'price'     => $amount,
                            'quantity'  => 1
                        ]
                    ],
                    'return_url'   => route('transaksi'),
                    'expired_time' => (time() + (24 * 60 * 60)), // 24 hours
                    'signature'    => $signature
                ];

                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . $tripayApiKey
                ])->post($tripayBaseUrl . 'transaction/create', $data);

                if ($response->successful()) {
                    $resData = $response->json();
                    if ($resData['success'] && isset($resData['data']['checkout_url'])) {
                        $transaction->update(['payment_url' => $resData['data']['checkout_url']]);
                        return redirect($resData['data']['checkout_url']);
                    }
                }
                
                $transaction->update(['status' => 'failed']);
                return redirect()->route('transaksi')->with('error', 'Gagal membuat transaksi Tripay.');
                
            } else if ($provider == 'xendit') {
                $xenditApiKey = \App\Models\Setting::where('key', 'xendit_api_key')->value('value');
                
                $data = [
                    'external_id' => $merchantRef,
                    'amount' => $package->price,
                    'payer_email' => $user->email,
                    'description' => 'Top Up ' . $package->amount . ' Listing',
                    'payment_methods' => [$channelCode],
                    'success_redirect_url' => route('transaksi'),
                    'failure_redirect_url' => route('transaksi')
                ];

                $response = \Illuminate\Support\Facades\Http::withBasicAuth($xenditApiKey, '')
                    ->post('https://api.xendit.co/v2/invoices', $data);

                if ($response->successful()) {
                    $resData = $response->json();
                    if (isset($resData['invoice_url'])) {
                        $transaction->update(['payment_url' => $resData['invoice_url']]);
                        return redirect($resData['invoice_url']);
                    }
                }
                
                $transaction->update(['status' => 'failed']);
                return redirect()->route('transaksi')->with('error', 'Gagal membuat transaksi Xendit.');
            }

            return redirect()->back()->with('error', 'Metode pembayaran tidak valid.');
        } else {
            return redirect()->back()->with('error', 'Metode pembayaran tidak valid.');
        }
    }

    public function uploadProof(\App\Models\TopupTransaction $transaction)
    {
        if ($transaction->user_id != \Illuminate\Support\Facades\Auth::id()) abort(403);
        if ($transaction->status != 'pending') return redirect()->route('transaksi')->with('error', 'Transaksi sudah diproses.');
        
        return view('front.user.topup.upload_proof', compact('transaction'));
    }

    public function storeProof(Request $request, \App\Models\TopupTransaction $transaction)
    {
        if ($transaction->user_id != \Illuminate\Support\Facades\Auth::id()) abort(403);
        if ($transaction->status != 'pending') return redirect()->route('transaksi');

        $request->validate([
            'payment_proof' => 'required|image|max:2048'
        ]);

        $path = $request->file('payment_proof')->store('public/payments');
        
        $transaction->update([
            'payment_proof' => \Illuminate\Support\Facades\Storage::url($path)
        ]);

        return redirect()->route('transaksi')->with('success', 'Bukti transfer berhasil diunggah. Silakan tunggu konfirmasi Admin.');
    }
}
