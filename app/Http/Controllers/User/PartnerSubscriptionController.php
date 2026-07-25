<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PartnerSubscriptionController extends Controller
{
    public function checkout($id)
    {
        $package = \App\Models\PartnerPackage::findOrFail($id);
        if ($package->is_free) abort(404);

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
            } catch (\Exception $e) { }
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
                
                $ewalletsResponse = \Illuminate\Support\Facades\Http::withBasicAuth($xenditApiKey, '')
                    ->timeout(5)
                    ->get('https://api.xendit.co/ewallets');
                    
                if ($ewalletsResponse->successful()) {
                    $ewallets = $ewalletsResponse->json();
                    if (is_array($ewallets)) {
                        foreach ($ewallets as $ewallet) {
                            if (isset($ewallet['ewallet_type'])) {
                                $pgChannels[] = [
                                    'code' => 'XENDIT_' . strtoupper($ewallet['ewallet_type']),
                                    'name' => strtoupper($ewallet['ewallet_type']) . ' (Xendit)',
                                    'type' => 'E-Wallet',
                                    'logo' => null,
                                    'provider' => 'xendit'
                                ];
                            }
                        }
                    }
                }
            } catch (\Exception $e) { }
        }

        return view('user.partner.billing.checkout', compact('package', 'offlineMethods', 'pgChannels'));
    }

    public function process(Request $request, $id)
    {
        $package = \App\Models\PartnerPackage::findOrFail($id);
        if ($package->is_free) abort(404);

        $request->validate([
            'payment_method' => 'required|string'
        ]);

        $methodParts = explode('|', $request->payment_method);
        $type = $methodParts[0];
        $methodIdOrCode = $methodParts[1] ?? null;

        if ($type == 'offline') {
            $minCode = \App\Models\Setting::where('key', 'offline_unique_code_min')->value('value') ?: 1;
            $maxCode = \App\Models\Setting::where('key', 'offline_unique_code_max')->value('value') ?: 999;
            $uniqueCode = rand((int)$minCode, (int)$maxCode);
            $totalAmount = $package->price + $uniqueCode;

            $transaction = \App\Models\PartnerSubscription::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'partner_package_id' => $package->id,
                'amount' => $totalAmount,
                'status' => 'pending',
                'payment_method' => 'offline',
                'unique_code' => $uniqueCode,
                'starts_at' => now(), // Will be updated on approval
                'ends_at' => now()->addDays($package->duration_days)
            ]);
            return redirect()->route('partner.billing.upload_proof', $transaction->id);
        } else if ($type == 'pg') {
            $provider = str_starts_with($methodIdOrCode, 'XENDIT_') ? 'xendit' : 'tripay';
            $channelCode = str_replace('XENDIT_', '', $methodIdOrCode);

            $transaction = \App\Models\PartnerSubscription::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'partner_package_id' => $package->id,
                'amount' => $package->price,
                'status' => 'pending',
                'payment_method' => $methodIdOrCode,
                'starts_at' => now(),
                'ends_at' => now()->addDays($package->duration_days)
            ]);

            $merchantRef = 'PARTNER-' . $transaction->id . '-' . time();
            $transaction->update(['payment_reference' => $merchantRef]);
            
            $user = \Illuminate\Support\Facades\Auth::user();

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
                            'sku'       => 'PARTNER-' . $package->id,
                            'name'      => 'Paket ' . $package->name,
                            'price'     => $amount,
                            'quantity'  => 1
                        ]
                    ],
                    'return_url'   => route('partner.billing'),
                    'expired_time' => (time() + (24 * 60 * 60)), // 24 hours
                    'signature'    => $signature
                ];

                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . $tripayApiKey
                ])->post($tripayBaseUrl . 'transaction/create', $data);

                if ($response->successful()) {
                    $resData = $response->json();
                    if (isset($resData['success']) && $resData['success'] && isset($resData['data']['checkout_url'])) {
                        $transaction->update(['payment_url' => $resData['data']['checkout_url']]);
                        return redirect($resData['data']['checkout_url']);
                    }
                }
                
                $transaction->update(['status' => 'failed']);
                return redirect()->route('partner.billing')->with('error', 'Gagal membuat transaksi Tripay. Cek log untuk detail.');
                
            } else if ($provider == 'xendit') {
                $xenditApiKey = \App\Models\Setting::where('key', 'xendit_api_key')->value('value');
                
                $data = [
                    'external_id' => $merchantRef,
                    'amount' => $package->price,
                    'payer_email' => $user->email,
                    'description' => 'Paket ' . $package->name,
                    'payment_methods' => [$channelCode],
                    'success_redirect_url' => route('partner.billing'),
                    'failure_redirect_url' => route('partner.billing')
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
                return redirect()->route('partner.billing')->with('error', 'Gagal membuat transaksi Xendit. Cek log untuk detail.');
            }

            return redirect()->back()->with('error', 'Metode pembayaran tidak valid.');
        } else {
            return redirect()->back()->with('error', 'Metode pembayaran tidak valid.');
        }
    }

    public function uploadProof(\App\Models\PartnerSubscription $transaction)
    {
        if ($transaction->user_id != \Illuminate\Support\Facades\Auth::id()) abort(403);
        if ($transaction->status != 'pending') return redirect()->route('partner.billing')->with('error', 'Transaksi sudah diproses.');
        
        return view('user.partner.billing.upload_proof', compact('transaction'));
    }

    public function storeProof(Request $request, \App\Models\PartnerSubscription $transaction)
    {
        if ($transaction->user_id != \Illuminate\Support\Facades\Auth::id()) abort(403);
        if ($transaction->status != 'pending') return redirect()->route('partner.billing');

        $request->validate([
            'payment_proof' => 'required|image|max:2048'
        ]);

        $path = $request->file('payment_proof')->store('public/payments');
        
        $transaction->update([
            'payment_proof' => \Illuminate\Support\Facades\Storage::url($path)
        ]);
        
        $adminEmail = \App\Models\Setting::getValue('admin_notification_email');
        if (!empty($adminEmail)) {
            try {
                $userName = \Illuminate\Support\Facades\Auth::user()->name;
                \Illuminate\Support\Facades\Mail::raw("Ada pembayaran Paket Bulanan Partner baru yang menunggu konfirmasi:\nOleh: {$userName}\nJumlah: Rp " . number_format($transaction->amount, 0, ',', '.'), function ($message) use ($adminEmail) {
                    $message->to($adminEmail)->subject('Notifikasi Pembayaran Paket Partner');
                });
            } catch (\Exception $e) { }
        }

        return redirect()->route('partner.billing')->with('success', 'Bukti transfer berhasil diunggah. Silakan tunggu konfirmasi Admin.');
    }
}
