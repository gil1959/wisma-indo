<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;

class TripayService
{
    private function baseUrl(array $credentials): string
    {
        $mode = strtolower(trim((string)($credentials['mode'] ?? 'sandbox')));

        return $mode === 'production'
            ? 'https://tripay.co.id/api'
            : 'https://tripay.co.id/api-sandbox';
    }

    public function fetchChannels(array $credentials): array
    {
        $apiKey = $credentials['api_key'] ?? null;
        if (!$apiKey) {
            throw new \RuntimeException('TriPay api_key belum diisi.');
        }

        $base = $this->baseUrl($credentials);

        $resp = Http::timeout(20)
            ->acceptJson()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])
            ->get($base . '/merchant/payment-channel');

        if (!$resp->ok()) {
            throw new \RuntimeException('Gagal ambil channel TriPay: ' . $resp->body());
        }

        $json = $resp->json();

        // Kadang TriPay balikin HTTP 200 tapi success=false
        if (isset($json['success']) && $json['success'] === false) {
            $msg = $json['message'] ?? 'Unknown error';

            if (stripos($msg, 'merchant status is rejected') !== false) {
                throw new \RuntimeException("Akun TriPay ditolak: {$msg}");
            }

            throw new \RuntimeException("Gagal ambil channel TriPay: {$msg}");
        }

        // Normalisasi struktur data
        $data = $json['data'] ?? [];
        if (is_array($data) && isset($data['data']) && is_array($data['data'])) {
            $data = $data['data'];
        }
        if (is_array($data) && isset($data['payment_channels']) && is_array($data['payment_channels'])) {
            $data = $data['payment_channels'];
        }
        if ($data === [] && is_array($json) && isset($json[0])) {
            $data = $json;
        }

        if (!is_array($data) || count($data) === 0) {
            $msg = $json['message'] ?? 'data kosong';
            $snippet = substr($resp->body(), 0, 800);
            throw new \RuntimeException("TriPay channel kosong ({$msg}). Response: {$snippet}");
        }

        return array_values(array_map(function ($item) {
            return [
                'channel_code'  => $item['code'] ?? null,
                'name'          => $item['name'] ?? ($item['code'] ?? 'UNKNOWN'),
                'group'         => $item['group'] ?? null,
                'icon_url'      => $item['icon_url'] ?? null,
                'fee_customer'  => $item['fee_customer'] ?? null,
                'active'        => (bool)($item['active'] ?? true),
            ];
        }, $data));
    }

    public function createTransaction(array $credentials, array $payload): array
    {
        $apiKey = $credentials['api_key'] ?? null;
        $privateKey = $credentials['private_key'] ?? null;
        $merchantCode = $credentials['merchant_code'] ?? null;

        if (!$apiKey || !$privateKey || !$merchantCode) {
            throw new \RuntimeException('TriPay credential kurang: api_key/private_key/merchant_code wajib.');
        }

        $base = $this->baseUrl($credentials);

        $merchantRef = $payload['merchant_ref'] ?? null;
        $amount = $payload['amount'] ?? null;

        if (!$merchantRef || !$amount) {
            throw new \RuntimeException('Payload TriPay invalid: merchant_ref dan amount wajib.');
        }

        // Signature sesuai dokumentasi: merchant_code + merchant_ref + amount (HMAC SHA256)
        $signature = hash_hmac('sha256', $merchantCode . $merchantRef . $amount, $privateKey);

        $resp = Http::timeout(20)
            ->acceptJson()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])
            ->asForm()
            ->post($base . '/transaction/create', array_merge($payload, [
                'merchant_code' => $merchantCode,
                'signature'     => $signature,
            ]));

        if (!$resp->ok()) {
            throw new \RuntimeException('Gagal create transaksi TriPay: ' . $resp->body());
        }

        return $resp->json();
    }

    public function getTransactionDetail(array $credentials, string $reference): array
    {
        $apiKey = $credentials['api_key'] ?? null;
        if (!$apiKey) {
            throw new \RuntimeException('TriPay api_key belum diisi.');
        }

        $base = $this->baseUrl($credentials);

        $resp = Http::timeout(20)
            ->acceptJson()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])
            ->get($base . '/transaction/detail', [
                'reference' => $reference,
            ]);

        if (!$resp->ok()) {
            throw new \RuntimeException('Gagal ambil detail transaksi TriPay: ' . $resp->body());
        }

        return $resp->json();
    }
}
