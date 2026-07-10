<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;

class PayPalService
{
    private function baseUrl(string $mode): string
    {
        return $mode === 'production'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    public function getAccessToken(array $cred): string
    {
        $mode = $cred['mode'] ?? 'sandbox';
        $clientId = $cred['client_id'] ?? null;
        $clientSecret = $cred['client_secret'] ?? null;

        if (!$clientId || !$clientSecret) {
            throw new \RuntimeException('PayPal credentials belum lengkap.');
        }

        $resp = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->post($this->baseUrl($mode) . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        // failed() = 400-599
        if ($resp->failed()) {
            throw new \RuntimeException('PayPal token error: ' . $resp->body());
        }

        $token = $resp->json('access_token');
        if (!$token) {
            throw new \RuntimeException('PayPal tidak mengembalikan access_token.');
        }

        return $token;
    }

    public function createOrder(array $cred, array $payload): array
    {
        $mode = $cred['mode'] ?? 'sandbox';
        $token = $this->getAccessToken($cred);

        $resp = Http::withToken($token)
            ->acceptJson()
            ->post($this->baseUrl($mode) . '/v2/checkout/orders', $payload);

        // PayPal create order biasanya 201, jadi jangan cek ==200.
        if ($resp->failed()) {
            throw new \RuntimeException('PayPal create order error: ' . $resp->body());
        }

        return $resp->json();
    }

    public function captureOrder(array $cred, string $orderId): array
    {
        $mode = $cred['mode'] ?? 'sandbox';
        $token = $this->getAccessToken($cred);

        $resp = Http::withToken($token)
            ->acceptJson()
            ->post($this->baseUrl($mode) . "/v2/checkout/orders/{$orderId}/capture");

        if ($resp->failed()) {
            throw new \RuntimeException('PayPal capture error: ' . $resp->body());
        }

        return $resp->json();
    }

    public function findApproveUrl(array $paypalOrder): ?string
    {
        foreach (($paypalOrder['links'] ?? []) as $l) {
            $rel = $l['rel'] ?? '';
            if ($rel === 'approve' || $rel === 'payer-action') {
                return $l['href'] ?? null;
            }
        }
        return null;
    }

    // ===== Optional: webhook signature verification (kalau lu mau "lengkap") =====
    public function verifyWebhookSignature(array $cred, array $headers, array $event): bool
    {
        $mode = $cred['mode'] ?? 'sandbox';
        $webhookId = $cred['webhook_id'] ?? '';

        if ($webhookId === '') {
            return false;
        }

        $token = $this->getAccessToken($cred);

        $transmissionId = $this->header($headers, 'paypal-transmission-id');
        $transmissionTime = $this->header($headers, 'paypal-transmission-time');
        $certUrl = $this->header($headers, 'paypal-cert-url');
        $authAlgo = $this->header($headers, 'paypal-auth-algo');
        $transmissionSig = $this->header($headers, 'paypal-transmission-sig');

        if (!$transmissionId || !$transmissionTime || !$certUrl || !$authAlgo || !$transmissionSig) {
            return false;
        }

        $body = [
            'auth_algo' => $authAlgo,
            'cert_url' => $certUrl,
            'transmission_id' => $transmissionId,
            'transmission_sig' => $transmissionSig,
            'transmission_time' => $transmissionTime,
            'webhook_id' => $webhookId,
            'webhook_event' => $event,
        ];

        $resp = Http::withToken($token)
            ->acceptJson()
            ->post($this->baseUrl($mode) . '/v1/notifications/verify-webhook-signature', $body);

        if ($resp->failed()) return false;

        return strtoupper((string)($resp->json('verification_status') ?? '')) === 'SUCCESS';
    }

    private function header(array $headers, string $key): ?string
    {
        $k = strtolower($key);
        foreach ($headers as $hk => $hv) {
            if (strtolower($hk) === $k) {
                if (is_array($hv)) return (string)($hv[0] ?? '');
                return (string)$hv;
            }
        }
        return null;
    }
}
