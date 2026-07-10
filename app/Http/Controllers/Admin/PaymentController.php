<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\PaymentMethod;
use App\Services\Payments\TripayService;
use App\Services\Payments\DokuService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Setting;


class PaymentController extends Controller
{
    public function index()
    {

        $defaults = [
            ['name' => 'doku', 'label' => 'DOKU'],
            ['name' => 'tripay', 'label' => 'TriPay'],
            ['name' => 'midtrans', 'label' => 'Midtrans'],
            ['name' => 'xendit', 'label' => 'Xendit'],
            ['name' => 'ipaymu', 'label' => 'iPaymu'],
            ['name' => 'paypal', 'label' => 'PayPal'],
        ];

        foreach ($defaults as $d) {
            PaymentGateway::firstOrCreate(
                ['name' => $d['name']],
                [
                    'label' => $d['label'],
                    'is_active' => false,
                    'credentials' => null,
                    'channels' => null,
                    'channels_synced_at' => null,
                ]
            );
        }

        $methods = PaymentMethod::orderBy('id', 'desc')->get();

        // Tampilkan HANYA 3 gateway
        $gateways = PaymentGateway::whereIn('name', [
            'doku',
            'tripay',
            'midtrans',
            'xendit',
            'ipaymu',
            'paypal',
        ])->orderBy('id')->get();

        $settings = Setting::whereIn('key', ['manual_unique_code_min', 'manual_unique_code_max'])
            ->pluck('value', 'key')
            ->toArray();


        return view('admin.payments.index', compact('methods', 'gateways', 'settings'));
    }

    public function addBank(Request $request)
    {
        // 1. Normalize input
        $request->merge([
            'swift_code' => $request->swift_code
                ? strtoupper(str_replace(' ', '', $request->swift_code))
                : null,
        ]);

        // 2. Validasi
        $request->validate([
            'bank_name'      => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder' => 'required|string|max:100',
            'swift_code'     => 'nullable|string|min:8|max:11|alpha_num',
        ]);

        // 3. Simpan
        PaymentMethod::create([
            'method_name'     => $request->bank_name,
            'slug'            => 'manual-' . Str::slug($request->bank_name) . '-' . time(),
            'type'            => 'manual',
            'bank_name'       => $request->bank_name,
            'account_number'  => $request->account_number,
            'account_holder'  => $request->account_holder,
            'swift_code'      => $request->swift_code,
            'is_active'       => true,
        ]);

        return back()->with('success', 'Rekening berhasil ditambahkan.');
    }

    public function deleteBank($bank)
    {
        // $bank itu id dari payment_methods
        $method = PaymentMethod::where('id', (int) $bank)
            ->where('type', 'manual')
            ->first();

        if (!$method) {
            return back()->with('error', 'Rekening manual tidak ditemukan.');
        }

        $method->delete();

        return back()->with('success', 'Rekening manual berhasil dihapus.');
    }


    public function toggleGateway(Request $request, $id, TripayService $tripay, DokuService $doku)
    {
        $gateway = PaymentGateway::findOrFail($id);

        if (!in_array($gateway->name, ['doku', 'tripay', 'midtrans', 'xendit', 'ipaymu', 'paypal'], true)) {
            abort(404);
        }


        $enable = (bool) $request->input('enable');

        // DISABLE
        if (!$enable) {
            $gateway->is_active = false;
            $gateway->save();

            return back()->with('success', strtoupper($gateway->name) . ' nonaktif.');
        }

        // ENABLE
        $mode = $request->input('mode', 'sandbox');
        if (!in_array($mode, ['sandbox', 'production'], true)) {
            return back()->with('error', 'Mode harus sandbox atau production.');
        }

        $credentials = ['mode' => $mode];

        // =========================
        // TRIPAY
        // =========================
        if ($gateway->name === 'tripay') {
            $credentials['api_key']       = trim((string) $request->input('api_key', ''));
            $credentials['private_key']   = trim((string) $request->input('private_key', ''));
            $credentials['merchant_code'] = trim((string) $request->input('merchant_code', ''));

            foreach (['api_key', 'private_key', 'merchant_code'] as $k) {
                if ($credentials[$k] === '') {
                    return back()->with('error', "TriPay: field {$k} wajib.");
                }
            }

            try {
                $channels = $tripay->fetchChannels($credentials);

                // simpan hanya active (biar list checkout clean)
                $channels = array_values(array_filter($channels, function ($c) {
                    return (bool)($c['active'] ?? true);
                }));


                // ✅ PENTING: kalau kosong, jangan dianggap sukses
                if (count($channels) === 0) {
                    return back()->with(
                        'error',
                        'TriPay: channel kosong. Pastikan API Key sesuai MODE (sandbox/production) dan merchant sudah approved di TriPay.'
                    );
                }
            } catch (\Throwable $e) {
                return back()->with('error', 'Gagal sync channel TriPay: ' . $e->getMessage());
            }
        }

        // =========================
        // DOKU
        // =========================
        elseif ($gateway->name === 'doku') {
            $credentials['client_id']  = trim((string) $request->input('client_id', ''));
            $credentials['secret_key'] = trim((string) $request->input('secret_key', ''));

            foreach (['client_id', 'secret_key'] as $k) {
                if ($credentials[$k] === '') {
                    return back()->with('error', "DOKU: field {$k} wajib.");
                }
            }

            $channels = $doku->staticChannels();
        } elseif ($gateway->name === 'xendit') {

    // 1) Ambil credential dari request dulu
    $credentials['secret_key'] = trim((string) $request->input('secret_key', ''));
    $credentials['callback_token'] = trim((string) $request->input('callback_token', ''));

    // 2) Validasi wajib
    foreach (['secret_key', 'callback_token'] as $k) {
        if ($credentials[$k] === '') {
            return back()->with('error', "Xendit: field {$k} wajib.");
        }
    }

    // 3) Validasi mode vs prefix key (baru dicek setelah key ada)
    $mode = $credentials['mode'] ?? 'sandbox';
    $key  = $credentials['secret_key'];

    if ($mode === 'production' && str_starts_with($key, 'xnd_development_')) {
        return back()->with('error', 'Xendit: Mode Production wajib pakai secret key xnd_production_, bukan xnd_development_.');
    }

    if ($mode === 'sandbox' && str_starts_with($key, 'xnd_production_')) {
        return back()->with('error', 'Xendit: Mode Sandbox wajib pakai secret key xnd_development_, bukan xnd_production_.');
    }

    // 4) Channel statis
    $channels = [
        ['channel_code' => 'invoice', 'name' => 'Xendit Invoice (All Methods)'],
    ];
}

 elseif ($gateway->name === 'ipaymu') {

            $credentials['va'] = trim((string) $request->input('va', ''));
            $credentials['api_key'] = trim((string) $request->input('api_key', ''));

            foreach (['va', 'api_key'] as $k) {
                if ($credentials[$k] === '') {
                    return back()->with('error', "iPaymu: field {$k} wajib.");
                }
            }

            $channels = [
                ['channel_code' => 'redirect', 'name' => 'iPaymu (All Methods)'],
            ];
        } elseif ($gateway->name === 'paypal') {
            $credentials['client_id'] = trim((string) $request->input('client_id', ''));
            $credentials['client_secret'] = trim((string) $request->input('client_secret', ''));
            // optional tapi berguna kalau nanti mau webhook verification
            $credentials['webhook_id'] = trim((string) $request->input('webhook_id', ''));

            foreach (['client_id', 'client_secret'] as $k) {
                if ($credentials[$k] === '') {
                    return back()->with('error', "PayPal: field {$k} wajib.");
                }
            }

            // channel statis (biar konsisten dengan konsep “channels tersimpan”)
            $channels = [
                ['channel_code' => 'checkout', 'name' => 'PayPal Checkout'],
            ];
        }


        // =========================
        // MIDTRANS
        // =========================
        else { // midtrans
            $credentials['server_key'] = trim((string) $request->input('server_key', ''));
            $credentials['client_key'] = trim((string) $request->input('client_key', ''));

            foreach (['server_key', 'client_key'] as $k) {
                if ($credentials[$k] === '') {
                    return back()->with('error', "Midtrans: field {$k} wajib.");
                }
            }

            $channels = [
                ['channel_code' => 'snap', 'name' => 'Midtrans Snap (All Methods)'],
            ];
        }




        // SAVE
        $gateway->is_active = true;
        $gateway->credentials = $credentials;
        $gateway->channels = $channels;
        $gateway->channels_synced_at = now();
        $gateway->save();

        return back()->with('success', strtoupper($gateway->name) . ' aktif.');
    }
    public function updateUniqueCodeSetting(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'manual_unique_code_min' => ['required', 'integer', 'min:1', 'max:999'],
            'manual_unique_code_max' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $min = (int) $data['manual_unique_code_min'];
        $max = (int) $data['manual_unique_code_max'];

        // kalau kebalik, swap biar user gak error
        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }

        Setting::updateOrCreate(['key' => 'manual_unique_code_min'], ['value' => (string)$min]);
        Setting::updateOrCreate(['key' => 'manual_unique_code_max'], ['value' => (string)$max]);

        return back()->with('success', 'Setting kode unik berhasil disimpan.');
    }
}
