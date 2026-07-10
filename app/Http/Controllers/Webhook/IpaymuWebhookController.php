<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class IpaymuWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();

        Log::info('iPaymu webhook payload', $data);

        /**
         * Status iPaymu:
         * Status = 1  => sukses
         */
        if (
            isset($data['Status']) &&
            (int) $data['Status'] === 1 &&
            isset($data['SessionID'])
        ) {
            $payment = Payment::where('gateway_name', 'ipaymu')
    ->where('gateway_reference', $data['SessionID'])
    ->latest()
    ->first();

            if (!$payment) {
                Log::error('iPaymu webhook: payment not found', [
                    'reference' => $data['SessionID']
                ]);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            // update payment
            $payment->status = 'paid';
            $payment->save();

            // update order
           if ($payment->order) {
    $prevOrderStatus = $payment->order->order_status;
    $payment->order->payment_status = 'paid';
    $payment->order->order_status   = 'approved';
    $payment->order->save();
    
    if ($payment->order->order_status !== $prevOrderStatus && $payment->order->order_status === 'approved') {
        app(\App\Services\OrderNotificationService::class)->sendVerificationEmail($payment->order, 'approved');
    }
}

        }

        return response()->json(['success' => true]);
    }
}
