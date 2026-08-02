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

/*
|--------------------------------------------------------------------------
| REST API v1 Routes
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->namespace('App\Http\Controllers\Api\V1')->group(function () {

    // Auth
    Route::post('/register', [App\Http\Controllers\Api\V1\AuthController::class, 'register']);
    Route::post('/login', [App\Http\Controllers\Api\V1\AuthController::class, 'login']);

    // Public Routes
    Route::get('/home', [App\Http\Controllers\Api\V1\Public\HomeController::class, 'index']);
    
    Route::get('/listings', [App\Http\Controllers\Api\V1\Public\ListingController::class, 'index']);
    Route::get('/listings/{slug}', [App\Http\Controllers\Api\V1\Public\ListingController::class, 'show']);
    Route::get('/listing-categories', [App\Http\Controllers\Api\V1\Public\ListingController::class, 'categories']);
    
    Route::get('/articles', [App\Http\Controllers\Api\V1\Public\ArticleController::class, 'index']);
    Route::get('/articles/{slug}', [App\Http\Controllers\Api\V1\Public\ArticleController::class, 'show']);
    Route::get('/article-categories', [App\Http\Controllers\Api\V1\Public\ArticleController::class, 'categories']);

    Route::get('/pages/{slug}', [App\Http\Controllers\Api\V1\Public\PageController::class, 'show']);
    
    Route::get('/simulator/kemampuan', [App\Http\Controllers\Api\V1\Public\SimulatorController::class, 'kemampuan']);
    Route::get('/simulator/kpr', [App\Http\Controllers\Api\V1\Public\SimulatorController::class, 'kpr']);

    // Authenticated Routes (User)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [App\Http\Controllers\Api\V1\AuthController::class, 'logout']);
        Route::get('/me', [App\Http\Controllers\Api\V1\AuthController::class, 'me']);
        
        // User Profile
        Route::get('/user/profile', [App\Http\Controllers\Api\V1\User\ProfileController::class, 'show']);
        Route::put('/user/profile', [App\Http\Controllers\Api\V1\User\ProfileController::class, 'update']);
        Route::post('/user/profile/avatar', [App\Http\Controllers\Api\V1\User\ProfileController::class, 'updateAvatar']);

        // User Listings (Iklan Saya)
        Route::get('/user/listings', [App\Http\Controllers\Api\V1\User\ListingController::class, 'index']);
        Route::post('/user/listings', [App\Http\Controllers\Api\V1\User\ListingController::class, 'store']);
        Route::get('/user/listings/{id}', [App\Http\Controllers\Api\V1\User\ListingController::class, 'show']);
        Route::put('/user/listings/{id}', [App\Http\Controllers\Api\V1\User\ListingController::class, 'update']);
        Route::delete('/user/listings/{id}', [App\Http\Controllers\Api\V1\User\ListingController::class, 'destroy']);
        
        // Listing Promotions
        Route::get('/user/listings/{id}/promotions/packages', [App\Http\Controllers\Api\V1\User\ListingPromotionController::class, 'packages']);
        Route::post('/user/listings/{id}/promotions/checkout/{package_id}', [App\Http\Controllers\Api\V1\User\ListingPromotionController::class, 'checkout']);
        Route::post('/user/listing-promotions/{transaction_id}/upload-proof', [App\Http\Controllers\Api\V1\User\ListingPromotionController::class, 'uploadProof']);
        
        // Favorites
        Route::get('/user/favorites', [App\Http\Controllers\Api\V1\User\FavoriteController::class, 'index']);
        Route::post('/user/favorites/{listing_id}', [App\Http\Controllers\Api\V1\User\FavoriteController::class, 'store']);
        Route::delete('/user/favorites/{listing_id}', [App\Http\Controllers\Api\V1\User\FavoriteController::class, 'destroy']);

        // Topup / Wallet
        Route::get('/user/topup-packages', [App\Http\Controllers\Api\V1\User\TopupController::class, 'packages']);
        Route::post('/user/topup/checkout/{package_id}', [App\Http\Controllers\Api\V1\User\TopupController::class, 'checkout']);
        Route::post('/user/topup/upload-proof/{transaction_id}', [App\Http\Controllers\Api\V1\User\TopupController::class, 'uploadProof']);
        Route::get('/user/transactions', [App\Http\Controllers\Api\V1\User\TopupController::class, 'transactions']);

        // Notifications
        Route::get('/user/notifications', [App\Http\Controllers\Api\V1\User\NotificationController::class, 'index']);
        Route::post('/user/notifications/{id}/read', [App\Http\Controllers\Api\V1\User\NotificationController::class, 'markAsRead']);

        // Partner Routes
        Route::middleware('role:partner')->prefix('partner')->group(function () {
            Route::get('/leads', [App\Http\Controllers\Api\V1\Partner\LeadController::class, 'index']);
            Route::get('/leads/{id}', [App\Http\Controllers\Api\V1\Partner\LeadController::class, 'show']);
            Route::post('/leads/{id}/activity', [App\Http\Controllers\Api\V1\Partner\LeadController::class, 'addActivity']);
            
            Route::get('/surveys', [App\Http\Controllers\Api\V1\Partner\SurveyController::class, 'index']);
            Route::post('/surveys/{id}/status', [App\Http\Controllers\Api\V1\Partner\SurveyController::class, 'updateStatus']);
            
            Route::get('/statistics', [App\Http\Controllers\Api\V1\Partner\StatisticController::class, 'index']);
            
            Route::get('/subscription-packages', [App\Http\Controllers\Api\V1\Partner\SubscriptionController::class, 'packages']);
            Route::post('/subscription/checkout/{package_id}', [App\Http\Controllers\Api\V1\Partner\SubscriptionController::class, 'checkout']);
            Route::post('/subscription/upload-proof/{transaction_id}', [App\Http\Controllers\Api\V1\Partner\SubscriptionController::class, 'uploadProof']);
            Route::get('/billing', [App\Http\Controllers\Api\V1\Partner\SubscriptionController::class, 'history']);
        });

        // Admin Routes
        Route::middleware('role:admin|site_moderator')->prefix('admin')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\Api\V1\Admin\DashboardController::class, 'index']);
            
            Route::get('/users', [App\Http\Controllers\Api\V1\Admin\UserController::class, 'index']);
            Route::get('/users/{id}', [App\Http\Controllers\Api\V1\Admin\UserController::class, 'show']);
            Route::post('/users/{id}/toggle-quota', [App\Http\Controllers\Api\V1\Admin\UserController::class, 'toggleQuota']);
            
            Route::get('/listings', [App\Http\Controllers\Api\V1\Admin\ListingController::class, 'index']);
            Route::post('/listings/{id}/approve', [App\Http\Controllers\Api\V1\Admin\ListingController::class, 'approve']);
            Route::post('/listings/{id}/reject', [App\Http\Controllers\Api\V1\Admin\ListingController::class, 'reject']);
            
            Route::get('/topups', [App\Http\Controllers\Api\V1\Admin\TopupController::class, 'index']);
            Route::post('/topups/{id}/approve', [App\Http\Controllers\Api\V1\Admin\TopupController::class, 'approve']);
            Route::post('/topups/{id}/reject', [App\Http\Controllers\Api\V1\Admin\TopupController::class, 'reject']);
        });
    });
});
