<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TripayWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        // Ambil gateway config (jangan blok webhook hanya karena is_active)
        $gateway = PaymentGateway::where('name', 'tripay')->first();
        if (!$gateway) {
            Log::error('Tripay webhook: gateway not configured', [
                'ip' => $request->ip(),
                'payload' => $request->all(),
            ]);
            return response()->json(['message' => 'gateway not configured'], 500);
        }

        $credentials = $gateway->credentials ?? [];
        $privateKey = $credentials['private_key'] ?? null;
        if (!$privateKey) {
            Log::error('Tripay webhook: missing private_key', [
                'ip' => $request->ip(),
                'payload' => $request->all(),
            ]);
            return response()->json(['message' => 'missing private_key'], 400);
        }

        // Optional event filter (kalau header kosong, tetap lanjut)
        $event = (string) $request->header('X-Callback-Event');
        if ($event !== '' && $event !== 'payment_status') {
            return response()->json(['message' => 'ignored event'], 200);
        }

        // Signature verification (Tripay kirim raw body)
        $raw = $request->getContent();
        $sig = (string) $request->header('X-Callback-Signature');

        $expected = hash_hmac('sha256', $raw, $privateKey);
        if ($sig === '' || !hash_equals($expected, $sig)) {
            Log::warning('Tripay webhook: invalid signature', [
                'ip' => $request->ip(),
                'sig' => $sig,
            ]);
            return response()->json(['message' => 'invalid signature'], 403);
        }

        $data = $request->input('data');
        if (!$data || !is_array($data)) {
            return response()->json(['message' => 'invalid payload'], 400);
        }

        $reference = $data['reference'] ?? null;
        $merchantRef = $data['merchant_ref'] ?? null; // invoice_number
        $status = strtoupper((string) ($data['status'] ?? ''));

        if (!$reference) {
            return response()->json(['message' => 'missing reference'], 400);
        }

        // 1) Cari payment by reference dulu
        $payment = Payment::where('gateway_name', 'tripay')
            ->where('gateway_reference', $reference)
            ->latest()
            ->first();

        // 2) Fallback: cari by invoice (merchant_ref) kalau reference mismatch
        if (!$payment && $merchantRef) {
            $payment = Payment::where('gateway_name', 'tripay')
                ->where('status', 'waiting_payment')
                ->whereHas('order', function ($q) use ($merchantRef) {
                    $q->where('invoice_number', $merchantRef);
                })
                ->latest()
                ->first();

            if ($payment) {
                // sinkronkan reference terbaru
                $payment->gateway_reference = $reference;
            }
        }

        if (!$payment) {
            Log::warning('Tripay webhook payment not found', [
                'reference' => $reference,
                'merchant_ref' => $merchantRef,
                'payload' => $request->all(),
            ]);
            return response()->json(['message' => 'payment not found'], 404);
        }

        $newPaymentStatus = match ($status) {
            'PAID', 'SUCCESS' => 'paid',
            'FAILED', 'EXPIRED' => 'failed',
            default => 'waiting_payment',
        };

        // Idempotent: jangan turunin status yang sudah paid
        if ($payment->status === 'paid' && $newPaymentStatus !== 'paid') {
            // tetap simpan payload terakhir buat audit
            $payment->gateway_payload = $request->all();
            $payment->save();

            return response()->json(['message' => 'ok'], 200);
        }

        $payment->status = $newPaymentStatus;
        $payment->gateway_payload = $request->all();
        $payment->save();

        $order = $payment->order;
        if ($order) {
            $prevOrderStatus = $order->order_status;
            
            if ($newPaymentStatus === 'paid') {
                $order->payment_status = 'paid';
                $order->order_status = 'approved';
            } elseif ($newPaymentStatus === 'failed') {
                $order->payment_status = 'failed';
                $order->order_status = 'rejected';
            } else {
                $order->payment_status = 'waiting_payment';
            }
            $order->save();
            
            if ($order->order_status !== $prevOrderStatus) {
                if ($order->order_status === 'approved') {
                    app(\App\Services\OrderNotificationService::class)->sendVerificationEmail($order, 'approved');
                } elseif ($order->order_status === 'rejected') {
                    app(\App\Services\OrderNotificationService::class)->sendVerificationEmail($order, 'rejected');
                }
            }
        }

        return response()->json(['message' => 'ok'], 200);
    }
}
