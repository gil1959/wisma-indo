@extends('user.layouts.app')
@php $isEn = app()->getLocale() === 'en'; @endphp

@section('title', $isEn ? 'Order Details' : 'Detail Order')
@section('page-title', $isEn ? 'Order Details' : 'Detail Order')
@section('page-subtitle', $isEn ? 'Full transaction information' : 'Informasi lengkap transaksi kamu')


@section('content')
<div class="space-y-5">

    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">
                Invoice <span style="color:#0194F3;">{{ $order->invoice_number }}</span>
            </h2>
            <p class="mt-1 text-sm text-slate-600">
                {{ $isEn ? 'Created:' : 'Dibuat:' }} <span class="font-semibold">{{ optional($order->created_at)->format('d M Y H:i') }}</span>
            </p>
        </div>

        <div class="flex flex-wrap gap-2 items-center">
            @include('user.partials.order-status-badge', ['status' => $order->order_status])
            @include('user.partials.payment-status-badge', ['status' => $order->payment_status])

            <a href="{{ route('user.orders') }}" class="btn btn-ghost px-4 py-2.5">
                <i data-lucide="arrow-left" class="w-4 h-4" style="color:#0194F3;"></i>
                Back
            </a>



            @if($order->payment_status === 'waiting_payment')
            <a href="{{ route('checkout.show', $order->id) }}"
                class="btn btn-primary px-4 py-2.5">
                <i data-lucide="credit-card" class="w-4 h-4"></i>
                {{ $isEn ? 'Pay Now' : 'Bayar Sekarang' }}

            </a>
            @endif

            @php
            $partnerUser = \App\Support\OrderPartnerResolver::resolvePartnerUser($order);
            $label = ($partnerUser && !empty($partnerUser->phone))
            ? ($isEn ? 'Contact Admin' : 'Hubungi Admin')
            : ($isEn ? 'Confirm to Admin' : 'Konfirmasi ke Admin');
            @endphp


            <a href="{{ route('user.orders.confirm-admin', $order) }}"
                target="_blank"
                class="btn btn-gateway px-4 py-2.5">
                <i data-lucide="message-circle" class="w-4 h-4"></i>
                {{ $label }}
            </a>


        </div>



    </div>
</div>

<div class="grid grid-cols-1 2xl:grid-cols-12 gap-5">

    <div class="2xl:col-span-7 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-sm font-extrabold text-slate-900">{{ $isEn ? 'Order Information' : 'Informasi Pesanan' }}</h2>
            <span class="text-xs font-extrabold px-3 py-1 rounded-full border shrink-0"
                style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                {{ strtoupper($order->type ?? '-') }}
            </span>
        </div>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Product' : 'Produk' }}</div>
                <div class="mt-1 font-semibold text-slate-900">{{ $order->product_name ?? '-' }}</div>
            </div>

            <div>
                <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Customer' : 'Customer' }}</div>
                <div class="mt-1 font-semibold text-slate-900">{{ $order->customer_name ?? '-' }}</div>
                <div class="text-xs text-slate-500 mt-0.5">{{ $order->customer_email ?? '-' }}</div>
                <div class="text-xs text-slate-500">{{ $order->customer_phone ?? '-' }}</div>
            </div>

            <div class="sm:col-span-2">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="rounded-2xl border border-slate-200 p-4 bg-white">
                        <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Subtotal' : 'Subtotal' }}</div>
                        <div class="mt-1 font-extrabold text-slate-900">Rp {{ number_format($order->subtotal ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 p-4 bg-white">
                        <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Discount' : 'Diskon' }}</div>
                        <div class="mt-1 font-extrabold text-slate-900">Rp {{ number_format($order->discount ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 p-4 bg-white">
                        <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Final Price' : 'Harga Akhir' }}</div>
                        <div class="mt-1 font-extrabold" style="color:#0194F3;">
                            Rp {{ number_format($order->final_price ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            @if($order->type === 'tour')
            <div>
                <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Departure' : 'Keberangkatan' }}</div>
                <div class="mt-1 font-semibold text-slate-900">
                    {{ $order->departure_date ? \Carbon\Carbon::parse($order->departure_date)->format('d M Y') : '-' }}
                </div>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Participants' : 'Peserta' }}</div>
                <div class="mt-1 font-semibold text-slate-900">{{ $order->participants ?? '-' }}</div>
            </div>
            @endif

            @if($order->type === 'rent_car')
            <div>
                <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Pickup Date' : 'Tanggal Ambil' }}</div>
                <div class="mt-1 font-semibold text-slate-900">
                    {{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('d M Y') : '-' }}
                </div>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Return Date' : 'Tanggal Kembali' }}</div>
                <div class="mt-1 font-semibold text-slate-900">
                    {{ $order->return_date ? \Carbon\Carbon::parse($order->return_date)->format('d M Y') : '-' }}
                </div>
            </div>
            @endif

            @if($order->type === 'restoran')
            <div>
                <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Reservation Date & Time' : 'Tanggal & Jam Reservasi' }}</div>
                <div class="mt-1 font-semibold text-slate-900">
                    {{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('d M Y, H:i') : '-' }}
                </div>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Participants' : 'Jumlah Peserta' }}</div>
                <div class="mt-1 font-semibold text-slate-900">{{ $order->participants ?? '-' }}</div>
            </div>
            @endif

            @if($order->type === 'hotel')
            <div>
                <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Check-in Date' : 'Tanggal Check-in' }}</div>
                <div class="mt-1 font-semibold text-slate-900">
                    {{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('d M Y') : '-' }}
                </div>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Check-out Date' : 'Tanggal Check-out' }}</div>
                <div class="mt-1 font-semibold text-slate-900">
                    {{ $order->return_date ? \Carbon\Carbon::parse($order->return_date)->format('d M Y') : '-' }}
                </div>
            </div>
            <div class="sm:col-span-2">
                <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Duration' : 'Durasi' }}</div>
                <div class="mt-1 font-semibold text-slate-900">
                    {{ $order->total_days ? $order->total_days . ($isEn ? ' nights' : ' malam') : '-' }}
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="2xl:col-span-5 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-sm font-extrabold text-slate-900">{{ $isEn ? 'Payment History' : 'Riwayat Pembayaran' }}</h2>
            <a href="{{ route('user.orders') }}"
                class="text-xs font-extrabold hover:underline decoration-[#0194F3]"
                style="color:#0194F3;">
                Orders →
            </a>
        </div>

        <div class="mt-4">
            @if(($order->payments ?? collect())->count() === 0)
            <div class="text-sm text-slate-500">{{ $isEn ? 'No payment data yet.' : 'Belum ada data pembayaran.' }}</div>
            @else
            <div class="space-y-3">
                @foreach($order->payments as $p)
                <div class="rounded-2xl border border-slate-200 p-4 bg-white">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="font-semibold text-slate-900">
                                {{ strtoupper($p->method ?? '-') }}
                                <span class="text-slate-400">•</span>
                                Rp {{ number_format($p->amount ?? 0, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                {{ optional($p->created_at)->format('d M Y H:i') }}
                            </div>
                        </div>

                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-extrabold border
                                            {{ ($p->status === 'paid') ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-700 border-slate-200' }}">
                            {{ $p->status ?? '-' }}
                        </span>
                    </div>

                    @if(!empty($p->payment_url))
                    <div class="mt-3">
                        <a href="{{ $p->payment_url }}" target="_blank" class="btn btn-gateway px-4 py-2.5">
                            <i data-lucide="external-link" class="w-4 h-4"></i>
                            {{ $isEn ? 'Continue Payment' : 'Lanjutkan Pembayaran' }}
                        </a>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div class="mt-4 text-xs text-slate-500">
            Jika status “Waiting Payment”, lanjutkan via tombol gateway (jika tersedia).
        </div>
    </div>

</div>
</div>
<div class="mt-10">
    <a href="{{ route('user.orders.invoice.print', $order) }}" class="btn btn-primary px-4 py-2.5">
        <i data-lucide="printer" class="w-4 h-4"></i>
        {{ $isEn ? 'Print Invoice' : 'Cetak Invoice' }}
    </a>
</div>


@endsection