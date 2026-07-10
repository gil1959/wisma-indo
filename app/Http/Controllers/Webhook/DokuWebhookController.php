<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class DokuWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $gateway = PaymentGateway::where('name', 'doku')->first();
        if (!$gateway || !$gateway->is_active) {
            return response()->json(['message' => 'gateway inactive'], 403);
        }

        $payload = $request->all();

        $invoice = data_get($payload, 'order.invoice_number')
            ?? data_get($payload, 'invoice_number')
            ?? data_get($payload, 'order_id');

        $amount = data_get($payload, 'order.amount')
            ?? data_get($payload, 'amount');

        $status = strtoupper((string)(
            data_get($payload, 'transaction.status')
            ?? data_get($payload, 'status')
            ?? ''
        ));

        if (!$invoice) {
            return response()->json(['message' => 'missing invoice'], 400);
        }

        // gateway_reference DOKU lu set = invoice_number
        $payment = Payment::where('gateway_name', 'doku')
            ->where('gateway_reference', $invoice)
            ->latest()
            ->first();

        if (!$payment) {
            return response()->json(['message' => 'payment not found'], 404);
        }

        // Guard amount (optional)
        if ($amount !== null) {
            $amountFloat = (float) $amount;
            if ((float) $payment->amount > 0 && abs(((float)$payment->amount) - $amountFloat) > 0.5) {
                return response()->json(['message' => 'amount mismatch'], 400);
            }
        }

        // Map ke enum DB lu
        $newPaymentStatus = match ($status) {
            'SUCCESS', 'PAID', 'COMPLETED' => 'paid',
            'FAILED', 'EXPIRED' => 'failed',
            default => 'waiting_payment',
        };

        $payment->status = $newPaymentStatus;
        $payment->gateway_payload = $payload;
        $payment->save();

        // Gateway auto approve/reject
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

        return response()->json(['message' => 'ok'], 200);
    }
}
