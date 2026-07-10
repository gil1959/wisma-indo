<!doctype html>
@php
$isEn = app()->getLocale() === 'en';

// Logo sama seperti navbar/footer (setting admin)
$logoUrl = $siteSettings['site_logo'] ?? asset('images/logo.png');
$brandName = $siteSettings['seo_site_title'] ?? 'Bintang Wisata';

// Dates
$invoiceDate = optional($order->created_at);
$dueDate = $invoiceDate ? (clone $invoiceDate)->addDays(30) : null; // default

// Amounts
$subtotal = (int) ($order->subtotal ?? 0);
$discount = (int) ($order->discount ?? 0);
$totalDue = (int) ($order->final_price ?? max(0, $subtotal - $discount));

// Qty heuristic sesuai struktur order
$qty = 1;
if (($order->type ?? '') === 'tour' || ($order->type ?? '') === 'umrah' || ($order->type ?? '') === 'restoran') {
    $qty = (int) ($order->participants ?? 1);
} elseif (($order->type ?? '') === 'rent_car' || ($order->type ?? '') === 'hotel') {
    $qty = (int) ($order->total_hours ?? $order->total_days ?? 1);
}
if ($qty <= 0) $qty=1;

    // unit price aman
    $unitPrice=$subtotal;
    $effectiveQty=1;
    if ($qty> 1 && $subtotal > 0 && ($subtotal % $qty) === 0) {
    $unitPrice = (int) ($subtotal / $qty);
    $effectiveQty = $qty;
    }

    // PAYMENT INFO berdasarkan payment_method order
    $paymentRaw = (string) ($order->payment_method ?? '');
    $paymentType = 'unknown';
    $paymentTitle = '-';

    $bankName = null;
    $accountHolder = null;
    $accountNumber = null;

    $gatewayName = null;
    $gatewayLabel = null;
    $channelCode = null;
    $channelName = null;

    if (str_starts_with($paymentRaw, 'manual:')) {
    $paymentType = 'manual';
    $id = (int) substr($paymentRaw, strlen('manual:'));

    $pm = \App\Models\PaymentMethod::query()
    ->where('id', $id)
    ->where('type', 'manual')
    ->first();

    $bankName = $pm->bank_name ?? null;
    $accountHolder = $pm->account_holder ?? null;
    $accountNumber = $pm->account_number ?? null;

    $paymentTitle = 'BANK MANUAL';
    } elseif (str_starts_with($paymentRaw, 'gateway:')) {
    $paymentType = 'gateway';
    $parts = explode(':', $paymentRaw, 3);
    $gatewayName = $parts[1] ?? null;
    $channelCode = $parts[2] ?? null;

    $gw = null;
    if ($gatewayName) {
    $gw = \App\Models\PaymentGateway::query()
    ->where('name', $gatewayName)
    ->first();
    }

    $gatewayLabel = $gw->label ?? ($gatewayName ? strtoupper($gatewayName) : null);

    if ($gw && is_array($gw->channels) && $channelCode) {
    foreach ($gw->channels as $ch) {
    if (($ch['channel_code'] ?? null) === $channelCode) {
    $channelName = $ch['name'] ?? $channelCode;
    break;
    }
    }
    }

    $paymentTitle = 'PAYMENT GATEWAY';
    }

    // Footer contact (kalau setting ada)
    $addr = $siteSettings['footer_address'] ?? '';
    $phone = $siteSettings['footer_phone'] ?? '';
    $email = $siteSettings['footer_email'] ?? '';
    @endphp

    <html lang="{{ $isEn ? 'en' : 'id' }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Invoice {{ $order->invoice_number }}</title>

        <style>
            :root {
                --ink: #0f172a;
                --muted: #64748b;
                --line: #e2e8f0;
                --bg: #f8fafc;
                --blue: #2fa7d8;
                --blue2: #77d4ff;
                --orange: #f59e0b;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: Arial, Helvetica, sans-serif;
                background: var(--bg);
                color: var(--ink);
                padding: 20px;
                /* JANGAN DIUBAH */
            }

            .wrap {
                max-width: 860px;
                /* JANGAN DIUBAH */
                margin: 0 auto;
            }

            /* tombol cetak/back */
            .toolbar {
                display: flex;
                gap: 10px;
                justify-content: flex-end;
                align-items: center;
                margin: 0 0 14px;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 10px 14px;
                border-radius: 12px;
                text-decoration: none;
                font-weight: 700;
                font-size: 13px;
                border: 1px solid var(--line);
                background: #fff;
                color: var(--ink);
            }

            .btn-primary {
                background: var(--blue);
                border-color: var(--blue);
                color: #fff;
            }

            /* Paper */
            .paper {
                position: relative;
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto;
                background: #fff;
                border-radius: 14px;
                overflow: hidden;
                box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
            }

            /* dekor diagonal atas & bawah (biarin sesuai design lu) */
            .paper:before {
                content: "";
                position: absolute;
                top: -170px;
                right: -260px;
                width: 560px;
                height: 220px;
                background: linear-gradient(135deg, var(--blue2), var(--blue));
                transform: rotate(12deg);
            }

            .paper:after {
                content: "";
                position: absolute;
                bottom: -210px;
                left: -260px;
                width: 660px;
                height: 260px;
                background: linear-gradient(135deg, var(--blue), var(--blue2));
                transform: rotate(12deg);
            }

            /* ====== RAPIHIN: padding konten biar gak mepet pinggir ====== */
            .content {
                position: relative;
                z-index: 2;
                padding: 34px 40px 34px;
                /* INI YANG DIRAPIHIN */
            }

            .top {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 18px;
                margin-bottom: 18px;
                /* sedikit lebih lega */
            }

            .logo img {
                height: 56px;
                width: auto;
                object-fit: contain;
                margin-top: 50px;
            }

            .invoice-title {
                font-size: 56px;
                font-weight: 900;
                letter-spacing: 1px;
                margin: 0;
                line-height: 1;
                text-align: right;
                margin-top: 50px;
            }

            .billto {
                margin-top: 14px;
            }

            .billto .label {
                font-size: 13px;
                font-weight: 900;
                letter-spacing: .6px;
                margin: 0 0 6px;
            }

            .billto .name {
                font-weight: 900;
                margin: 0;
                font-size: 14px;
            }

            .billto .text {
                margin: 3px 0;
                font-size: 14px;
            }

            .meta-bar {
                margin: 18px 0 22px;
                /* tambah jarak bawah */
                border: 2px solid #0b1220;
                padding: 12px 12px;
                display: flex;
                justify-content: space-between;
                gap: 12px;
                font-size: 14px;
                font-weight: 900;
                flex-wrap: wrap;
            }

            table.items {
                width: 100%;
                border-collapse: collapse;
            }

            table.items thead th {
                background: var(--blue);
                color: #fff;
                padding: 12px 12px;
                font-size: 13px;
                letter-spacing: .6px;
                text-align: left;
            }

            table.items tbody td {
                padding: 14px 12px;
                border-bottom: 1px solid var(--line);
                font-size: 14px;
                background: #f3f8fe;
            }

            table.items tbody tr+tr td {
                background: #eef6ff;
            }

            .num {
                width: 60px;
                text-align: center;
            }

            .right {
                text-align: right;
            }

            .center {
                text-align: center;
            }

            .totals-wrap {
                display: flex;
                justify-content: flex-end;
                margin-top: 20px;
                /* sedikit lebih lega */
            }

            .totals {
                width: 360px;
                border-collapse: collapse;
            }

            .totals td {
                padding: 16px 14px;
                border-bottom: 1px solid var(--line);
                background: #f3f8fe;
                font-size: 14px;
                font-weight: 900;
            }

            .totals .value {
                text-align: right;
            }

            .totals .grand td {
                background: var(--blue);
                color: #fff;
                font-size: 18px;
                border-bottom: none;
            }

            .bottom {
                display: grid;
                grid-template-columns: 1.2fr .8fr;
                gap: 32px;
                /* lebih rapih */
                margin-top: 34px;
                /* tambah jarak */
            }

            .section-title {
                font-weight: 900;
                letter-spacing: .6px;
                margin: 0 0 10px;
            }

            .terms p {
                margin: 0;
                font-size: 14px;
                line-height: 1.6;
            }

            .pay-row {
                display: flex;
                justify-content: space-between;
                gap: 12px;
                padding: 9px 0;
                /* sedikit lebih rapi */
                border-bottom: 1px solid var(--line);
                font-size: 14px;
            }

            .pay-row:last-child {
                border-bottom: none;
            }

            .pay-key {
                color: var(--muted);
                width: 44%;
            }

            .pay-val {
                font-weight: 900;
                text-align: right;
                width: 56%;
            }

            /* ====== RAPIHIN: footer jangan nempel pinggir bawah ====== */
            .footer {
                margin-top: 36px;
                padding-bottom: 28px;
                /* INI YANG DIRAPIHIN */
                display: flex;
                justify-content: space-between;
                align-items: flex-end;
                gap: 18px;
            }

            .teksterms {
                align-items: center;
                width: 300px;
            }

            .thanks {
                font-weight: 900;
                color: var(--orange);
                letter-spacing: .4px;
                margin-bottom: 40px;
            }

            .contact {
                margin-top: 50px;
                text-align: right;
                color: var(--orange);
                font-size: 13px;
                line-height: 1.6;
            }

            /* ================= PRINT: KUNCI BIAR HASIL SAMA ================= */
            @page {
                size: A4;
                margin: 0;
            }


            @media print {

                html,
                body {
                    background: #fff !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    width: 210mm !important;
                    height: 297mm !important;
                    overflow: hidden !important;
                }

                * {
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }

                .toolbar {
                    display: none !important;
                }

                /* paksa full lebar A4 */
                .wrap {
                    width: 210mm !important;
                    max-width: 210mm !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }

                .paper {
                    width: 210mm !important;
                    min-height: 297mm !important;
                    margin: 0 !important;
                    border: none !important;
                    border-radius: 0 !important;
                    box-shadow: none !important;
                }

                /* kalau lu mau konten mepet pinggir, kecilin paddingnya */
                .content {
                    padding: 22px 22px 22px !important;
                    /* sebelumnya 34px 40px */
                }
            }
        </style>
    </head>

    <body>
        <div class="wrap">

            <div class="toolbar">
                <a href="javascript:window.print()" class="btn btn-primary">{{ $isEn ? 'Print Invoice' : 'Cetak Invoice' }}</a>
                <a href="javascript:history.back()" class="btn">{{ $isEn ? 'Back' : 'Kembali' }}</a>
            </div>

            <div class="paper">
                <div class="content">

                    <div class="top">
                        <div>
                            <div class="logo">
                                <img src="{{ $logoUrl }}" alt="{{ $brandName }}">
                            </div>

                            <div class="billto">
                                <div class="label">BILL TO</div>
                                <p class="name">{{ $order->customer_name ?? '-' }}</p>
                                <p class="text">{{ $order->billing_address ?? '' }}</p>
                                <p class="text">{{ $order->customer_phone ?? '' }}</p>
                            </div>
                        </div>

                        <div>
                            <h1 class="invoice-title">INVOICE</h1>
                        </div>
                    </div>

                    <div class="meta-bar">
                        <div>INVOICE # {{ $order->invoice_number }}</div>
                        <div>INVOICE DATE: {{ $invoiceDate ? $invoiceDate->format('F d, Y') : '-' }}</div>
                        <div>DUE DATE: {{ $dueDate ? $dueDate->format('F d, Y') : '-' }}</div>
                    </div>

                    <table class="items">
                        <thead>
                            <tr>
                                <th class="num">NO</th>
                                <th>DESCRIPTION</th>
                                <th class="right">PRICE</th>
                                <th class="center">QTY</th>
                                <th class="right">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="num">1</td>
                                <td style="background:#f3f8fe;">
                                    <div style="font-weight:900;">{{ $order->product_name ?? 'ITEM/SERVICE' }}</div>
                                </td>
                                <td class="right">Rp {{ number_format($unitPrice, 0, ',', '.') }}</td>
                                <td class="center">{{ $effectiveQty }}</td>
                                <td class="right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>

                            @if($discount > 0)
                            <tr>
                                <td class="num">2</td>
                                <td style="background:#eef6ff; font-weight:900;">DISCOUNT</td>
                                <td class="right">-</td>
                                <td class="center">-</td>
                                <td class="right">- Rp {{ number_format($discount, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>

                    <div class="totals-wrap">
                        <table class="totals">
                            <tr>
                                <td>SUB-TOTAL</td>
                                <td class="value">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="grand">
                                <td>Total Due</td>
                                <td class="value">Rp {{ number_format($totalDue, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="bottom">
                        <div class="terms">
                            <div class="section-title">TERM AND CONDITIONS</div>
                            <div class="teksterms">
                                <p>Please make the payment by the due date to the account below. We accept bank transfer, credit card, or check.</p>
                            </div>

                        </div>

                        <div class="payment">
                            <div class="section-title">PAYMENT METHOD</div>

                            @if($paymentType === 'manual')
                            <div class="pay-row">
                                <div class="pay-key">Bank</div>
                                <div class="pay-val">{{ $bankName ?? '-' }}</div>
                            </div>
                            <div class="pay-row">
                                <div class="pay-key">Account Name</div>
                                <div class="pay-val">{{ $accountHolder ?? '-' }}</div>
                            </div>
                            <div class="pay-row">
                                <div class="pay-key">Account Number</div>
                                <div class="pay-val">{{ $accountNumber ?? '-' }}</div>
                            </div>
                            @elseif($paymentType === 'gateway')
                            <div class="pay-row">
                                <div class="pay-key">Type</div>
                                <div class="pay-val">{{ $paymentTitle }}</div>
                            </div>
                            <div class="pay-row">
                                <div class="pay-key">Gateway</div>
                                <div class="pay-val">{{ $gatewayLabel ?? '-' }}</div>
                            </div>
                            <div class="pay-row">
                                <div class="pay-key">Channel</div>
                                <div class="pay-val">{{ $channelName ?? ($channelCode ?? '-') }}</div>
                            </div>
                            @else
                            <div class="pay-row">
                                <div class="pay-key">Method</div>
                                <div class="pay-val">{{ $paymentRaw ?: '-' }}</div>
                            </div>
                            @endif

                            <div style="margin-top:14px; font-weight:900; text-align:right;">
                                PT. BINTANG WISATA HOLIDAY
                            </div>
                        </div>
                    </div>

                    <div class="footer">
                        <div class="thanks">THANK YOU FOR YOUR BUSINESS</div>

                        <div class="contact">
                            @if($addr)<div>{{ $addr }}</div>@endif
                            @if($phone)<div>{{ $phone }}</div>@endif
                            @if($email)<div>{{ $email }}</div>@endif
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </body>

    </html>