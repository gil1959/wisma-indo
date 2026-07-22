<?php

/**
 * SCRIPT UNTUK MENJALANKAN MIGRASI DI CPANEL TANPA TERMINAL
 * 
 * PENTING: 
 * 1. Upload file ini ke folder "public" (atau folder utama public_html Anda di cPanel).
 * 2. Buka URL: https://domain-anda.com/cmd-migrate.php
 * 3. SETELAH SELESAI, WAJIB HAPUS FILE INI AGAR TIDAK DIRETAS ORANG LAIN!
 */

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

define('LARAVEL_START', microtime(true));

// Deteksi otomatis posisi folder vendor
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    // Jika file ada di dalam folder public/
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
} elseif (file_exists(__DIR__.'/vendor/autoload.php')) {
    // Jika file dipindah ke root folder (public_html/)
    require __DIR__.'/vendor/autoload.php';
    $app = require_once __DIR__.'/bootstrap/app.php';
} else {
    die("<h2 style='color:red;'>HTTP ERROR 500: Autoloader tidak ditemukan! Pastikan Anda menaruh file ini di folder yang benar.</h2>");
}

$kernel = $app->make(Kernel::class);
$response = $kernel->handle(
    $request = Request::capture()
);

echo "<div style='font-family: sans-serif; max-width: 800px; margin: 40px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>";
echo "<h1 style='color: #0194F3;'>Menjalankan Laravel Migrate...</h1>";

try {
    // Menjalankan php artisan migrate --force (force penting untuk server production)
    Artisan::call('migrate', ['--force' => true]);
    $output = Artisan::output();
    
    echo "<div style='background: #f4f4f4; padding: 15px; border-radius: 5px; font-family: monospace; white-space: pre-wrap;'>";
    echo $output ?: "Nothing to migrate (Database sudah up to date).";
    echo "</div>";
    
    echo "<h2 style='color: green;'>&#10004; Eksekusi Selesai!</h2>";
    echo "<p style='color: red; font-weight: bold; background: #ffebee; padding: 10px; border-radius: 5px;'>PENTING: Tolong segera HAPUS file cmd-migrate.php ini dari cPanel Anda demi keamanan database!</p>";
    
} catch (\Exception $e) {
    echo "<h2 style='color: red;'>&#10008; Terjadi Kesalahan (Error):</h2>";
    echo "<div style='background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; font-family: monospace; white-space: pre-wrap;'>";
    echo $e->getMessage();
    echo "</div>";
}

echo "</div>";
