<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\PaymentGateway;
use App\Models\Setting;
use App\Services\Payments\TripayService;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\MicePackage;



class CheckoutController extends Controller
{
    public function show($orderId, TripayService $tripay)
    {
        $order = Order::findOrFail($orderId);

        // Ambil paket buat ringkasan
        if ($order->type === 'tour') {
    $package = \App\Models\TourPackage::find($order->product_id);
} elseif ($order->type === 'rent_car') {
    $package = \App\Models\RentCarPackage::find($order->product_id);
} elseif ($order->type === 'ship') {
    $package = \App\Models\ShipPackage::find($order->product_id);
} elseif ($order->type === 'umrah') {
    $package = \App\Models\UmrahPackage::find($order->product_id);
} elseif ($order->type === 'mice') {
    $package = \App\Models\MicePackage::find($order->product_id);
} else {
    $package = null;
}



        // Manual methods tetap sama
        $manualMethods = PaymentMethod::where('is_active', 1)
            ->where('type', 'manual')
            ->orderBy('id')
            ->get();

        // Gateway: batasi cuma 3
        $gateways = PaymentGateway::where('is_active', 1)
            ->whereIn('name', ['doku', 'tripay', 'midtrans', 'xendit', 'ipaymu', 'paypal'])
            ->orderBy('id')
            ->get();

        // Bangun opsi gateway per channel, supaya checkout bisa list metode
        // payment_method format: gateway:{gateway_name}:{channel_code}
        $gatewayOptions = [];
        foreach ($gateways as $g) {
            $channels = is_array($g->channels) ? $g->channels : [];

            // ✅ Fallback: kalau TriPay aktif tapi channels kosong, sync ulang di checkout
            if ($g->name === 'tripay' && count($channels) === 0) {
                try {
                    $creds = is_array($g->credentials) ? $g->credentials : [];
                    $fresh = $tripay->fetchChannels($creds);

                    // ambil yang active saja
                    $fresh = array_values(array_filter($fresh, function ($c) {
                        return (bool)($c['active'] ?? true);
                    }));

                    // simpan hasilnya biar next load gak fetch lagi
                    if (count($fresh) > 0) {
                        $g->channels = $fresh;
                        $g->channels_synced_at = now();
                        $g->save();
                        $channels = $fresh;
                    }
                } catch (\Throwable $e) {
                    Log::warning('Tripay channels empty on checkout and sync failed', [
                        'gateway_id' => $g->id,
                        'error' => $e->getMessage(),
                    ]);
                    $channels = []; // biar aman, gak bikin checkout error
                }
            }

            foreach ($channels as $ch) {
                $code = $ch['channel_code'] ?? null;
                if (!$code) continue;

                // ✅ safety: skip yang nonaktif
                if (isset($ch['active']) && !(bool)$ch['active']) {
                    continue;
                }

                $gatewayLabel = $g->label ?? strtoupper($g->name);

                $gatewayOptions[] = [
                    'value'         => "gateway:{$g->name}:{$code}",
                    'label'         => $gatewayLabel . ' - ' . ($ch['name'] ?? $code),

                    // tambahan untuk UI checkout (logo + grouping)
                    'gateway'       => $g->name,
                    'gateway_label' => $gatewayLabel,
                    'channel_code'  => $code,
                    'name'          => $ch['name'] ?? $code,
                    'group'         => $ch['group'] ?? null,
                    'icon_url'      => $ch['icon_url'] ?? null,
                ];
            }
        }

        return view('front.checkout.index', [
            'order'          => $order,
            'package'        => $package,
            'manualMethods'  => $manualMethods,
            'gateways'       => $gateways,
            'gatewayOptions' => $gatewayOptions,
        ]);
    }

    public function process(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        $data = $request->validate([
            'billing_first_name' => 'required|string|max:120',
            'billing_last_name'  => 'required|string|max:120',
            'billing_country'    => 'required|string',
            'billing_address'    => 'required|string',
            'billing_city'       => 'required|string',
            'billing_state'      => 'required|string',
            'billing_postal'     => 'required|string|max:20',
            'billing_phone'      => 'required|string|max:20',
            'payment_method'     => 'required|string',
        ]);

        $raw = $data['payment_method'];
// ===============================
// AUTO-CREATE USER UNTUK GUEST
// ===============================
if (!Auth::check()) {
    // Email sumbernya dari draft booking (customer_email)
    $email = trim((string) $order->customer_email);

    if (empty($email)) {
        return back()->withErrors([
            'payment_method' => 'Email customer kosong. Tidak bisa membuat akun otomatis.',
        ])->withInput();
    }

    // Kalau email sudah terdaftar => jangan auto-login (ini celah takeover)
    $existing = User::where('email', $email)->first();
    if ($existing) {
        return redirect()
            ->route('login')
            ->with('error', 'Email sudah terdaftar. Silakan login dulu untuk melanjutkan pembayaran.');
    }

    $fullName = trim($data['billing_first_name'] . ' ' . $data['billing_last_name']);

    // Gabung alamat jadi 1 text
    $fullAddress = trim(
        ($data['billing_address'] ?? '') . ', ' .
        ($data['billing_city'] ?? '') . ', ' .
        ($data['billing_state'] ?? '') . ', ' .
        ($data['billing_postal'] ?? '') . ', ' .
        ($data['billing_country'] ?? '')
    );

    $user = User::create([
        'name'              => $fullName ?: ($order->customer_name ?? 'User'),
        'email'             => $email,
        'password'          => Hash::make(Str::random(24)),
        'phone'             => $data['billing_phone'] ?? ($order->customer_phone ?? null),
        'address'           => $fullAddress ?: null,
        'email_verified_at' => now(), // auto verified sesuai requirement lu
        'is_affiliate'            => false,
    'affiliate_status'        => 'none',
    'affiliate_requested_at'  => null,
    'affiliate_reviewed_at'   => null,
    'affiliate_reviewed_by'   => null,
    'affiliate_review_note'   => null,
    ]);

    // Role default
    $user->assignRole('user');

    // Login otomatis biar bisa direct ke dashboard order
    Auth::login($user);

    // Attach order ke user
    $order->user_id = $user->id;
    $order->save();
} else {
    // Kalau sudah login, pastikan order ini milik dia (atau attach kalau masih null)
    if ($order->user_id === null) {
        $order->user_id = Auth::id();
        $order->save();
    }

    if ((int) $order->user_id !== (int) Auth::id()) {
    abort(403, 'Order ini bukan milik akun kamu.');
}

}

        // --- Manual: manual:{id} ---
        if (str_starts_with($raw, 'manual:')) {
            $value = substr($raw, strlen('manual:'));

            $method = PaymentMethod::where('id', (int)$value)
                ->where('type', 'manual')
                ->where('is_active', 1)
                ->first();

            if (!$method) {
                return back()->withErrors([
                    'payment_method' => 'Rekening transfer tidak ditemukan / nonaktif.',
                ])->withInput();
            }

            // Generate kode unik (manual)
            if ($order->unique_code === null || $order->payable_amount === null) {
                $min = (int)(Setting::where('key', 'manual_unique_code_min')->value('value') ?? 1);
                $max = (int)(Setting::where('key', 'manual_unique_code_max')->value('value') ?? 999);

                if ($min < 1) $min = 1;
                if ($max > 999) $max = 999;
                if ($min > $max) {
                    [$min, $max] = [$max, $min];
                }

                $code = null;

                // anti bentrok: payable_amount unik untuk semua order yang masih menunggu
                for ($tries = 0; $tries < 25; $tries++) {
                    $candidate = random_int($min, $max);
                    $payable = (int)$order->final_price + $candidate;

                    $exists = Order::whereIn('payment_status', ['waiting_payment', 'waiting_verification'])
                        ->where('payable_amount', $payable)
                        ->exists();

                    if (!$exists) {
                        $code = $candidate;
                        break;
                    }
                }

                // fallback scan kalau range sempit
                if ($code === null) {
                    for ($candidate = $min; $candidate <= $max; $candidate++) {
                        $payable = (int)$order->final_price + $candidate;

                        $exists = Order::whereIn('payment_status', ['waiting_payment', 'waiting_verification'])
                            ->where('payable_amount', $payable)
                            ->exists();

                        if (!$exists) {
                            $code = $candidate;
                            break;
                        }
                    }
                }

                if ($code === null) $code = $min;

                $order->unique_code = $code;
                $order->payable_amount = (int)$order->final_price + $code;
            }

            // Update order tetap sama seperti flow lama
            $order->update([
                'billing_first_name' => $data['billing_first_name'],
                'billing_last_name'  => $data['billing_last_name'],
                'billing_country'    => $data['billing_country'],
                'billing_address'    => $data['billing_address'],
                'billing_city'       => $data['billing_city'],
                'billing_state'      => $data['billing_state'],
                'billing_postal'     => $data['billing_postal'],
                'billing_phone'      => $data['billing_phone'],
                'payment_method'     => $raw,
                'payment_status'     => 'waiting_payment',
                'unique_code'        => $order->unique_code,
                'payable_amount'     => $order->payable_amount,
            ]);

            return redirect()->route('payment.manual.page', $order->id);
        }

        // --- Gateway: gateway:{gateway}:{channel_code} ---
        if (str_starts_with($raw, 'gateway:')) {
            $parts = explode(':', $raw, 3);
            if (count($parts) !== 3) {
                return back()->withErrors([
                    'payment_method' => 'Format payment gateway tidak valid.',
                ])->withInput();
            }

            [, $gatewayName, $channelCode] = $parts;

            // Batasi gateway hanya 3
            if (!in_array($gatewayName, ['doku', 'tripay', 'midtrans', 'xendit', 'ipaymu', 'paypal'], true)) {
                return back()->withErrors([
                    'payment_method' => 'Payment gateway tidak didukung.',
                ])->withInput();
            }

            $gateway = PaymentGateway::where('name', $gatewayName)
                ->where('is_active', 1)
                ->first();

            if (!$gateway) {
                return back()->withErrors([
                    'payment_method' => 'Payment gateway tidak tersedia.',
                ])->withInput();
            }

            // Pastikan channelCode ada di channels yang tersimpan (anti-tamper)
            $channels = is_array($gateway->channels) ? $gateway->channels : [];
            $found = false;
            foreach ($channels as $ch) {
                if (($ch['channel_code'] ?? null) === $channelCode) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                return back()->withErrors([
                    'payment_method' => 'Metode gateway tidak valid.',
                ])->withInput();
            }

            // ✅ Generate kode unik juga untuk gateway
            if ($order->unique_code === null || $order->payable_amount === null) {
                $min = (int)(Setting::where('key', 'manual_unique_code_min')->value('value') ?? 1);
                $max = (int)(Setting::where('key', 'manual_unique_code_max')->value('value') ?? 999);

                if ($min < 1) $min = 1;
                if ($max > 999) $max = 999;
                if ($min > $max) {
                    [$min, $max] = [$max, $min];
                }

                $code = null;

                for ($tries = 0; $tries < 25; $tries++) {
                    $candidate = random_int($min, $max);
                    $payable = (int)$order->final_price + $candidate;

                    $exists = Order::whereIn('payment_status', ['waiting_payment', 'waiting_verification'])
                        ->where('payable_amount', $payable)
                        ->exists();

                    if (!$exists) {
                        $code = $candidate;
                        break;
                    }
                }

                if ($code === null) $code = $min;

                $order->unique_code = $code;
                $order->payable_amount = (int)$order->final_price + $code;
            }

            // Update order sama: simpan payment_method & status waiting_payment + nominal unik
            $order->update([
                'billing_first_name' => $data['billing_first_name'],
                'billing_last_name'  => $data['billing_last_name'],
                'billing_country'    => $data['billing_country'],
                'billing_address'    => $data['billing_address'],
                'billing_city'       => $data['billing_city'],
                'billing_state'      => $data['billing_state'],
                'billing_postal'     => $data['billing_postal'],
                'billing_phone'      => $data['billing_phone'],
                'payment_method'     => $raw,
                'payment_status'     => 'waiting_payment',
                'unique_code'        => $order->unique_code,
                'payable_amount'     => $order->payable_amount,
            ]);

            return redirect()->route('payment.gateway.page', $order->id);
        }

        return back()->withErrors([
            'payment_method' => 'Metode pembayaran tidak dikenal.',
        ])->withInput();
    }
}
