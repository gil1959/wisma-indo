<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifikasi Order {{ $order->invoice_number }}</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f8fafc; padding:24px;">
  <div style="max-width: 760px; margin: 0 auto; background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden;">
    <div style="padding:18px 20px; background:#0194F3; color:#ffffff;">
      <div style="font-size:18px; font-weight:bold;">Bintang Wisata - Partner</div>
      <div style="font-size:12px; opacity:0.9;">
        Invoice: {{ $order->invoice_number }}
        <span style="opacity:0.75;">•</span>
        Tanggal: {{ optional($order->created_at)->format('d/m/Y H:i') }}
      </div>
    </div>

    <div style="padding:20px; color:#0f172a;">
      <p style="margin:0 0 12px; padding:10px 12px; background:#fff7ed; border:1px solid #fed7aa; border-radius:12px;">
        Ini adalah notifikasi order untuk partner penyelenggara.
      </p>

      <p style="margin:0 0 12px;">Halo <b>{{ $partner->name }}</b>,</p>
      <p style="margin:0 0 16px;">Terdapat pesanan baru untuk paket Anda: <b>{{ $order->product_name }}</b>.</p>

      @include('emails.partials.order_full_detail', ['order' => $order])

      <div style="margin-top:18px; padding:12px 14px; background:#f1f5f9; border-radius:12px;">
        <div style="font-size:12px; color:#475569;">Status Pembayaran Saat Ini</div>
        <div style="font-weight:bold;">
          @php
            $paymentLabel = match($order->payment_status) {
              'waiting_payment' => 'Menunggu Pembayaran',
              'waiting_verification' => 'Menunggu Verifikasi',
              'paid' => 'Lunas',
              'failed' => 'Gagal',
              default => $order->payment_status
            };
          @endphp
          {{ $paymentLabel }}
        </div>
      </div>

      <p style="margin:18px 0 0; font-size:12px; color:#64748b;">
        Email ini dikirim otomatis. Jika ada pertanyaan, hubungi tim admin Bintang Wisata.
      </p>
    </div>
  </div>
</body>
</html>
