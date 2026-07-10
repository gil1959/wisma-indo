<?php

/**
 * DIAGNOSTIK EMAIL - jalankan via browser: /test-email-diag
 * HAPUS FILE INI SETELAH SELESAI TEST!
 */

define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain; charset=utf-8');

$results = [];

// 1. Cek config mail
$results[] = "=== MAIL CONFIG ===";
$results[] = "MAILER:      " . config('mail.default');
$results[] = "HOST:        " . config('mail.mailers.smtp.host');
$results[] = "PORT:        " . config('mail.mailers.smtp.port');
$results[] = "USERNAME:    " . config('mail.mailers.smtp.username');
$results[] = "ENCRYPTION:  " . config('mail.mailers.smtp.encryption');
$results[] = "FROM:        " . config('mail.from.address');
$results[] = "QUEUE_CONN:  " . config('queue.default');
$results[] = "";

// 2. Cek admin email dari settings
$results[] = "=== ADMIN EMAIL ===";
try {
    $adminEmail = App\Models\Setting::invoiceAdminEmail();
    $results[] = "Admin email: " . ($adminEmail ?: '(null/kosong!)');
} catch (\Throwable $e) {
    $results[] = "ERROR get admin email: " . $e->getMessage();
}
$results[] = "";

// 3. Test kirim email sederhana
$results[] = "=== TEST KIRIM EMAIL ===";
$testTo = request()->get('email', config('mail.from.address'));
$results[] = "Kirim ke: " . $testTo;

try {
    Illuminate\Support\Facades\Mail::raw(
        'TEST EMAIL dari Bintang Wisata - ' . date('Y-m-d H:i:s') . "\n\nJika email ini masuk, SMTP berfungsi!",
        function ($msg) use ($testTo) {
            $msg->to($testTo)->subject('[TEST] Email Diagnostik Bintang Wisata - ' . date('H:i:s'));
        }
    );
    $results[] = "STATUS: BERHASIL DIKIRIM!";
} catch (\Throwable $e) {
    $results[] = "STATUS: GAGAL!";
    $results[] = "ERROR: " . $e->getMessage();
    $results[] = "CLASS: " . get_class($e);
    if ($e->getPrevious()) {
        $results[] = "CAUSED BY: " . $e->getPrevious()->getMessage();
    }
}
$results[] = "";

// 4. Test order email via Mailable
$results[] = "=== TEST ORDER MAILABLE ===";
try {
    $order = App\Models\Order::latest()->first();
    if ($order) {
        $results[] = "Order found: " . $order->invoice_number . " (" . $order->type . ")";
        $results[] = "Customer email: " . $order->customer_email;

        // coba render dulu tanpa kirim
        $mailable = new App\Mail\OrderInvoiceMail($order, false);
        $rendered = $mailable->render();
        $results[] = "Template render: OK (" . strlen($rendered) . " bytes)";
    } else {
        $results[] = "Tidak ada order di DB";
    }
} catch (\Throwable $e) {
    $results[] = "ERROR Mailable: " . $e->getMessage();
    $results[] = "File: " . $e->getFile() . " line " . $e->getLine();
}

echo implode("\n", $results);
exit;
