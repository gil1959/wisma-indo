<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Webhook\TripayWebhookController;
use App\Http\Controllers\Webhook\DokuWebhookController;
use App\Http\Controllers\Webhook\MidtransWebhookController;
use App\Http\Controllers\Webhook\XenditWebhookController;
use App\Http\Controllers\Webhook\IpaymuWebhookController;
use App\Http\Controllers\Webhook\PayPalWebhookController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Webhooks (NO AUTH middleware)
 * URL yang kepake:
 * - /api/webhooks/tripay
 * - /api/webhooks/doku
 * - /api/webhooks/midtrans
 */
Route::post('/webhooks/tripay', TripayWebhookController::class);
Route::post('/webhooks/doku', DokuWebhookController::class);
Route::post('/webhooks/midtrans', MidtransWebhookController::class);
Route::post('/webhooks/xendit', [XenditWebhookController::class, 'handle']);
Route::post('/webhooks/ipaymu', [IpaymuWebhookController::class, 'handle']);
Route::post('/webhooks/paypal', [\App\Http\Controllers\Webhook\PayPalWebhookController::class, 'handle'])
    ->name('webhooks.paypal');

// Webhooks for Top Up
Route::post('/webhooks/topup/tripay', [\App\Http\Controllers\Api\PaymentCallbackController::class, 'tripayCallback']);
Route::post('/webhooks/topup/xendit', [\App\Http\Controllers\Api\PaymentCallbackController::class, 'xenditCallback']);

Route::match(['GET', 'HEAD'], '/webhooks/tripay', function () {
    return response()->json(['message' => 'ok'], 200);
});
