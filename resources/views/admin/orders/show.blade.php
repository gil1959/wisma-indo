@extends('layouts.admin')

@section('title', 'Detail Order')
@section('page-title', 'Detail Order')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">
                Detail Order
                <span class="text-azure">{{ $order->invoice_number }}</span>
            </h2>
            <p class="mt-1 text-sm text-slate-600">
                Informasi pesanan, pembayaran, dan tindakan admin.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.orders.index') }}"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white text-slate-800 hover:bg-slate-50 transition">
                <i data-lucide="arrow-left" class="w-4 h-4" style="color:#0194F3;"></i>
                Kembali
            </a>

            <a href="{{ route('admin.orders.invoice.print', $order) }}"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                style="background:#0194F3;"
                onmouseover="this.style.background='#0186DB'"
                onmouseout="this.style.background='#0194F3'">
                <i data-lucide="printer" class="w-4 h-4"></i>
                Cetak Invoice
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
        <div class="font-bold">Berhasil</div>
        <div class="text-sm mt-1">{{ session('success') }}</div>
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800">
        <div class="font-bold">Gagal</div>
        <div class="text-sm mt-1">{{ session('error') }}</div>
    </div>
    @endif

    {{-- Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

        {{-- Left --}}
        <div class="lg:col-span-6 space-y-5">

            {{-- Info Pesanan --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div class="font-extrabold text-slate-900">Info Pesanan</div>
                    <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-extrabold"
                        style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                        <i data-lucide="receipt" class="w-4 h-4" style="color:#0194F3;"></i>
                        {{ strtoupper($order->type) }}
                    </span>
                </div>

                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs font-extrabold text-slate-500">Produk</div>
                            <div class="mt-1 font-bold text-slate-900">{{ $order->product_name }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-extrabold text-slate-500">Customer</div>
                            <div class="mt-1 font-bold text-slate-900">{{ $order->customer_name }}</div>
                            <div class="text-xs text-slate-500">{{ $order->customer_email }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-extrabold text-slate-500">Telepon</div>
                            <div class="mt-1 font-bold text-slate-900">{{ $order->customer_phone }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-extrabold text-slate-500">Status</div>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-extrabold">
                                    Payment: {{ $order->payment_status }}
                                </span>
                                <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-extrabold">
                                    Order: {{ $order->order_status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Jadwal/Tanggal --}}
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4" style="color:#0194F3;"></i>
                            Jadwal / Tanggal
                        </div>

                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-slate-700">

                            @if(in_array($order->type, ['tour','umrah','mice']))
                            <div>
                                <div class="text-xs font-extrabold text-slate-500">
                                    @if($order->type === 'umrah')
                                    Tanggal Booking
                                    @elseif($order->type === 'mice')
                                    Jadwal / Tanggal
                                    @else
                                    Keberangkatan
                                    @endif
                                </div>
                                <div class="mt-1 font-bold text-slate-900">
                                    {{ $order->departure_date ? $order->departure_date->translatedFormat('d F Y') : '-' }}
                                </div>
                            </div>

                            <div>
                                <div class="text-xs font-extrabold text-slate-500">Partisipan</div>
                                <div class="mt-1 font-bold text-slate-900">
                                    {{ $order->participants ? number_format($order->participants,0,',','.') . ' orang' : '-' }}
                                </div>
                            </div>
                            @endif


                            @if($order->type === 'ship')
                            <div>
                                <div class="text-xs font-extrabold text-slate-500">Tanggal Sewa</div>
                                <div class="mt-1 font-bold text-slate-900">
                                    {{ $order->departure_date ? $order->departure_date->translatedFormat('d F Y') : '-' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs font-extrabold text-slate-500">Qty</div>
                                <div class="mt-1 font-bold text-slate-900">
                                    {{ $order->participants ? number_format($order->participants,0,',','.') : 1 }}
                                </div>
                            </div>
                            @endif

                            @if($order->type === 'rent_car')
                            <div>
                                <div class="text-xs font-extrabold text-slate-500">Pickup</div>
                                <div class="mt-1 font-bold text-slate-900">
                                    {{ $order->pickup_date ? $order->pickup_date->translatedFormat('d F Y H:i') : '-' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs font-extrabold text-slate-500">Return</div>
                                <div class="mt-1 font-bold text-slate-900">
                                    {{ $order->return_date ? $order->return_date->translatedFormat('d F Y H:i') : '-' }}
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <div class="text-xs font-extrabold text-slate-500">Durasi</div>
                                <div class="mt-1 font-bold text-slate-900">
                                    {{ $order->total_hours ? $order->total_hours . ' jam' : ($order->total_days ? $order->total_days . ' hari' : '-') }}
                                </div>
                            </div>
                            @endif

                            @if($order->type === 'restoran')
                            <div>
                                <div class="text-xs font-extrabold text-slate-500">Tanggal & Jam Reservasi</div>
                                <div class="mt-1 font-bold text-slate-900">
                                    {{ $order->pickup_date ? $order->pickup_date->translatedFormat('d F Y H:i') : '-' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs font-extrabold text-slate-500">Partisipan</div>
                                <div class="mt-1 font-bold text-slate-900">
                                    {{ $order->participants ? number_format($order->participants,0,',','.') . ' orang' : '-' }}
                                </div>
                            </div>
                            @endif

                            @if($order->type === 'hotel')
                            <div>
                                <div class="text-xs font-extrabold text-slate-500">Check-in</div>
                                <div class="mt-1 font-bold text-slate-900">
                                    {{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->translatedFormat('d F Y') : '-' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs font-extrabold text-slate-500">Check-out</div>
                                <div class="mt-1 font-bold text-slate-900">
                                    {{ $order->return_date ? \Carbon\Carbon::parse($order->return_date)->translatedFormat('d F Y') : '-' }}
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <div class="text-xs font-extrabold text-slate-500">Durasi</div>
                                <div class="mt-1 font-bold text-slate-900">
                                    {{ $order->total_days ? $order->total_days . ' malam' : '-' }}
                                </div>
                            </div>
                            @endif

                        </div>

                    </div>

                    {{-- WhatsApp --}}
                    @php
                    $wa = preg_replace('/\D/', '', $order->customer_phone);
                    $waText = urlencode("Halo {$order->customer_name}, terkait pesanan {$order->invoice_number} di Bintang Wisata.");
                    @endphp

                    @if($wa)
                    <a href="https://wa.me/{{ $wa }}?text={{ $waText }}"
                        target="_blank"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-extrabold text-white transition"
                        style="background:#16a34a;"
                        onmouseover="this.style.background='#15803d'"
                        onmouseout="this.style.background='#16a34a'">
                        <i data-lucide="message-circle" class="w-5 h-5"></i>
                        WhatsApp Customer
                    </a>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
                <div class="font-extrabold text-slate-900 mb-3">Tindakan Admin</div>

                <div class="flex flex-col sm:flex-row flex-wrap gap-2">
                    <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                        @csrf
                        @method('PUT')
                        <button name="action" value="approve"
                            class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                            style="background:#0194F3;"
                            onmouseover="this.style.background='#0186DB'"
                            onmouseout="this.style.background='#0194F3'"
                            onclick="return confirm('Setujui order ini?')">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            Approve
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                        @csrf
                        @method('PUT')
                        <button name="action" value="reject"
                            class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                            style="background:#f59e0b;"
                            onmouseover="this.style.background='#d97706'"
                            onmouseout="this.style.background='#f59e0b'"
                            onclick="return confirm('Tolak order ini?')">
                            <i data-lucide="ban" class="w-4 h-4"></i>
                            Tolak
                        </button>
                    </form>

                    <form method="POST"
                        action="{{ route('admin.orders.destroy', $order) }}"
                        onsubmit="return confirm('Yakin hapus order ini? Tindakan tidak dapat dibatalkan.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                            style="background:#ef4444"
                            onmouseover="this.style.background='#dc2626'"
                            onmouseout="this.style.background='#ef4444'">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- Right --}}
        <div class="lg:col-span-6 space-y-5">

            {{-- Ringkasan Pembayaran --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 font-extrabold text-slate-900">
                    Ringkasan Pembayaran
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="text-xs font-extrabold text-slate-500">Subtotal</div>
                            <div class="mt-1 font-extrabold text-slate-900">
                                Rp {{ number_format($order->subtotal,0,',','.') }}
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="text-xs font-extrabold text-slate-500">Diskon</div>
                            <div class="mt-1 font-extrabold text-slate-900">
                                Rp {{ number_format($order->discount,0,',','.') }}
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:col-span-2">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-xs font-extrabold text-slate-500">Total</div>
                                    <div class="mt-1 text-xl font-extrabold" style="color:#0194F3;">
                                        Rp {{ number_format($order->final_price,0,',','.') }}
                                    </div>
                                </div>
                                <div class="h-11 w-11 rounded-2xl grid place-items-center border shrink-0"
                                    style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                                    <i data-lucide="wallet" class="w-6 h-6" style="color:#0194F3;"></i>
                                </div>
                            </div>

                            <div class="mt-3 text-sm text-slate-700">
                                <div><span class="font-extrabold text-slate-900">Metode:</span> {{ $order->payment_method }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Riwayat Payment --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 font-extrabold text-slate-900">
                    Riwayat Payment
                </div>

                <div class="p-5 space-y-3">
                    @forelse($order->payments as $pay)
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                            <div class="space-y-1 text-sm text-slate-700">
                                <div><span class="font-extrabold text-slate-900">Metode:</span> {{ $pay->method }}</div>
                                <div><span class="font-extrabold text-slate-900">Amount:</span> Rp {{ number_format($pay->amount,0,',','.') }}</div>
                                <div><span class="font-extrabold text-slate-900">Status:</span> {{ $pay->status }}</div>

                                @if($pay->gateway_reference)
                                <div class="text-xs text-slate-600">
                                    <span class="font-extrabold text-slate-900">Gateway Ref:</span> {{ $pay->gateway_reference }}
                                </div>
                                @endif

                                <div class="text-xs text-slate-500">
                                    {{ $pay->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>

                            @if($pay->proof_image)
                            <a href="{{ asset('storage/'.$pay->proof_image) }}" target="_blank"
                                class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-xs font-extrabold border border-slate-200 bg-white text-slate-800 hover:bg-slate-50 transition shrink-0">
                                <i data-lucide="image" class="w-4 h-4" style="color:#0194F3;"></i>
                                Lihat Bukti
                            </a>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center">
                        <div class="mx-auto h-12 w-12 rounded-2xl border grid place-items-center"
                            style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22);">
                            <i data-lucide="inbox" class="w-6 h-6" style="color:#0194F3;"></i>
                        </div>
                        <div class="mt-3 font-extrabold text-slate-900">Belum ada data payment</div>
                        <div class="mt-1 text-sm text-slate-600">Riwayat pembayaran akan tampil di sini.</div>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

</div>
@endsection