<?php

// ===== MAIL DIAGNOSTIC v2 - PARTNER TRACE =====
Route::get('/bw-mail-diag', function () {
    if (request()->get('token') !== 'bwdiag2026') {
        abort(403);
    }

    $results = [];
    $testTo = request()->get('email', config('mail.from.address'));

    $results[] = "=== MAIL CONFIG ===";
    $results[] = "MAILER:      " . config('mail.default');
    $results[] = "HOST:        " . config('mail.mailers.smtp.host');
    $results[] = "PORT:        " . config('mail.mailers.smtp.port');
    $results[] = "USERNAME:    " . config('mail.mailers.smtp.username');
    $results[] = "ENCRYPTION:  " . config('mail.mailers.smtp.encryption');
    $results[] = "FROM:        " . config('mail.from.address');
    $results[] = "QUEUE_CONN:  " . config('queue.default');
    $results[] = "";

    // ===== CEK 5 ORDER TERBARU =====
    $results[] = "=== 5 ORDER TERBARU ===";
    $orders = App\Models\Order::latest()->take(5)->get();
    foreach ($orders as $o) {
        $results[] = "  [{$o->id}] {$o->invoice_number} | type={$o->type} | product_id={$o->product_id} | customer={$o->customer_email}";
    }
    $results[] = "";

    // ===== TRACE PARTNER RESOLUTION PER ORDER =====
    $results[] = "=== TRACE PARTNER RESOLUTION (5 ORDER TERBARU) ===";
    $payoutService = app(App\Services\PartnerPayoutService::class);
    foreach ($orders as $o) {
        $results[] = "--- Order {$o->invoice_number} (type={$o->type}, product_id={$o->product_id}) ---";
        $partnerId = $payoutService->resolvePartnerIdFromOrder($o);
        $results[] = "  resolvePartnerIdFromOrder => " . ($partnerId ?: 'NULL (tidak ada partner!)');
        if ($partnerId) {
            $partner = App\Models\User::find($partnerId);
            if ($partner) {
                $results[] = "  Partner: id={$partner->id}, name={$partner->name}, email={$partner->email}";
                if ($partner->email === $o->customer_email) {
                    $results[] = "  >>> SKIP: email partner = email customer, tidak dikirim!";
                } else {
                    $results[] = "  >>> Email AKAN dikirim ke: {$partner->email}";
                }
            } else {
                $results[] = "  ERROR: partner_id={$partnerId} tapi user TIDAK ADA di DB!";
            }
        } else {
            // cek produk langsung
            if ($o->type === 'restoran') {
                $pkg = App\Models\RestoranPackage::find($o->product_id);
                if ($pkg) {
                    $results[] = "  RestoranPackage id={$o->product_id}: created_by_partner_id=" . ($pkg->created_by_partner_id ?: 'NULL (produk dibuat oleh admin, bukan partner!)');
                } else {
                    $results[] = "  RestoranPackage id={$o->product_id}: TIDAK DITEMUKAN!";
                }
            } elseif ($o->type === 'hotel') {
                $pkg = App\Models\HotelPackage::find($o->product_id);
                if ($pkg) {
                    $results[] = "  HotelPackage id={$o->product_id}: created_by_partner_id=" . ($pkg->created_by_partner_id ?: 'NULL (produk dibuat oleh admin, bukan partner!)');
                } else {
                    $results[] = "  HotelPackage id={$o->product_id}: TIDAK DITEMUKAN!";
                }
            } elseif ($o->type === 'tour') {
                $pkg = App\Models\TourPackage::find($o->product_id);
                if ($pkg) {
                    $results[] = "  TourPackage id={$o->product_id}: created_by_partner_id=" . ($pkg->created_by_partner_id ?: 'NULL (produk dibuat oleh admin, bukan partner!)');
                } else {
                    $results[] = "  TourPackage id={$o->product_id}: TIDAK DITEMUKAN!";
                }
            } elseif ($o->type === 'rent_car') {
                $pkg = App\Models\RentCarPackage::find($o->product_id);
                if ($pkg) {
                    $results[] = "  RentCarPackage id={$o->product_id}: created_by_partner_id=" . ($pkg->created_by_partner_id ?: 'NULL (produk dibuat oleh admin, bukan partner!)');
                } else {
                    $results[] = "  RentCarPackage id={$o->product_id}: TIDAK DITEMUKAN!";
                }
            }
        }
    }
    $results[] = "";

    // ===== CEK SEMUA PARTNER USER =====
    $results[] = "=== PARTNER USERS DI DATABASE ===";
    try {
        $partners = App\Models\User::where('role', 'partner')->get(['id','name','email','role']);
        if ($partners->isEmpty()) {
            $results[] = "TIDAK ADA user dengan role=partner!";
        } else {
            foreach ($partners as $p) {
                // hitung berapa produk mereka
                $rCount = App\Models\RestoranPackage::where('created_by_partner_id', $p->id)->count();
                $hCount = App\Models\HotelPackage::where('created_by_partner_id', $p->id)->count();
                $tCount = App\Models\TourPackage::where('created_by_partner_id', $p->id)->count();
                $results[] = "  id={$p->id} | {$p->name} | {$p->email} | restoran={$rCount} hotel={$hCount} tour={$tCount}";
            }
        }
    } catch (\Throwable $e) {
        $results[] = "ERROR: " . $e->getMessage();
    }
    $results[] = "";

    // ===== TEST KIRIM PARTNER MAILABLE LANGSUNG =====
    $results[] = "=== TEST KIRIM PARTNER EMAIL KE: {$testTo} ===";
    try {
        $order = App\Models\Order::latest()->first();
        $order->loadMissing('payments');
        $fakePartner = App\Models\User::first();
        Illuminate\Support\Facades\Mail::to($testTo)->send(new App\Mail\PartnerOrderInvoiceMail($order, $fakePartner));
        $results[] = "STATUS: >>> BERHASIL KIRIM! <<<";
    } catch (\Throwable $e) {
        $results[] = "ERROR: " . $e->getMessage();
        $results[] = "File: " . basename($e->getFile()) . " line " . $e->getLine();
    }

    return response(implode("\n", $results), 200, ['Content-Type' => 'text/plain; charset=utf-8']);
});
// ===== END MAIL DIAGNOSTIC =====
