@php
$isEn = app()->getLocale() === 'en';

$paymentLabel = match($order->payment_status) {
'waiting_payment' => ($isEn ? 'Waiting Payment' : 'Menunggu Pembayaran'),
'waiting_verification' => ($isEn ? 'Waiting Verification' : 'Menunggu Verifikasi'),
'paid' => ($isEn ? 'Paid' : 'Lunas'),
'failed' => ($isEn ? 'Failed' : 'Gagal'),
default => $order->payment_status
};

$orderLabel = match($order->order_status) {
'pending' => ($isEn ? 'Pending' : 'Menunggu'),
'approved' => ($isEn ? 'Approved' : 'Disetujui'),
'rejected' => ($isEn ? 'Rejected' : 'Ditolak'),
default => $order->order_status
};

// FIX: include umrah & ship explicitly (sebelumnya umrah kebaca "Sewa Kapal")
$typeLabel = match($order->type) {
'tour' => ($isEn ? 'Tour' : 'Tour'),
'umrah' => ($isEn ? 'Umrah' : 'Umrah'),
'rent_car' => ($isEn ? 'Car Rental' : 'Rental Mobil'),
'ship' => ($isEn ? 'Ship Rental' : 'Sewa Kapal'),
default => (string)($order->type ?? '-'),
};

$departure = $order->departure_date ? \Carbon\Carbon::parse($order->departure_date)->translatedFormat('d F Y') : '-';
$pickup = $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->translatedFormat('d F Y H:i') : '-';
$return = $order->return_date ? \Carbon\Carbon::parse($order->return_date)->translatedFormat('d F Y H:i') : '-';

$latestPayment = $order->payments?->sortByDesc('id')->first();
@endphp


{{-- INFO PESANAN --}}
<div style="border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; margin-bottom:14px;">
  <div style="padding:12px 14px; background:#f1f5f9; font-weight:bold;">
    {{ $isEn ? 'Order Info' : 'Info Pesanan' }}
  </div>

  <div style="padding:14px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
      <tr>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; width:38%; color:#475569;">{{ $isEn ? 'Product' : 'Produk' }}</td>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;"><b>{{ $order->product_name }}</b></td>
      </tr>

      <tr>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; color:#475569;">{{ $isEn ? 'Type' : 'Tipe' }}</td>
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
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; color:#475569;">{{ $isEn ? 'Phone' : 'Telepon' }}</td>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">{{ $order->customer_phone }}</td>
      </tr>

      <tr>
        <td style="padding:8px 0; color:#475569;">{{ $isEn ? 'Status' : 'Status' }}</td>
        <td style="padding:8px 0;">
          <span style="display:inline-block; padding:6px 10px; border:1px solid #e2e8f0; border-radius:999px; font-size:12px; font-weight:bold;">
            {{ $isEn ? 'Payment' : 'Payment' }}: {{ $paymentLabel }}
          </span>
          <span style="display:inline-block; padding:6px 10px; border:1px solid #e2e8f0; border-radius:999px; font-size:12px; font-weight:bold; margin-left:6px;">
            {{ $isEn ? 'Order' : 'Order' }}: {{ $orderLabel }}
          </span>
        </td>
      </tr>
    </table>
  </div>
</div>

{{-- JADWAL / TANGGAL --}}
<div style="border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; margin-bottom:14px;">
  <div style="padding:12px 14px; background:#f1f5f9; font-weight:bold;">
    {{ $isEn ? 'Schedule / Dates' : 'Jadwal / Tanggal' }}
  </div>

  <div style="padding:14px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
      @if($order->type === 'tour' || $order->type === 'umrah')
      <tr>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; width:38%; color:#475569;">
          {{ $order->type === 'umrah'
    ? ($isEn ? 'Booking Date' : 'Tanggal Booking')
    : ($isEn ? 'Departure' : 'Keberangkatan')
}}
        </td>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">{{ $departure }}</td>
      </tr>
      <tr>
        <td style="padding:8px 0; color:#475569;">{{ $isEn ? 'Participants' : 'Partisipan' }}</td>
        <td style="padding:8px 0;">
          {{ $order->participants
    ? number_format($order->participants,0,',','.') . ($isEn ? ' people' : ' orang')
    : '-'
}}
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
        <td style="padding:8px 0; color:#475569;">{{ $isEn ? 'Duration' : 'Durasi' }}</td>
        <td style="padding:8px 0;">
          {{ $order->total_hours ? $order->total_hours . ($isEn ? ' hours' : ' jam') : ($order->total_days ? $order->total_days . ($isEn ? ' days' : ' hari') : '-') }}
        </td>
      </tr>
      @endif

      @if($order->type === 'ship')
      <tr>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">{{ $isEn ? 'Rental Date' : 'Tanggal Sewa' }}</td>
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

      @if($order->type === 'restoran')
      <tr>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; width:38%; color:#475569;">{{ $isEn ? 'Reservation Date & Time' : 'Tanggal & Jam Reservasi' }}</td>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">{{ $pickup }}</td>
      </tr>
      <tr>
        <td style="padding:8px 0; color:#475569;">{{ $isEn ? 'Participants' : 'Partisipan' }}</td>
        <td style="padding:8px 0;">
          {{ $order->participants ? number_format($order->participants,0,',','.') . ($isEn ? ' people' : ' orang') : '-' }}
        </td>
      </tr>
      @endif

      @if($order->type === 'hotel')
      <tr>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; width:38%; color:#475569;">{{ $isEn ? 'Check-in' : 'Check-in' }}</td>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">{{ $pickup }}</td>
      </tr>
      <tr>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; width:38%; color:#475569;">{{ $isEn ? 'Check-out' : 'Check-out' }}</td>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">{{ $return }}</td>
      </tr>
      <tr>
        <td style="padding:8px 0; color:#475569;">{{ $isEn ? 'Duration' : 'Durasi' }}</td>
        <td style="padding:8px 0;">
          {{ $order->total_days ? $order->total_days . ($isEn ? ' nights' : ' malam') : '-' }}
        </td>
      </tr>
      @endif
    </table>
  </div>
</div>

{{-- RINGKASAN PEMBAYARAN --}}
<div style="border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; margin-bottom:14px;">
  <div style="padding:12px 14px; background:#f1f5f9; font-weight:bold;">
    {{ $isEn ? 'Payment Summary' : 'Ringkasan Pembayaran' }}
  </div>

  <div style="padding:14px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
      <tr>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; width:38%; color:#475569;">Subtotal</td>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
      </tr>
      <tr>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0; color:#475569;">{{ $isEn ? 'Discount' : 'Diskon' }}</td>
        <td style="padding:8px 0; border-bottom:1px solid #e2e8f0;">Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
      </tr>
      <tr>
        <td style="padding:8px 0; color:#475569;">Total</td>
        <td style="padding:8px 0; font-size:16px;"><b>Rp {{ number_format($order->final_price, 0, ',', '.') }}</b></td>
      </tr>

      <tr>
        <td style="padding:8px 0; color:#475569;">{{ $isEn ? 'Payment Method' : 'Metode Pembayaran' }}</td>

        <td style="padding:8px 0;">{{ $order->payment_method ?: '-' }}</td>
      </tr>

      @if($latestPayment && $latestPayment->payment_url)
      <tr>
        <td style="padding:8px 0; color:#475569;">{{ $isEn ? 'Payment Link' : 'Link Pembayaran' }}</td>
        <td style="padding:8px 0;">
          <a href="{{ $latestPayment->payment_url }}" target="_blank" style="font-weight:bold; color:#0f172a;">
            {{ $isEn ? 'Open Payment Link' : 'Buka Link Pembayaran' }}
          </a>
        </td>
      </tr>
      @endif
    </table>
  </div>
</div>

{{-- RIWAYAT PAYMENT --}}
<div style="border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; margin-bottom:14px;">
  <div style="padding:12px 14px; background:#f1f5f9; font-weight:bold;">
    {{ $isEn ? 'Payment History' : 'Riwayat Payment' }}
  </div>

  <div style="padding:14px;">
    @if($order->payments && $order->payments->count())
    @foreach($order->payments->sortByDesc('id') as $pay)
    <div style="border:1px solid #e2e8f0; border-radius:12px; padding:12px; margin-bottom:10px;">
      <div style="font-size:13px; color:#0f172a;">
        <div><b>{{ $isEn ? 'Method' : 'Metode' }}:</b> {{ $pay->method }}</div>
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
            {{ $isEn ? 'View Proof' : 'Lihat Bukti' }}
          </a>
        </div>
        @endif
      </div>
    </div>
    @endforeach
    @else
    <div style="padding:12px; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:12px; color:#475569; font-size:13px;">
      {{ $isEn ? 'No payment data yet.' : 'Belum ada data payment.' }}
    </div>
    @endif
  </div>
</div>