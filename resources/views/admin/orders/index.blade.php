@extends('layouts.admin')

@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
    <div>
        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Orders</h2>
        <p class="mt-1 text-sm text-slate-600">
            Kelola dan pantau pesanan yang masuk.
        </p>
    </div>

    <a href="{{ route('admin.orders.rekap') }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
       style="background:#0194F3;"
       onmouseover="this.style.background='#0186DB'"
       onmouseout="this.style.background='#0194F3'">
        <i data-lucide="printer" class="w-4 h-4"></i>
        Rekap & Print
    </a>
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

    @php
        $q = request('q');
        $queryParams = array_filter(['q' => $q]);
        $filter = ($currentFilter ?? 'all');
    @endphp

    {{-- Filter + Search --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">

            {{-- Search --}}
            <form method="GET" action="{{ url()->current() }}" class="w-full lg:max-w-xl">
                <label class="block text-sm font-bold text-slate-800 mb-2">Pencarian</label>
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-1">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </span>
                        <input
                            type="text"
                            name="q"
                            value="{{ $q }}"
                            autocomplete="off"
                            placeholder="Cari berdasarkan nama customer atau invoice..."
                            class="w-full rounded-xl border border-slate-200 bg-white pl-10 pr-3 py-2.5 text-sm
                                   focus:border-[color:var(--brand)] focus:ring-0"
                        >
                    </div>

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                            style="background:#0194F3;"
                            onmouseover="this.style.background='#0186DB'"
                            onmouseout="this.style.background='#0194F3'">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        Cari
                    </button>

                    @if(!empty($q))
                        <a href="{{ url()->current() }}"
                           class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white text-slate-800 hover:bg-slate-50 transition">
                            <i data-lucide="rotate-ccw" class="w-4 h-4" style="color:#0194F3;"></i>
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- Tabs --}}
            <div class="w-full lg:w-auto">
                <div class="text-sm font-bold text-slate-800 mb-2">Filter</div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.orders.index', $queryParams) }}"
                       class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border transition
                              {{ $filter === 'all' ? 'text-white border-transparent' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}"
                       style="{{ $filter === 'all' ? 'background:#0194F3;' : '' }}">
                        <i data-lucide="list" class="w-4 h-4 {{ $filter === 'all' ? 'text-white' : '' }}"></i>
                        Semua
                    </a>

                    <a href="{{ route('admin.orders.approved', $queryParams) }}"
                       class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border transition
                              {{ $filter === 'approved' ? 'text-white border-transparent' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}"
                       style="{{ $filter === 'approved' ? 'background:#16a34a;' : '' }}">
                        <i data-lucide="check-circle" class="w-4 h-4 {{ $filter === 'approved' ? 'text-white' : 'text-emerald-600' }}"></i>
                        Approved
                    </a>

                    <a href="{{ route('admin.orders.rejected', $queryParams) }}"
                       class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border transition
                              {{ $filter === 'rejected' ? 'text-white border-transparent' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}"
                       style="{{ $filter === 'rejected' ? 'background:#ef4444;' : '' }}">
                        <i data-lucide="x-circle" class="w-4 h-4 {{ $filter === 'rejected' ? 'text-white' : 'text-red-500' }}"></i>
                        Rejected
                    </a>
                </div>
            </div>

        </div>

        {{-- Active filter chips --}}
        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-slate-600">
            <span class="font-extrabold text-slate-800">Ringkasan:</span>

            @if($q)
                <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1"
                      style="background: rgba(1,148,243,0.06); border-color: rgba(1,148,243,0.18);">
                    <i data-lucide="search" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                    Kata kunci: <span class="font-extrabold text-slate-900">{{ $q }}</span>
                </span>
            @endif

            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1">
                <i data-lucide="filter" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                Status: <span class="font-extrabold text-slate-900">
                    {{ $filter === 'all' ? 'Semua' : ($filter === 'approved' ? 'Approved' : 'Rejected') }}
                </span>
            </span>
        </div>
    </div>

    {{-- Table --}}
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
                        <th class="px-5 py-3 w-[190px]">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                @forelse($orders as $order)
                    <tr class="text-sm text-slate-700 hover:bg-slate-50/70 transition">
                        <td class="px-5 py-4 font-extrabold text-slate-900">
                            {{ $order->invoice_number }}
                        </td>
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
                        <td class="px-5 py-4 text-slate-600">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
                                   style="background:#0194F3;"
                                   onmouseover="this.style.background='#0186DB'"
                                   onmouseout="this.style.background='#0194F3'">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    Detail
                                </a>

                                <form action="{{ route('admin.orders.destroy', $order) }}"
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
                        <td colspan="9" class="px-5 py-12 text-center">
                            <div class="mx-auto h-12 w-12 rounded-2xl border grid place-items-center"
                                 style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22);">
                                <i data-lucide="inbox" class="w-6 h-6" style="color:#0194F3;"></i>
                            </div>
                            <div class="mt-3 font-extrabold text-slate-900">Belum ada order</div>
                            <div class="mt-1 text-sm text-slate-600">Data order akan muncul di sini ketika ada transaksi.</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 sm:px-5 py-4 border-t border-slate-200 bg-white">
            {{ $orders->links() }}
        </div>
    </div>

</div>
@endsection
