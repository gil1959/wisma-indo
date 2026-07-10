@extends('partner.layouts.app')

@section('title', 'Orders')
@section('page-subtitle', 'Monitoring')
@section('page-title', 'Orders Masuk')

@section('content')
<div class="space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Orders</h2>
            <p class="mt-1 text-sm text-slate-600">
                Data order untuk produk yang kamu buat. Bisa search + filter.
            </p>
        </div>
    </div>

    @php
        $q = request('q');
        $type = request('type');
        $queryParams = array_filter(['q' => $q, 'type' => $type]);
        $filter = ($currentFilter ?? 'all');
    @endphp

    <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">

            <form method="GET" action="{{ url()->current() }}" class="w-full lg:max-w-2xl">
                <label class="block text-sm font-bold text-slate-800 mb-2">Pencarian</label>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                    <div class="md:col-span-7 relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </span>
                        <input
                            type="text"
                            name="q"
                            value="{{ $q }}"
                            autocomplete="off"
                            placeholder="Cari customer / invoice / produk..."
                            class="w-full rounded-xl border border-slate-200 bg-white pl-10 pr-3 py-2.5 text-sm focus:border-[color:var(--brand)] focus:ring-0"
                        >
                    </div>

                    <div class="md:col-span-3">
                        <select name="type"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-[color:var(--brand)] focus:ring-0">
                            <option value="">Semua Tipe</option>
                            <option value="tour" {{ $type==='tour' ? 'selected' : '' }}>TOUR</option>
                            <option value="rent_car" {{ $type==='rent_car' ? 'selected' : '' }}>RENT CAR</option>
                            <option value="ship" {{ $type==='ship' ? 'selected' : '' }}>SHIP</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                                style="background:#0194F3;"
                                onmouseover="this.style.background='#0186DB'"
                                onmouseout="this.style.background='#0194F3'">
                            <i data-lucide="search" class="w-4 h-4"></i>
                            Cari
                        </button>
                    </div>
                </div>

                @if(!empty($q) || !empty($type))
                    <div class="mt-2">
                        <a href="{{ route('partner.orders.index') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-xs font-extrabold border border-slate-200 bg-white text-slate-800 hover:bg-slate-50 transition">
                            <i data-lucide="rotate-ccw" class="w-4 h-4" style="color:#0194F3;"></i>
                            Reset Filter
                        </a>
                    </div>
                @endif
            </form>

            <div class="w-full lg:w-auto">
                <div class="text-sm font-bold text-slate-800 mb-2">Filter Status</div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('partner.orders.index', $queryParams) }}"
                       class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border transition
                              {{ $filter === 'all' ? 'text-white border-transparent' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}"
                       style="{{ $filter === 'all' ? 'background:#0194F3;' : '' }}">
                        <i data-lucide="list" class="w-4 h-4 {{ $filter === 'all' ? 'text-white' : '' }}"></i>
                        Semua
                    </a>

                    <a href="{{ route('partner.orders.approved', $queryParams) }}"
                       class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border transition
                              {{ $filter === 'approved' ? 'text-white border-transparent' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}"
                       style="{{ $filter === 'approved' ? 'background:#16a34a;' : '' }}">
                        <i data-lucide="check-circle" class="w-4 h-4 {{ $filter === 'approved' ? 'text-white' : 'text-emerald-600' }}"></i>
                        Approved
                    </a>

                    <a href="{{ route('partner.orders.rejected', $queryParams) }}"
                       class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border transition
                              {{ $filter === 'rejected' ? 'text-white border-transparent' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}"
                       style="{{ $filter === 'rejected' ? 'background:#ef4444;' : '' }}">
                        <i data-lucide="x-circle" class="w-4 h-4 {{ $filter === 'rejected' ? 'text-white' : 'text-red-500' }}"></i>
                        Rejected
                    </a>
                </div>
            </div>

        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-[1000px] w-full">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-extrabold text-slate-600">
                        <th class="px-5 py-3">Invoice</th>
                        <th class="px-5 py-3">Customer</th>
                        <th class="px-5 py-3">Tipe</th>
                        <th class="px-5 py-3">Produk</th>
                        <th class="px-5 py-3">Total</th>
                        <th class="px-5 py-3">Payment</th>
                        <th class="px-5 py-3">Order</th>
                        <th class="px-5 py-3">Dibuat</th>
                        <th class="px-5 py-3 w-[210px]">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                @forelse($orders as $order)
                    <tr class="text-sm text-slate-700 hover:bg-slate-50/70 transition">
                        <td class="px-5 py-4 font-extrabold text-slate-900">{{ $order->invoice_number }}</td>

                        <td class="px-5 py-4">
                            <div class="font-bold text-slate-900">{{ $order->customer_name }}</div>
                            @if(!empty($order->customer_email))
                                <div class="text-xs text-slate-500">{{ $order->customer_email }}</div>
                            @endif
                        </td>

                        <td class="px-5 py-4">
                            <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-extrabold">
                                {{ strtoupper($order->type) }}
                            </span>
                        </td>

                        <td class="px-5 py-4">
                            <div class="line-clamp-2">{{ $order->product_name }}</div>
                        </td>

                        <td class="px-5 py-4 font-extrabold text-slate-900">
                            Rp {{ number_format($order->final_price,0,',','.') }}
                        </td>

                        <td class="px-5 py-4">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-extrabold border border-slate-200 bg-white">
                                {{ $order->payment_status }}
                            </span>
                        </td>

                        <td class="px-5 py-4">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-extrabold border border-slate-200 bg-white">
                                {{ $order->order_status }}
                            </span>
                        </td>

                        <td class="px-5 py-4 text-slate-600">{{ $order->created_at->format('d/m/Y H:i') }}</td>

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('partner.orders.show', $order) }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
                                   style="background:#0194F3;"
                                   onmouseover="this.style.background='#0186DB'"
                                   onmouseout="this.style.background='#0194F3'">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    Detail
                                </a>

                                <form action="{{ route('partner.orders.destroy', $order) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin hapus order ini? Tindakan tidak dapat dibatalkan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
                                            style="background:#ef4444"
                                            onmouseover="this.style.background='#dc2626'"
                                            onmouseout="this.style.background='#ef4444'">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-5 py-10 text-center text-slate-500">
                            Belum ada order untuk produk kamu.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200">
            {{ $orders->links() }}
        </div>
    </div>

</div>
@endsection
