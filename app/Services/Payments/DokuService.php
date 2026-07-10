<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;

class DokuService
{
    public function createPayment(array $credentials, array $payload): array
    {
        $mode = strtolower(trim((string)($credentials['mode'] ?? 'sandbox')));
if (!in_array($mode, ['sandbox', 'production'], true)) {
    $mode = 'sandbox';
}

$clientId  = trim((string)($credentials['client_id'] ?? ''));
$secretKey = trim((string)($credentials['secret_key'] ?? ''));

// buang whitespace aneh (copy paste sering ada)
$clientId = preg_replace('/\s+/', '', $clientId);

if ($clientId === '' || $secretKey === '') {
    throw new \RuntimeException('DOKU credential kurang: client_id & secret_key wajib.');
}

$base = $mode === 'production'
    ? 'https://api.doku.com'
    : 'https://api-sandbox.doku.com';


        $path = '/checkout/v1/payment';
        $url = $base . $path;

        $requestId = (string) \Illuminate\Support\Str::uuid();
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');

        // Digest (simple)
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $digest = base64_encode(hash('sha256', $body, true));

        // NOTE: Format signature DOKU bisa beda tergantung produk.
        // Ini implementasi umum "HMAC SHA256" di signature header.
        $signatureComponent =
            "Client-Id:" . $clientId . "\n" .
            "Request-Id:" . $requestId . "\n" .
            "Request-Timestamp:" . $timestamp . "\n" .
            "Request-Target:" . $path . "\n" .
            "Digest:" . $digest;

        $signature = base64_encode(hash_hmac('sha256', $signatureComponent, $secretKey, true));

       $resp = Http::withHeaders([
    // kirim dua versi untuk kompatibilitas (beberapa gateway implementasi masih “aneh”)
    'Client-Id' => $clientId,
    'Client-ID' => $clientId,

    'Request-Id' => $requestId,
    'Request-Timestamp' => $timestamp,
    'Signature' => 'HMACSHA256=' . $signature,
    'Digest' => $digest,
    'Content-Type' => 'application/json',
])->withBody($body, 'application/json')
  ->post($url);


        if (!$resp->ok()) {
    throw new \RuntimeException(
        'Gagal create pembayaran DOKU (mode=' . $mode . ', client_id=' . substr($clientId, 0, 6) . '***): ' . $resp->body()
    );
}


        return $resp->json();
    }

    public function staticChannels(): array
    {
        // DOKU “payment_method_types” contoh yang umum dipakai.
        // Kalau project lu butuh lebih spesifik, tinggal ganti list ini.
        return [
            ['channel_code' => 'VIRTUAL_ACCOUNT', 'name' => 'Virtual Account'],
            ['channel_code' => 'CREDIT_CARD', 'name' => 'Kartu Kredit'],
            ['channel_code' => 'E_WALLET', 'name' => 'E-Wallet'],
            ['channel_code' => 'RETAIL', 'name' => 'Retail (Alfamart/Indomaret)'],
            ['channel_code' => 'QRIS', 'name' => 'QRIS'],
        ];
    }
}
