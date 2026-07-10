<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\Payments\MidtransService;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function __invoke(Request $request, MidtransService $midtrans)
    {
        $gateway = PaymentGateway::where('name', 'midtrans')->first();
        if (!$gateway || !$gateway->is_active) {
            return response()->json(['message' => 'gateway inactive'], 403);
        }

        $notification = $request->all();

        if (!$midtrans->verifyWebhookSignature($gateway->credentials ?? [], $notification)) {
            return response()->json(['message' => 'invalid signature'], 401);
        }

        $orderId = $notification['order_id'] ?? null;
        $transactionStatus = $notification['transaction_status'] ?? null;
        $fraudStatus = $notification['fraud_status'] ?? null;

        if (!$orderId) return response()->json(['message' => 'missing order_id'], 400);

        // Midtrans: gateway_reference lu set = invoice_number (order_id)
        $payment = Payment::where('gateway_reference', $orderId)->latest()->first();
        if (!$payment) return response()->json(['message' => 'payment not found'], 404);

        // MAP -> enum payments.status (waiting_payment|paid|failed)
        $newPaymentStatus = 'waiting_payment';

        if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
            if ($transactionStatus === 'capture' && $fraudStatus === 'challenge') {
                $newPaymentStatus = 'waiting_payment';
            } else {
                $newPaymentStatus = 'paid';
            }
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure'], true)) {
            $newPaymentStatus = 'failed'; // expire masuk failed (karena enum lu ga ada expired)
        }

        $payment->status = $newPaymentStatus;
        $payment->gateway_payload = $notification;
        $payment->save();

        // FLOW LU: gateway auto approve/reject
        if ($payment->order) {
            $prevOrderStatus = $payment->order->order_status;
            
            if ($newPaymentStatus === 'paid') {
                $payment->order->payment_status = 'paid';
                $payment->order->order_status = 'approved';
            } elseif ($newPaymentStatus === 'failed') {
                $payment->order->payment_status = 'failed';
                $payment->order->order_status = 'rejected';
            } else {
                $payment->order->payment_status = 'waiting_payment';
                // order_status biarin pending
            }
            $payment->order->save();
            
            if ($payment->order->order_status !== $prevOrderStatus) {
                if ($payment->order->order_status === 'approved') {
                    app(\App\Services\OrderNotificationService::class)->sendVerificationEmail($payment->order, 'approved');
                } elseif ($payment->order->order_status === 'rejected') {
                    app(\App\Services\OrderNotificationService::class)->sendVerificationEmail($payment->order, 'rejected');
                }
            }
        }

        return response()->json(['message' => 'ok']);
    }
}
