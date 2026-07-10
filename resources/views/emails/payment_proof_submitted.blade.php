<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bukti Pembayaran - {{ $order->invoice_number }}</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; background:#f8fafc; padding:24px;">
  <div style="max-width: 760px; margin: 0 auto; background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden;">

    <div style="padding:18px 20px; background:#0f172a; color:#ffffff;">
      <div style="font-size:18px; font-weight:bold;">Bintang Wisata</div>
      <div style="font-size:12px; opacity:0.9;">
        Invoice: {{ $order->invoice_number }}
        <span style="opacity:0.75;">•</span>
        Tanggal: {{ optional($order->created_at)->format('d/m/Y H:i') }}
      </div>
    </div>

    <div style="padding:20px; color:#0f172a;">

      @php
      $paymentLabel = match($order->payment_status) {
      'waiting_payment' => 'Menunggu Pembayaran',
      'waiting_verification' => 'Menunggu Verifikasi',
      'paid' => 'Lunas',
      'failed' => 'Gagal',
      default => $order->payment_status
      };

      $orderLabel = match($order->order_status) {
      'pending' => 'Pending',
      'approved' => 'Approved',
      'rejected' => 'Rejected',
      default => $order->order_status
      };

      $typeLabel = $order->type === 'tour' ? 'Tour' : 'Rent Car';

      $departure = $order->departure_date ? $order->departure_date->translatedFormat('d F Y') : '-';
      $pickup = $order->pickup_date ? $order->pickup_date->translatedFormat('d F Y H:i') : '-';
      $return = $order->return_date ? $order->return_date->translatedFormat('d F Y H:i') : '-';

      // payment terakhir buat tombol bukti/link bayar
      $latestPayment = $order->payments?->sortByDesc('id')->first();
      @endphp

      @if($isAdminCopy)
      <p style="margin:0 0 12px; padding:10px 12px; background:#fff7ed; border:1px solid #fed7aa; border-radius:12px;">
        Ini notifikasi untuk admin: user sudah melakukan pembayaran / upload bukti.
      </p>
      @endif

      <p style="margin:0 0 12px;">
        Halo <b>{{ $isAdminCopy ? 'Admin' : $order->customer_name }}</b>,
      </p>

      <p style="margin:0 0 16px;">
        Pembayaran untuk invoice <b>{{ $order->invoice_number }}</b> sudah diproses.
        Status saat ini: <b>{{ $paymentLabel }}</b>.
      </p>

      {{-- Tombol bukti pembayaran TERBARU (biar admin/customer langsung nemu) --}}
      @if($latestPayment && $latestPayment->proof_image)
      <div style="margin:0 0 14px; padding:12px 14px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px;">
        <div style="font-size:12px; color:#475569; margin-bottom:6px;">Bukti Pembayaran (Terbaru)</div>
        <a href="{{ url('storage/'.$latestPayment->proof_image) }}"
          target="_blank"
          style="display:inline-block; padding:10px 12px; border:1px solid #e2e8f0; border-radius:12px; text-decoration:none; font-weight:bold; font-size:13px; color:#0f172a;">
          Lihat Bukti Pembayaran
        </a>
      </div>
      @endif

      {{-- Tombol link bayar (kalau payment gateway) --}}
      @if($latestPayment && $latestPayment->payment_url)
      <div style="margin:0 0 14px; padding:12px 14px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px;">
        <div style="font-size:12px; color:#475569; margin-bottom:6px;">Link Pembayaran</div>
        <a href="{{ $latestPayment->payment_url }}"
          target="_blank"
          style="display:inline-block; padding:10px 12px; border:1px solid #e2e8f0; border-radius:12px; text-decoration:none; font-weight:bold; font-size:13px; color:#0f172a;">
          Buka Link Pembayaran
        </a>
      </div>
      @endif

      {{-- INFO PESANAN --}}
      <div style="border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; margin-bottom:14px;">
        <div style="padding:12px 14px; background:#f1f5f9; font-weight:bold;">
          Info Pesanan
        </div>

        <div style="padding:14px;">
          <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
            <tr>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; width:38%; color:#475569;">Produk</td>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;"><b>{{ $order->product_name }}</b></td>
            </tr>

            <tr>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; color:#475569;">Tipe</td>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">{{ $typeLabel }}</td>
            </tr>

            <tr>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; color:#475569;">Customer</td>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">
                <b>{{ $order->customer_name }}</b><br>
                <span style="font-size:12px; color:#64748b;">{{ $order->customer_email }}</span>
              </td>
            </tr>

            <tr>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; color:#475569;">Telepon</td>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">{{ $order->customer_phone }}</td>
            </tr>

            <tr>
              <td style="padding:8px 0; color:#475569;">Status</td>
              <td style="padding:8px 0;">
                <span style="display:inline-block; padding:6px 10px; border:1px solid #e2e8f0; border-radius:999px; font-size:12px; font-weight:bold;">
                  Payment: {{ $paymentLabel }}
                </span>
                <span style="display:inline-block; padding:6px 10px; border:1px solid #e2e8f0; border-radius:999px; font-size:12px; font-weight:bold; margin-left:6px;">
                  Order: {{ $orderLabel }}
                </span>
              </td>
            </tr>
          </table>
        </div>
      </div>

      {{-- JADWAL / TANGGAL --}}
      <div style="border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; margin-bottom:14px;">
        <div style="padding:12px 14px; background:#f1f5f9; font-weight:bold;">
          Jadwal / Tanggal
        </div>

        <div style="padding:14px;">
          <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
            @if($order->type === 'tour' || $order->type === 'umrah')
            <tr>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; width:38%; color:#475569;">
                {{ $order->type === 'umrah' ? 'Tanggal Booking' : 'Keberangkatan' }}
              </td>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">{{ $departure }}</td>
            </tr>
            <tr>
              <td style="padding:8px 0; color:#475569;">Partisipan</td>
              <td style="padding:8px 0;">
                {{ $order->participants ? number_format($order->participants,0,',','.') . ' orang' : '-' }}
              </td>
            </tr>
            @elseif($order->type === 'rent_car')
            <tr>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; width:38%; color:#475569;">Pickup</td>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">{{ $pickup }}</td>
            </tr>
            <tr>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; color:#475569;">Return</td>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">{{ $return }}</td>
            </tr>
            <tr>
              <td style="padding:8px 0; color:#475569;">Durasi</td>
              <td style="padding:8px 0;">
                {{ $order->total_hours ? $order->total_hours . ($isEn ? ' hours' : ' jam') : ($order->total_days ? $order->total_days . ($isEn ? ' days' : ' hari') : '-') }}
              </td>
            </tr>
            @endif

            @if($order->type === 'ship')
            <tr>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">Tanggal Sewa</td>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">
                {{ $order->departure_date ? $order->departure_date->format('d M Y') : '-' }}
              </td>
            </tr>
            <tr>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">Qty</td>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">
                {{ $order->participants ?? 1 }}
              </td>
            </tr>
            @endif

          </table>
        </div>
      </div>

      {{-- RINGKASAN PEMBAYARAN --}}
      <div style="border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; margin-bottom:14px;">
        <div style="padding:12px 14px; background:#f1f5f9; font-weight:bold;">
          Ringkasan Pembayaran
        </div>

        <div style="padding:14px;">
          <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
            <tr>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; width:38%; color:#475569;">Subtotal</td>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; color:#475569;">Diskon</td>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
            </tr>

            @if(!is_null($order->unique_code))
            <tr>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; color:#475569;">Kode Unik</td>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">{{ $order->unique_code }}</td>
            </tr>
            @endif

            @if(!is_null($order->payable_amount))
            <tr>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; color:#475569;">Total Tagihan</td>
              <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;"><b>Rp {{ number_format($order->payable_amount, 0, ',', '.') }}</b></td>
            </tr>
            @endif

            <tr>
              <td style="padding:8px 0; color:#475569;">Total</td>
              <td style="padding:8px 0; font-size:16px;"><b>Rp {{ number_format($order->final_price, 0, ',', '.') }}</b></td>
            </tr>

            <tr>
              <td style="padding:8px 0; color:#475569;">Metode Pembayaran</td>
              <td style="padding:8px 0;">{{ $order->payment_method ?: '-' }}</td>
            </tr>
          </table>
        </div>
      </div>

      {{-- RIWAYAT PAYMENT --}}
      <div style="border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; margin-bottom:14px;">
        <div style="padding:12px 14px; background:#f1f5f9; font-weight:bold;">
          Riwayat Payment
        </div>

        <div style="padding:14px;">
          @if($order->payments && $order->payments->count())
          @foreach($order->payments->sortByDesc('id') as $pay)
          <div style="border:1px solid #e2e8f0; border-radius:12px; padding:12px; margin-bottom:10px;">
            <div style="font-size:13px; color:#0f172a;">
              <div><b>Metode:</b> {{ $pay->method }}</div>
              <div><b>Amount:</b> Rp {{ number_format($pay->amount,0,',','.') }}</div>
              <div><b>Status:</b> {{ $pay->status }}</div>

              @if($pay->gateway_name)
              <div style="font-size:12px; color:#475569; margin-top:4px;">
                <b>Gateway:</b> {{ $pay->gateway_name }}
              </div>
              @endif

              @if($pay->gateway_reference)
              <div style="font-size:12px; color:#475569; margin-top:4px;">
                <b>Gateway Ref:</b> {{ $pay->gateway_reference }}
              </div>
              @endif

              <div style="font-size:12px; color:#64748b; margin-top:6px;">
                {{ optional($pay->created_at)->format('d/m/Y H:i') }}
              </div>

              @if($pay->proof_image)
              <div style="margin-top:8px;">
                <a href="{{ url('storage/'.$pay->proof_image) }}"
                  target="_blank"
                  style="display:inline-block; padding:8px 10px; border:1px solid #e2e8f0; border-radius:10px; text-decoration:none; font-weight:bold; font-size:12px; color:#0f172a;">
                  Lihat Bukti
                </a>
              </div>
              @endif

              @if($pay->payment_url)
              <div style="margin-top:8px;">
                <a href="{{ $pay->payment_url }}"
                  target="_blank"
                  style="display:inline-block; padding:8px 10px; border:1px solid #e2e8f0; border-radius:10px; text-decoration:none; font-weight:bold; font-size:12px; color:#0f172a;">
                  Buka Link Pembayaran
                </a>
              </div>
              @endif
            </div>
          </div>
          @endforeach
          @else
          <div style="padding:12px; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:12px; color:#475569; font-size:13px;">
            Belum ada data payment.
          </div>
          @endif
        </div>
      </div>

      <div style="margin-top:18px; padding:12px 14px; background:#f1f5f9; border-radius:12px;">
        <div style="font-size:12px; color:#475569;">Catatan</div>
        <div style="font-size:14px;">
          @if($isAdminCopy)
          Silakan cek dashboard admin untuk memverifikasi pembayaran (jika manual).
          @else
          Jika pembayaran manual, admin akan memeriksa bukti transfer Anda. Setelah diverifikasi, Anda akan menerima email konfirmasi.
          @endif
        </div>
      </div>

      <p style="margin:18px 0 0; font-size:12px; color:#64748b;">
        Email ini dikirim otomatis. Jika ada pertanyaan, balas email ini atau hubungi admin.
      </p>
    </div>
  </div>
</body>

</html>