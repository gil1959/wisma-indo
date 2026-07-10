<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;

class MidtransService
{
    /**
     * Legacy (kalau masih kepakai di tempat lain)
     */
    public function createSnapToken(array $credentials, array $payload): string
    {
        $json = $this->createSnapTransaction($credentials, $payload);

        $token = $json['token'] ?? null;
        if (!$token) {
            throw new \RuntimeException('Midtrans tidak mengembalikan token.');
        }

        return $token;
    }

    /**
     * ✅ Yang lu butuhin: return full response {token, redirect_url}
     */
    public function createSnapTransaction(array $credentials, array $payload): array
    {
        $mode = $credentials['mode'] ?? 'sandbox';
        $serverKey = $credentials['server_key'] ?? null;

        if (!$serverKey) {
            throw new \RuntimeException('Midtrans server_key belum diisi.');
        }

        $base = $mode === 'production'
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';

        $resp = Http::withBasicAuth($serverKey, '')
            ->acceptJson()
            ->post($base . '/snap/v1/transactions', $payload);

        if (!$resp->ok()) {
            throw new \RuntimeException('Gagal create Snap: ' . $resp->body());
        }

        $json = $resp->json();

        if (empty($json['token'])) {
            throw new \RuntimeException('Midtrans tidak mengembalikan token.');
        }

        return $json; // biasanya: ['token' => '...', 'redirect_url' => '...']
    }

    public function snapJsUrl(array $credentials): string
    {
        $mode = $credentials['mode'] ?? 'sandbox';
        return $mode === 'production'
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    public function verifyWebhookSignature(array $credentials, array $notification): bool
    {
        $serverKey = $credentials['server_key'] ?? null;
        if (!$serverKey) return false;

        $orderId = (string)($notification['order_id'] ?? '');
        $statusCode = (string)($notification['status_code'] ?? '');
        $grossAmount = (string)($notification['gross_amount'] ?? '');
        $signatureKey = (string)($notification['signature_key'] ?? '');

        if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $signatureKey === '') return false;

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        return hash_equals($expected, $signatureKey);
    }
}
