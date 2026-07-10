<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\PaymentMethod;
use App\Services\Payments\TripayService;
use App\Services\Payments\DokuService;
use App\Services\Payments\MidtransService;
use App\Services\Payments\PayPalService;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentProofSubmittedMail;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    // Router lama biar gak ngerusak link existing
    
    public function show(Request $request, Order $order, TripayService $tripay)
{
    // ✅ Sync status Tripay kalau user balik dari gateway tapi webhook belum update
    $gatewayName = null;

    if ($order->payment_method && str_starts_with($order->payment_method, 'gateway:')) {
        $parts = explode(':', $order->payment_method, 3);
        $gatewayName = $parts[1] ?? null;
    }

    // ✅ Sync status XENDIT kalau user balik dari gateway tapi webhook belum update
if ($gatewayName === 'xendit' && $order->payment_status !== 'paid') {

    $channelCode = null;
    if ($order->payment_method && str_starts_with($order->payment_method, 'gateway:')) {
        $parts = explode(':', $order->payment_method, 3);
        $channelCode = $parts[2] ?? null;
    }

    $paymentQuery = $order->payments()
        ->where('method', 'gateway')
        ->where('gateway_name', 'xendit')
        ->where('status', 'waiting_payment');

    if (!empty($channelCode)) {
        $paymentQuery->where('channel_code', $channelCode);
    }

    $payment = $paymentQuery->latest()->first();

    if ($payment && !empty($payment->gateway_reference)) {
        $gw = PaymentGateway::where('name', 'xendit')->first();
        $cred = $gw?->credentials ?? [];

        try {
            // Xendit invoice status endpoint
            $http = Http::withBasicAuth($cred['secret_key'] ?? '', '')
                ->get('https://api.xendit.co/v2/invoices/' . $payment->gateway_reference);

            if ($http->ok()) {
                $detail = $http->json();
                $status = strtoupper((string) data_get($detail, 'status', ''));

                if (in_array($status, ['PAID', 'SETTLED'], true)) {
                    $payment->status = 'paid';
                    $payment->gateway_payload = $detail;
                    $payment->save();

                    $order->payment_status = 'paid';
                    $order->order_status = 'approved';
                    $order->save();
                } elseif (in_array($status, ['EXPIRED'], true)) {
                    $payment->status = 'failed';
                    $payment->gateway_payload = $detail;
                    $payment->save();

                    $order->payment_status = 'failed';
                    $order->order_status = 'rejected';
                    $order->save();
                } else {
                    // simpan payload terakhir (optional)
                    $payment->gateway_payload = $detail;
                    $payment->save();
                }
            } else {
                Log::warning('Xendit sync failed http', [
                    'invoice' => $order->invoice_number,
                    'ref' => $payment->gateway_reference,
                    'status' => $http->status(),
                    'body' => $http->body(),
                ]);
            }

        } catch (\Throwable $e) {
            Log::warning('Xendit sync on return_url failed', [
                'invoice' => $order->invoice_number,
                'ref' => $payment->gateway_reference,
                'err' => $e->getMessage(),
            ]);
        }
    }
}


    if ($gatewayName === 'tripay' && $order->payment_status !== 'paid') {
       $channelCode = null;
if ($order->payment_method && str_starts_with($order->payment_method, 'gateway:')) {
    $parts = explode(':', $order->payment_method, 3);
    $channelCode = $parts[2] ?? null; // contoh: QRIS
}

$paymentQuery = $order->payments()
    ->where('method', 'gateway')
    ->where('gateway_name', 'tripay')
    ->where('status', 'waiting_payment');

if (!empty($channelCode)) {
    $paymentQuery->where('channel_code', $channelCode);
}

$payment = $paymentQuery->latest()->first();


        if ($payment && !empty($payment->gateway_reference)) {
            $gw = PaymentGateway::where('name', 'tripay')->first();
            $cred = $gw?->credentials ?? [];

            try {
                $detail = $tripay->getTransactionDetail($cred, $payment->gateway_reference);

                $status = strtoupper((string) data_get($detail, 'data.status', ''));

                if (in_array($status, ['PAID', 'SUCCESS'], true)) {
                    $payment->status = 'paid';
                    $payment->gateway_payload = $detail;
                    $payment->save();

                    $order->payment_status = 'paid';
                    $order->order_status = 'approved';
                    $order->save();
                } elseif (in_array($status, ['FAILED', 'EXPIRED'], true)) {
                    $payment->status = 'failed';
                    $payment->gateway_payload = $detail;
                    $payment->save();

                    $order->payment_status = 'failed';
                    $order->order_status = 'rejected';
                    $order->save();
                }
            } catch (\Throwable $e) {
                Log::warning('Tripay sync on return_url failed', [
                    'invoice' => $order->invoice_number,
                    'ref' => $payment->gateway_reference,
                    'err' => $e->getMessage(),
                ]);
            }
        }
    }

if (
    $order->payment_status === 'paid'
    && $order->payment_method
    && str_starts_with($order->payment_method, 'gateway:')
) {
    if (!auth()->check()) {
        // WAJIB aman: validasi token return_url (token ini lu bikin & simpan di payments)
        $token = (string) $request->query('token');

        $valid = false;
        if ($token !== '') {
            $valid = $order->payments()
                ->where('method', 'gateway')
                ->where('return_token', $token)
                ->exists();
        }

        if (!$valid) {
            return redirect()
                ->route('login')
                ->with('error', 'Silakan login untuk melihat order kamu.');
        }

        // token valid → boleh auto login + create account
        $this->autoLoginForPaidOrder($order);
    }

    return redirect()
        ->route('user.orders.show', $order)
        ->with('success', 'Pembayaran berhasil. Order kamu sudah tercatat.');
}


    // === FLOW LAMA (tetap) ===
    if (!$order->payment_method) {
        return redirect()->route('checkout.show', $order);
    }

    if (str_starts_with($order->payment_method, 'manual:')) {
        return redirect()->route('payment.manual.page', $order);
    }

    if (str_starts_with($order->payment_method, 'gateway:')) {
        return redirect()->route('payment.gateway.page', $order);
    }

    abort(400, 'Metode pembayaran tidak dikenal.');
}



    // ===== PAGE MANUAL =====
    public function manualPage(Order $order)
    {
        if (strpos($order->payment_method, 'manual:') !== 0) {
            abort(403);
        }

        [, $id] = explode(':', $order->payment_method, 2);

        $manualMethod = PaymentMethod::where('id', (int)$id)
            ->where('type', 'manual')
            ->where('is_active', 1)
            ->firstOrFail();

        return view('front.payment.manual', compact('order', 'manualMethod'));
    }

    // ===== PAGE GATEWAY =====
    public function gatewayPage(Order $order)
    {
        if (strpos($order->payment_method, 'gateway:') !== 0) {
            abort(403);
        }

        $parts = explode(':', $order->payment_method, 3);
        if (count($parts) !== 3) abort(400, 'Format gateway invalid');

        [, $gatewayName, $channelCode] = $parts;

        $gateway = PaymentGateway::where('name', $gatewayName)
            ->where('is_active', 1)
            ->firstOrFail();

        $gatewayMethodLabel = $channelCode;
        $channels = is_array($gateway->channels) ? $gateway->channels : [];
        $found = false;

        foreach ($channels as $ch) {
            if (($ch['channel_code'] ?? null) === $channelCode) {
                $gatewayMethodLabel = $ch['name'] ?? $channelCode;
                $found = true;
                break;
            }
        }

        if (!$found) abort(400, 'Channel gateway tidak valid.');

        return view('front.payment.gateway', compact('order', 'gateway', 'channelCode', 'gatewayMethodLabel'));
    }

    // SUBMIT MANUAL
    public function submitManual(Request $request, Order $order)
    {
        if (strpos($order->payment_method, 'manual:') !== 0) {
            abort(403);
        }

        $request->validate([
            'proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($order->payments()->whereIn('status', ['waiting_verification', 'paid'])->exists()) {
            return redirect()
                ->route('payment.waiting', $order)
                ->with('error', 'Pembayaran sudah dikirim. Tunggu verifikasi admin.');
        }

        $path = $request->file('proof')->store('payments', 'public');

        $order->payments()->create([
            'method'            => 'manual',
            'amount'            => $order->final_price,
            'proof_image'       => $path,
            'gateway_reference' => null,
            'status'            => 'waiting_verification',
        ]);

        $order->update([
    'payment_status'   => 'paid',
    'approved_at'      => now(),
    'approved_by'      => 'gateway',
]);


        // ✅ EMAIL: proof uploaded (admin + buyer)
        try {
            // ke buyer
            if (!empty($order->customer_email)) {
                Mail::to($order->customer_email)->send(new PaymentProofSubmittedMail($order, false));
            }

            // ke admin
            $adminEmail = Setting::invoiceAdminEmail();
            if (!empty($adminEmail)) {
                Mail::to($adminEmail)->send(new PaymentProofSubmittedMail($order, true));
            }
        } catch (\Throwable $e) {
            Log::warning('Email bukti pembayaran gagal dikirim', [
                'invoice' => $order->invoice_number,
                'err' => $e->getMessage(),
            ]);
        }

        // Pastikan user login untuk akses halaman order
if (!auth()->check()) {
    // harusnya sudah login dari checkout auto-create
    return redirect()->route('login')->with('error', 'Silakan login untuk melihat order kamu.');
}

return redirect()
    ->route('user.orders.show', $order)
    ->with('success', 'Bukti transfer berhasil diupload. Silakan konfirmasi ke admin.');

    }

    public function waiting(Order $order)
    {
        $rawWa = (string) Setting::where('key', 'footer_whatsapp')->value('value');
        $wa = preg_replace('/\D+/', '', $rawWa);

        if (str_starts_with($wa, '0')) {
            $wa = '62' . substr($wa, 1);
        }

        return view('front.payment.waiting', compact('order', 'wa'));
    }

    // ✅ START GATEWAY (beneran call API + redirect)
    public function startGateway(
        Request $request,
        Order $order,
        PayPalService $paypal,
        TripayService $tripay,
        DokuService $doku,
        MidtransService $midtrans
    ) {
        if (strpos($order->payment_method, 'gateway:') !== 0) {
            abort(403);
        }

        $parts = explode(':', $order->payment_method, 3);
        if (count($parts) !== 3) abort(400, 'Format gateway invalid');

        [, $gatewayName, $channelCode] = $parts;

        if (!in_array($gatewayName, ['doku', 'tripay', 'midtrans', 'xendit', 'ipaymu', 'paypal'], true)) {
            abort(400, 'Gateway tidak didukung');
        }

        $gateway = PaymentGateway::where('name', $gatewayName)
            ->where('is_active', 1)
            ->firstOrFail();

        // Anti-tamper channel
        $channels = is_array($gateway->channels) ? $gateway->channels : [];
        $valid = false;
        foreach ($channels as $ch) {
            if (($ch['channel_code'] ?? null) === $channelCode) {
                $valid = true;
                break;
            }
        }
        if (!$valid) abort(400, 'Channel gateway tidak valid');

        // ✅ Amount konsisten buat semua gateway
        $amount = $order->payable_amount ?? $order->final_price;

        // ✅ prevent double click + jangan reuse payment gateway lain
        $payment = $order->payments()
            ->where('method', 'gateway')
            ->where('status', 'waiting_payment')
            ->where('gateway_name', $gatewayName)
            ->where('channel_code', $channelCode)
            ->latest()
            ->first();

       if (!$payment) {
    $payment = $order->payments()->create([
        'method'            => 'gateway',
        'amount'            => (int)$amount,
        'gateway_name'      => $gatewayName,
        'channel_code'      => $channelCode,
        'gateway_reference' => null,
        'payment_url'       => null,
        'return_token'      => Str::random(48),
        'gateway_payload'   => null,
        'status'            => 'waiting_payment',
    ]);
} else {
    // safety: kalau payment lama belum punya token (data lama)
    if (empty($payment->return_token)) {
        $payment->return_token = Str::random(48);
        $payment->save();
    }
}
if (
    $gatewayName === 'tripay'
    && $payment->status === 'waiting_payment'
    && !empty($payment->gateway_reference)
    && !empty($payment->payment_url)
) {
    return redirect()->away($payment->payment_url);
}
        // ========= TRIPAY =========
if ($gatewayName === 'tripay') {
    $cred = $gateway->credentials ?? [];

    $returnUrl = route('payment.page', $order->id) . '?token=' . $payment->return_token;

    $payload = [
        'method'       => $channelCode,
        'merchant_ref' => $order->invoice_number,
        'amount'       => (int) $amount,

        'customer_name'  => $order->customer_name,
        'customer_email' => $order->customer_email,
        'customer_phone' => $order->customer_phone,

        'order_items' => [[
            'sku'      => 'ORDER',
            'name'     => $order->product_name,
            'price'    => (int) $amount,
            'quantity' => 1,
            'subtotal' => (int) $amount,
        ]],

        // Webhook server-to-server:
        'callback_url' => url('/api/webhooks/tripay'),

        // Return ke browser user:
        'return_url'   => $returnUrl,
    ];

    try {
        $resp = $tripay->createTransaction($cred, $payload);
    } catch (\Throwable $e) {
        Log::error('TRIPAY error', ['err' => $e->getMessage()]);
        return back()->with('error', 'TriPay sedang bermasalah. Coba lagi.');
    }

    // TriPay biasanya balikin:
    // data.reference + data.checkout_url
    $reference = data_get($resp, 'data.reference');
    $checkoutUrl = data_get($resp, 'data.checkout_url');

    $payment->gateway_reference = $reference;
    $payment->payment_url = $checkoutUrl;
    $payment->gateway_payload = $resp;
    $payment->save();

    if (!$checkoutUrl) {
        Log::error('Tripay missing checkout_url', ['resp' => $resp]);
        return back()->with('error', 'TriPay response invalid.');
    }

    return redirect()->away($checkoutUrl);
}


        // ========= DOKU =========
        if ($gatewayName === 'doku') {
            $cred = $gateway->credentials ?? [];

            $payload = [
                'order' => [
                    'invoice_number' => $order->invoice_number,
                    'amount'         => (int)$amount,
                ],
                'customer' => [
                    'name'  => $order->customer_name,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                ],
                'payment' => [
                    'payment_method' => $channelCode,
                ],
                'callback_url' => url('/api/webhooks/doku'),
                'return_url'   => route('payment.page', $order->id),
            ];

            try {
                $resp = $doku->createPayment($cred, $payload);
            } catch (\Throwable $e) {
                Log::error('DOKU error', ['err' => $e->getMessage()]);
                return back()->with('error', 'DOKU sedang bermasalah. Coba lagi.');
            }

            $paymentUrl = data_get($resp, 'payment.paymentUrl')
                ?? data_get($resp, 'paymentUrl')
                ?? data_get($resp, 'redirect_url');

            $payment->gateway_reference = $order->invoice_number;
            $payment->payment_url = $paymentUrl;
            $payment->gateway_payload = $resp;
            $payment->save();

            if (!$paymentUrl) {
                return back()->with('error', 'DOKU tidak mengembalikan payment url.');
            }

            return redirect()->away($paymentUrl);
        }

        // ========= XENDIT =========
        if ($gatewayName === 'xendit') {
            $cred = $gateway->credentials ?? [];

            $returnUrl = route('payment.page', $order->id) . '?token=' . $payment->return_token;

$payload = [
    'external_id' => $order->invoice_number,
    'amount' => (int)$amount,
    'payer_email' => $order->customer_email,
    'description' => 'Order ' . $order->invoice_number,
    'success_redirect_url' => $returnUrl,
    'failure_redirect_url' => $returnUrl,
];


            $http = Http::withBasicAuth($cred['secret_key'] ?? '', '')
                ->post('https://api.xendit.co/v2/invoices', $payload);

            if ($http->failed()) {
   Log::error('Xendit create invoice failed', [
    'status' => $http->status(),
    'body' => $http->body(),
    'mode' => $cred['mode'] ?? null,
    'key_prefix' => substr(($cred['secret_key'] ?? ''), 0, 20),
]);

    return redirect()
        ->route('payment.gateway.page', $order->id)
        ->with('error', 'Xendit sedang bermasalah. Coba lagi atau pilih metode lain.')
        ->with('show_change_method', true);
}


            $resp = $http->json();

            $payment->gateway_reference = $resp['id'] ?? null;
            $payment->payment_url = $resp['invoice_url'] ?? null;
            $payment->gateway_payload = $resp;
            $payment->save();

            if (!$payment->payment_url) {
                Log::error('Xendit missing invoice_url', ['resp' => $resp]);
                return back()->with('error', 'Xendit response invalid.');
            }

            return redirect()->away($payment->payment_url);
        }

        // ========= IPAYMU =========
        if ($gatewayName === 'ipaymu') {
            $cred = $gateway->credentials ?? [];

            $url = ($cred['mode'] ?? 'sandbox') === 'production'
                ? 'https://my.ipaymu.com/api/v2/payment'
                : 'https://sandbox.ipaymu.com/api/v2/payment';

            $body = [
                'product' => [$order->product_name],
                'qty' => [1],
                'price' => [(int)$amount],
                'returnUrl' => route('payment.page', $order->id),
                'notifyUrl' => url('/api/webhooks/ipaymu'),
                'cancelUrl' => route('payment.page', $order->id),
            ];

            $json = json_encode($body);
            $hash = strtolower(hash('sha256', $json));
            $string = "POST:{$cred['va']}:{$hash}:{$cred['api_key']}";
            $signature = hash_hmac('sha256', $string, $cred['api_key']);

            $http = Http::withHeaders([
                'Content-Type' => 'application/json',
                'va' => $cred['va'] ?? '',
                'signature' => $signature,
                'timestamp' => now()->format('YmdHis'),
            ])->post($url, $body);

            if ($http->failed()) {
                Log::error('iPaymu create payment failed', ['body' => $http->body()]);
                return back()->with('error', 'iPaymu sedang bermasalah. Coba lagi.');
            }

            $resp = $http->json();

            $payment->gateway_reference = data_get($resp, 'Data.SessionID');
            $payment->payment_url = data_get($resp, 'Data.Url');
            $payment->gateway_payload = $resp;
            $payment->save();

            if (!$payment->payment_url) {
                Log::error('iPaymu missing Url', ['resp' => $resp]);
                return back()->with('error', 'iPaymu response invalid.');
            }

            return redirect()->away($payment->payment_url);
        }

        // ========= PAYPAL =========
        // ========= PAYPAL =========
        if ($gatewayName === 'paypal') {
            $cred = $gateway->credentials ?? [];

            // amount dalam IDR (sudah konsisten dari atas)
            $amountIdr = (float) $amount;

            // PayPal WAJIB USD (IDR tidak disupport)
            $currency = 'USD';

            // Ambil kurs dari settings (format angka US: 16698.39)
            $rate = (float) Setting::where('key', 'paypal_usd_rate')->value('value');

            // fallback keras kalau setting kosong / rusak
            if ($rate <= 0) {
                $rate = 16698.39;
            }

            // Konversi IDR -> USD
            $amountUsd = round($amountIdr / $rate, 2);

            // PayPal minimal 0.01 USD
            if ($amountUsd < 0.01) {
                $amountUsd = 0.01;
            }

            $payload = [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $order->invoice_number,
                    'description'  => 'Order ' . $order->invoice_number,
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => number_format($amountUsd, 2, '.', ''),
                    ],
                ]],
                'application_context' => [
                    'brand_name' => config('app.name'),
                    'landing_page' => 'LOGIN',
                    'user_action' => 'PAY_NOW',
                    'return_url' => route('paypal.return', $order->id),
                    'cancel_url' => route('paypal.cancel', $order->id),
                ],
            ];

            try {
                $pp = $paypal->createOrder($cred, $payload);
            } catch (\Throwable $e) {
                \Log::error('PayPal createOrder failed', [
                    'order_id' => $order->id,
                    'amount_idr' => $amountIdr,
                    'rate' => $rate,
                    'error' => $e->getMessage(),
                ]);

                return back()->with(
                    'error',
                    'PayPal sedang tidak tersedia. Silakan coba lagi.'
                );
            }

            $paypalOrderId = $pp['id'] ?? null;
            $approveUrl = $paypal->findApproveUrl($pp);

            $payment->gateway_reference = $paypalOrderId;
            $payment->payment_url = $approveUrl;
            $payment->gateway_payload = array_merge(
                (array) $payment->gateway_payload,
                [
                    'paypal_amount_usd' => $amountUsd,
                    'paypal_rate' => $rate,
                    'paypal_amount_idr' => $amountIdr,
                ]
            );
            $payment->save();

            if (!$paypalOrderId || !$approveUrl) {
                \Log::error('PayPal missing approveUrl/orderId', ['resp' => $pp]);
                return back()->with(
                    'error',
                    'PayPal sedang bermasalah. Silakan coba lagi.'
                );
            }

            return redirect()->away($approveUrl);
        }



        // ========= MIDTRANS =========
        if ($gatewayName === 'midtrans') {
            $cred = $gateway->credentials ?? [];

            $payload = [
                'transaction_details' => [
                    'order_id'     => $order->invoice_number,
                    'gross_amount' => (int)$amount,
                ],
                'customer_details' => [
                    'first_name' => $order->billing_first_name ?: $order->customer_name,
                    'last_name'  => $order->billing_last_name ?: '',
                    'email'      => $order->customer_email,
                    'phone'      => $order->customer_phone,
                ],
                'item_details' => [[
                    'id'       => 'ORDER',
                    'price'    => (int)$amount,
                    'quantity' => 1,
                    'name'     => $order->product_name,
                ]],
                'callbacks' => [
                    'finish' => route('payment.page', $order->id),
                ],
            ];

            try {
                $snap = $midtrans->createSnapTransaction($cred, $payload);
            } catch (\Throwable $e) {
                Log::error('Midtrans error', ['err' => $e->getMessage()]);
                return back()->with('error', 'Midtrans sedang bermasalah. Coba lagi.');
            }

            $redirectUrl = $snap['redirect_url'] ?? null;

            $payment->gateway_reference = $order->invoice_number;
            $payment->payment_url = $redirectUrl;
            $payment->gateway_payload = $snap;
            $payment->save();

            if (!$redirectUrl) {
                return back()->with('error', 'Midtrans tidak mengembalikan redirect_url.');
            }

            return redirect()->away($redirectUrl);
        }

        return back()->with('error', 'Gateway tidak dikenal.');
    }

    public function paypalReturn(Request $request, Order $order, PayPalService $paypal)
    {
        $paypalOrderId = (string) $request->query('token', '');
        if ($paypalOrderId === '') {
            return redirect()->route('payment.page', $order->id)->with('error', 'PayPal return tanpa token.');
        }

        $gateway = PaymentGateway::where('name', 'paypal')->where('is_active', 1)->first();
        if (!$gateway) {
            return redirect()->route('payment.page', $order->id)->with('error', 'PayPal gateway tidak aktif.');
        }

        $channelCode = null;

if ($order->payment_method && str_starts_with($order->payment_method, 'gateway:')) {
    $parts = explode(':', $order->payment_method, 3);
    $channelCode = $parts[2] ?? null; // contoh: QRIS
}

$paymentQuery = $order->payments()
    ->where('method', 'gateway')
    ->where('gateway_name', 'paypal')
    ->where('status', 'waiting_payment');


if (!empty($channelCode)) {
    $paymentQuery->where('channel_code', $channelCode);
}

$payment = $paymentQuery->latest()->first();


        if (!$payment) {
            return redirect()->route('payment.page', $order->id)->with('error', 'Payment PayPal tidak ditemukan.');
        }

        if (($payment->gateway_reference ?? '') !== $paypalOrderId) {
            return redirect()->route('payment.page', $order->id)->with('error', 'Token PayPal tidak cocok.');
        }

        try {
            $cap = $paypal->captureOrder($gateway->credentials ?? [], $paypalOrderId);
        } catch (\Throwable $e) {
            Log::error('PayPal capture failed', ['err' => $e->getMessage()]);
            $payment->gateway_payload = array_merge((array)$payment->gateway_payload, ['capture_error' => $e->getMessage()]);
            $payment->save();
            return redirect()->route('payment.page', $order->id)->with('error', 'PayPal capture gagal. Coba lagi.');
        }

        $status = strtoupper((string)($cap['status'] ?? ''));
        $payment->gateway_payload = array_merge((array)$payment->gateway_payload, ['capture' => $cap]);

        if ($status === 'COMPLETED') {
            $payment->status = 'paid';
            $payment->save();

            $order->payment_status = 'paid';
$order->order_status   = 'approved';
$order->save();


            return redirect()->route('payment.page', $order->id)->with('success', 'Pembayaran PayPal berhasil.');
        }

        $payment->save();
        return redirect()->route('payment.page', $order->id)->with('error', 'PayPal status: ' . ($cap['status'] ?? 'UNKNOWN'));
    }

    public function paypalCancel(Order $order)
{
    return redirect()
        ->route('payment.page', $order->id)
        ->with('error', 'Pembayaran PayPal dibatalkan.')
        ->with('show_change_method', true);
}
private function autoLoginForPaidOrder(Order $order): void
{
    $email = trim((string) $order->customer_email);

    if ($email === '') {
        return; // gak bisa auto-create tanpa email
    }

    $user = User::where('email', $email)->first();

    if (!$user) {
        $name = trim((string) $order->customer_name);

        $user = User::create([
            'name'              => $name !== '' ? $name : 'User',
            'email'             => $email,
            'password'          => Hash::make(Str::random(24)),
            'phone'             => $order->customer_phone ?: null,

            // di Order model lu adanya billing_address, bukan customer_address
            'address'           => $order->billing_address ?: null,

            // field lain di User ada, tapi order lu gak punya datanya → biarin null
            'full_address'      => null,
            'sub_district'      => null,

            // auto verified sesuai requirement lu
            'email_verified_at' => now(),

            // field affiliate ada di fillable user lu
            'is_affiliate'           => false,
            'affiliate_status'       => 'none',
            'affiliate_requested_at' => null,
            'affiliate_reviewed_at'  => null,
            'affiliate_reviewed_by'  => null,
            'affiliate_review_note'  => null,
        ]);

        // role default (project lu pakai spatie/permission)
        $user->assignRole('user');
    } else {
        // requirement lu: kalau user ada tapi belum verified → auto verify
        if (empty($user->email_verified_at)) {
            $user->email_verified_at = now();
            $user->save();
        }
    }

    // attach order ke user kalau belum ada
    if (empty($order->user_id)) {
        $order->user_id = $user->id;
        $order->save();
    }

    Auth::login($user);
}


}
