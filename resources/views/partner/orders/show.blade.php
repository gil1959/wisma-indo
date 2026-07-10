@extends('partner.layouts.app')

@section('title', 'Detail Order')
@section('page-subtitle', 'Order')
@section('page-title', 'Detail Order')

@section('content')
<div class="space-y-5">

    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">
                Detail Order <span class="text-azure">{{ $order->invoice_number }}</span>
            </h2>
            <p class="mt-1 text-sm text-slate-600">Detail pesanan + pembayaran.</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('partner.orders.index') }}"
               class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white text-slate-800 hover:bg-slate-50 transition">
                <i data-lucide="arrow-left" class="w-4 h-4" style="color:#0194F3;"></i>
                Kembali
            </a>

            <form method="POST"
                  action="{{ route('partner.orders.destroy', $order) }}"
                  onsubmit="return confirm('Yakin hapus order ini?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                        style="background:#ef4444"
                        onmouseover="this.style.background='#dc2626'"
                        onmouseout="this.style.background='#ef4444'">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

        <div class="lg:col-span-6 space-y-5">
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

                    @php
                        $wa = \App\Support\OrderPartnerResolver::normalizeWhatsapp($order->customer_phone);
                        $waText = "Halo {$order->customer_name}, terkait order {$order->invoice_number} untuk {$order->product_name}.";
                    @endphp

                    @if($wa)
                        <a href="{{ \App\Support\OrderPartnerResolver::buildWaLink($wa, $waText) }}"
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
        </div>

        <div class="lg:col-span-6 space-y-5">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 font-extrabold text-slate-900">
                    Ringkasan Pembayaran
                </div>

                <div class="p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="text-xs font-extrabold text-slate-500">Subtotal</div>
                            <div class="mt-1 font-extrabold text-slate-900">Rp {{ number_format($order->subtotal,0,',','.') }}</div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="text-xs font-extrabold text-slate-500">Diskon</div>
                            <div class="mt-1 font-extrabold text-slate-900">Rp {{ number_format($order->discount,0,',','.') }}</div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:col-span-2">
                            <div class="text-xs font-extrabold text-slate-500">Final</div>
                            <div class="mt-1 text-lg font-extrabold" style="color:#0194F3;">
                                Rp {{ number_format($order->final_price,0,',','.') }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="font-extrabold text-slate-900 mb-2">Riwayat Payment</div>

                        @forelse($order->payments as $p)
                            <div class="py-2 border-b border-slate-200 last:border-b-0 text-sm">
                                <div class="flex items-center justify-between">
                                    <div class="font-bold">{{ $p->method ?? '-' }}</div>
                                    <div class="text-xs font-extrabold px-2 py-1 rounded-full border border-slate-200 bg-white">
                                        {{ $p->status ?? '-' }}
                                    </div>
                                </div>
                                <div class="text-xs text-slate-500 mt-1">
                                    {{ optional($p->created_at)->format('d M Y H:i') }}
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-slate-500">Belum ada data payment.</div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
