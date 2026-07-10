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

// USER DASHBOARD (Requires Auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/akun', [\App\Http\Controllers\User\DashboardController::class, 'index'])->name('akun');
    Route::get('/iklan-saya', [\App\Http\Controllers\User\ListingController::class, 'index'])->name('iklan.saya');
    Route::get('/iklan-favorit', [\App\Http\Controllers\User\FavoriteController::class, 'index'])->name('iklan.favorit');
    Route::get('/top-up', [\App\Http\Controllers\User\TopupController::class, 'index'])->name('topup');
    Route::get('/transaksi', [\App\Http\Controllers\User\TransactionController::class, 'index'])->name('transaksi');
    Route::get('/pasang-iklan', [\App\Http\Controllers\User\ListingController::class, 'create'])->name('pasang.iklan');
});

require __DIR__ . '/auth.php';
