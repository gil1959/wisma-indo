<?php

use Illuminate\Support\Facades\Route;

// FRONT ROUTES
Route::get('/', [\App\Http\Controllers\Front\HomeController::class, 'index'])->name('home');
Route::get('/dijual', [\App\Http\Controllers\Front\ListingController::class, 'dijual'])->name('dijual');
Route::get('/disewakan', [\App\Http\Controllers\Front\ListingController::class, 'disewakan'])->name('disewakan');
Route::get('/properti', [\App\Http\Controllers\Front\ListingController::class, 'properti'])->name('properti');
Route::get('/barang-dan-jasa', [\App\Http\Controllers\Front\ListingController::class, 'barangJasa'])->name('barangjasa');
Route::get('/simulasi', [\App\Http\Controllers\Front\SimulasiController::class, 'index'])->name('simulasi');
Route::get('/quran', [\App\Http\Controllers\Front\QuranController::class, 'index'])->name('quran');
Route::get('/co-broke', [\App\Http\Controllers\Front\CoBrokeController::class, 'index'])->name('cobroke');
Route::get('/artikel', [\App\Http\Controllers\Front\ArticleController::class, 'index'])->name('articles');
Route::get('/artikel/{slug}', [\App\Http\Controllers\Front\ArticleController::class, 'show'])->name('articles.show');
Route::get('/listing/{slug}', [\App\Http\Controllers\Front\ListingController::class, 'show'])->name('listing.show');
Route::get('/pengguna/{id}', [\App\Http\Controllers\Front\ListingController::class, 'userListings'])->name('user.listings');
Route::get('/simulasi', [\App\Http\Controllers\Front\SimulasiController::class, 'index'])->name('simulasi');
Route::get('/privacy-policy', [\App\Http\Controllers\Front\LegalController::class, 'privacy'])->name('privacy');
Route::get('/terms-conditions', [\App\Http\Controllers\Front\LegalController::class, 'terms'])->name('terms');
Route::get('/contact', [\App\Http\Controllers\Front\LegalController::class, 'contact'])->name('contact');

// Google Auth Routes (Outside auth middleware)
Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback']);

// USER DASHBOARD (Requires Auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/akun', [\App\Http\Controllers\User\DashboardController::class, 'index'])->name('akun');
    Route::get('/iklan-saya', [\App\Http\Controllers\User\ListingController::class, 'index'])->name('iklan.saya');
    Route::get('/iklan-favorit', [\App\Http\Controllers\User\FavoriteController::class, 'index'])->name('iklan.favorit');
    
    // Top Up
    Route::get('/top-up', [\App\Http\Controllers\User\TopupController::class, 'index'])->name('topup');
    Route::get('/top-up/checkout/{package}', [\App\Http\Controllers\User\TopupController::class, 'checkout'])->name('topup.checkout');
    Route::post('/top-up/process/{package}', [\App\Http\Controllers\User\TopupController::class, 'process'])->name('topup.process');
    Route::get('/top-up/waiting/{transaction}', [\App\Http\Controllers\User\TopupController::class, 'waiting'])->name('topup.waiting');
    Route::get('/top-up/upload-proof/{transaction}', [\App\Http\Controllers\User\TopupController::class, 'uploadProof'])->name('topup.upload_proof');
    Route::post('/top-up/upload-proof/{transaction}', [\App\Http\Controllers\User\TopupController::class, 'storeProof'])->name('topup.store_proof');
    
    Route::get('/transaksi', [\App\Http\Controllers\User\TransactionController::class, 'index'])->name('transaksi');
    
    // Listing Promotions (Sundul / Premium)
    Route::get('/promosi-iklan/{listing}/paket', [\App\Http\Controllers\User\ListingPromotionController::class, 'packages'])->name('listing_promotions.packages');
    Route::get('/promosi-iklan/{listing}/checkout/{package}', [\App\Http\Controllers\User\ListingPromotionController::class, 'checkout'])->name('listing_promotions.checkout');
    Route::post('/promosi-iklan/{listing}/process/{package}', [\App\Http\Controllers\User\ListingPromotionController::class, 'process'])->name('listing_promotions.process');
    Route::get('/promosi-iklan/upload-proof/{transaction}', [\App\Http\Controllers\User\ListingPromotionController::class, 'uploadProof'])->name('listing_promotions.upload_proof');
    Route::post('/promosi-iklan/upload-proof/{transaction}', [\App\Http\Controllers\User\ListingPromotionController::class, 'storeProof'])->name('listing_promotions.store_proof');

    // Notifications
    Route::get('/notifikasi', [\App\Http\Controllers\User\NotificationController::class, 'index'])->name('user.notifications.index');
    Route::get('/notifikasi/{id}', [\App\Http\Controllers\User\NotificationController::class, 'show'])->name('user.notifications.show');

    // Verified only
    Route::middleware(['verified'])->group(function () {
        Route::get('/profil/edit', [\App\Http\Controllers\User\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profil/update', [\App\Http\Controllers\User\ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profil/reset-password-link', [\App\Http\Controllers\User\ProfileController::class, 'sendPasswordResetLink'])->name('profile.reset-password-link');
        Route::get('/pasang-iklan', [\App\Http\Controllers\User\ListingController::class, 'create'])->name('pasang.iklan');
        Route::post('/pasang-iklan', [\App\Http\Controllers\User\ListingController::class, 'store'])->name('pasang.iklan.store');
        Route::get('/iklan-saya/{listing}/edit', [\App\Http\Controllers\User\ListingController::class, 'edit'])->name('iklan.saya.edit');
        Route::put('/iklan-saya/{listing}', [\App\Http\Controllers\User\ListingController::class, 'update'])->name('iklan.saya.update');
        Route::delete('/iklan-saya/{listing}', [\App\Http\Controllers\User\ListingController::class, 'destroy'])->name('iklan.saya.destroy');
        Route::post('/iklan-favorit/{listing}', [\App\Http\Controllers\User\FavoriteController::class, 'store'])->name('iklan.favorit.store');
        Route::delete('/iklan-favorit/{listing}', [\App\Http\Controllers\User\FavoriteController::class, 'destroy'])->name('iklan.favorit.destroy');

        // AI Generator Route
        Route::post('/ai/generate', [\App\Http\Controllers\AiController::class, 'generate'])->name('ai.generate');
    });
});

Route::middleware(['auth', \Spatie\Permission\Middleware\RoleMiddleware::class . ':admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Settings
    Route::get('/settings/general', [\App\Http\Controllers\Admin\SettingController::class, 'general'])->name('settings.general');
    Route::post('/settings/general', [\App\Http\Controllers\Admin\SettingController::class, 'saveGeneral'])->name('settings.general.save');
    Route::post('/settings/general/footer-logos', [\App\Http\Controllers\Admin\SettingController::class, 'storeFooterLogo'])->name('settings.footer_logo.store');
    Route::post('/settings/general/footer-logos/{logo}', [\App\Http\Controllers\Admin\SettingController::class, 'destroyFooterLogo'])->name('settings.footer_logo.destroy');
    
    // Popup Widget Settings
    Route::get('/settings/popup', [\App\Http\Controllers\Admin\PopupWidgetController::class, 'edit'])->name('settings.popup.edit');
    Route::post('/settings/popup', [\App\Http\Controllers\Admin\PopupWidgetController::class, 'update'])->name('settings.popup.save');
    
    Route::get('/settings/payment', [\App\Http\Controllers\Admin\SettingController::class, 'payment'])->name('settings.payment');
    Route::post('/settings/payment', [\App\Http\Controllers\Admin\SettingController::class, 'updatePayment'])->name('settings.payment.save');

    // Home Settings
    Route::get('/settings/home', [\App\Http\Controllers\Admin\HomeSettingController::class, 'index'])->name('settings.home');
    Route::post('/settings/home/hero', [\App\Http\Controllers\Admin\HomeSettingController::class, 'updateHero'])->name('settings.home.hero');
    Route::post('/settings/home/texts', [\App\Http\Controllers\Admin\HomeSettingController::class, 'updateSectionTexts'])->name('settings.home.texts');
    Route::post('/settings/home/banners', [\App\Http\Controllers\Admin\HomeSettingController::class, 'storeBanner'])->name('settings.home.banner.store');
    Route::delete('/settings/home/banners/{banner}', [\App\Http\Controllers\Admin\HomeSettingController::class, 'destroyBanner'])->name('settings.home.banner.destroy');
    Route::post('/settings/home/buttons', [\App\Http\Controllers\Admin\HomeSettingController::class, 'storeButton'])->name('settings.home.button.store');
    Route::delete('/settings/home/buttons/{button}', [\App\Http\Controllers\Admin\HomeSettingController::class, 'destroyButton'])->name('settings.home.button.destroy');
    Route::post('/settings/home/locations', [\App\Http\Controllers\Admin\HomeSettingController::class, 'storeLocation'])->name('settings.home.location.store');
    Route::put('/settings/home/locations/{location}', [\App\Http\Controllers\Admin\HomeSettingController::class, 'updateLocation'])->name('settings.home.location.update');
    Route::delete('/settings/home/locations/{location}', [\App\Http\Controllers\Admin\HomeSettingController::class, 'destroyLocation'])->name('settings.home.location.destroy');

    // Admin Profile
    Route::get('/profile/edit', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

    // System Cache
    Route::post('/system/clear-cache', [\App\Http\Controllers\Admin\SystemController::class, 'clearCache'])->name('system.clear-cache');
    
    // Users
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::post('users/{user}/toggle-quota', [\App\Http\Controllers\Admin\UserController::class, 'toggleFreeQuota'])->name('users.toggle_quota');
    
    // Listing Categories
    Route::resource('listing-categories', \App\Http\Controllers\Admin\ListingCategoryController::class)->except(['show']);
    
    // Articles
    Route::resource('article-categories', \App\Http\Controllers\Admin\ArticleCategoryController::class);
    Route::resource('articles', \App\Http\Controllers\Admin\ArticleController::class);
    
    // Notifications
    Route::get('/notifications/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('notifications.create');
    Route::post('/notifications/store', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('notifications.store');

    // Listings
    Route::resource('listings', \App\Http\Controllers\Admin\ListingController::class);
    Route::post('listings/{listing}/approve', [\App\Http\Controllers\Admin\ListingController::class, 'approve'])->name('listings.approve');
    Route::post('listings/{listing}/reject', [\App\Http\Controllers\Admin\ListingController::class, 'reject'])->name('listings.reject');

    // Admin Legal Routes
    Route::get('/legal/privacy', [\App\Http\Controllers\Admin\LegalController::class, 'privacy'])->name('legal.privacy');
    Route::post('/legal/privacy', [\App\Http\Controllers\Admin\LegalController::class, 'updatePrivacy'])->name('legal.privacy.update');
    Route::get('/legal/terms', [\App\Http\Controllers\Admin\LegalController::class, 'terms'])->name('legal.terms');
    Route::post('/legal/terms', [\App\Http\Controllers\Admin\LegalController::class, 'updateTerms'])->name('legal.terms.update');
    Route::get('/legal/contact', [\App\Http\Controllers\Admin\LegalController::class, 'contact'])->name('legal.contact');
    Route::post('/legal/contact', [\App\Http\Controllers\Admin\LegalController::class, 'updateContact'])->name('legal.contact.update');
    
    // Topups
    Route::resource('topups', \App\Http\Controllers\Admin\TopupController::class);
    Route::post('topups/{topup}/approve', [\App\Http\Controllers\Admin\TopupController::class, 'approve'])->name('topups.approve');
    Route::post('topups/{topup}/reject', [\App\Http\Controllers\Admin\TopupController::class, 'reject'])->name('topups.reject');
    Route::resource('topup-packages', \App\Http\Controllers\Admin\TopupPackageController::class);
    Route::resource('offline-payment-methods', \App\Http\Controllers\Admin\OfflinePaymentMethodController::class)->only(['store', 'destroy']);
    
    // Listing Promotions
    Route::resource('listing-promotions', \App\Http\Controllers\Admin\ListingPromotionController::class)->only(['index', 'update', 'destroy']);
});

require __DIR__ . '/auth.php';

// Fallback route for storage files when symlink is not available (common on shared hosting)
Route::get('storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!\Illuminate\Support\Facades\File::exists($filePath)) {
        abort(404);
    }
    $mime = \Illuminate\Support\Facades\File::mimeType($filePath);
    return response()->file($filePath, ['Content-Type' => $mime]);
})->where('path', '.*');
