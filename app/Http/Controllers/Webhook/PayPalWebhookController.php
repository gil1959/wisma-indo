<?php

namespace App\Http\Controllers\Webhooks;
namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Payments\PayPalService;

class PayPalWebhookController extends Controller
{
    public function handle(Request $request, PayPalService $paypal)
    {
        $gateway = PaymentGateway::where('name', 'paypal')->where('is_active', 1)->first();
        if (!$gateway) return response()->json(['ok' => true], 200); // ignore if disabled

        $cred = $gateway->credentials ?? [];
        if (empty($cred['webhook_id'])) {
            // kalau webhook dihidupin tapi webhook_id kosong, itu salah konfigurasi.
            Log::warning('PayPal webhook received but webhook_id not configured');
            return response()->json(['error' => 'webhook_id not configured'], 400);
        }

        // 1) verify signature (WAJIB untuk "lengkap")
        $headers = $request->headers->all();
        $rawBody = $request->getContent();
        $event = json_decode($rawBody, true);

        $verified = $paypal->verifyWebhookSignature($cred, $headers, $event);
        if (!$verified) {
            Log::warning('PayPal webhook signature invalid');
            return response()->json(['error' => 'invalid signature'], 400);
        }

        // 2) idempotency: pakai event_id agar webhook duplicate gak bikin double-paid
        $eventId = $event['id'] ?? null;
        if (!$eventId) return response()->json(['ok' => true], 200);

        // TODO: simpan eventId ke tabel webhook_events (disarankan)
        // Kalau belum mau bikin tabel, minimal cek gateway_payload existing.

        $eventType = $event['event_type'] ?? '';
        $resource = $event['resource'] ?? [];

        if ($eventType === 'PAYMENT.CAPTURE.COMPLETED') {
            $captureId = $resource['id'] ?? null;
            $supp = $resource['supplementary_data']['related_ids'] ?? [];
            $paypalOrderId = $supp['order_id'] ?? null;

            if ($paypalOrderId) {
                $payment = Payment::where('gateway_name', 'paypal')
                    ->where('gateway_reference', $paypalOrderId)
                    ->latest()
                    ->first();

                if ($payment && $payment->status !== 'paid') {
                    $payment->status = 'paid';
                    $payment->gateway_payload = array_merge((array)$payment->gateway_payload, [
                        'webhook' => $event,
                    ]);
                    $payment->save();

                    $order = $payment->order;
                   if ($order && $order->payment_status !== 'paid') {
    $prevOrderStatus = $order->order_status;
    $order->payment_status = 'paid';
    $order->order_status   = 'approved';
    $order->save();
    
    if ($order->order_status !== $prevOrderStatus && $order->order_status === 'approved') {
        app(\App\Services\OrderNotificationService::class)->sendVerificationEmail($order, 'approved');
    }
}

                }
            }
        }

        // event lain bisa di-handle sesuai kebutuhan
        return response()->json(['ok' => true], 200);
    }
}
