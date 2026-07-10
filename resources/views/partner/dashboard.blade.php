@extends('partner.layouts.app')

@section('title', 'Dashboard')
@section('page-subtitle', 'Overview')
@section('page-title', 'Partner Dashboard')

@section('content')
<div class="space-y-5">

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <div class="text-sm text-slate-500">Selamat datang,</div>
                <div class="text-xl font-extrabold text-slate-900">{{ $user->name }}</div>
                <div class="text-xs text-slate-500 mt-1">{{ $user->email }}</div>
            </div>

            <div class="flex items-center gap-2">
    <a href="{{ route('partner.orders.index') }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
       style="background:#0194F3;"
       onmouseover="this.style.background='#0186DB'"
       onmouseout="this.style.background='#0194F3'">
        <i data-lucide="shopping-bag" class="w-4 h-4"></i>
        Lihat Orders
    </a>

    <a href="{{ route('partner.withdraw.index') }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white text-slate-800 hover:bg-slate-50 transition">
        <i data-lucide="wallet" class="w-4 h-4" style="color:#0194F3;"></i>
        Withdraw
    </a>

    <a href="{{ route('partner.profile.edit') }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white text-slate-800 hover:bg-slate-50 transition">
        <i data-lucide="user" class="w-4 h-4" style="color:#0194F3;"></i>
        Profile
    </a>
</div>

        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
            <div class="text-xs font-extrabold text-slate-500">Total Orders</div>
            <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($totalOrders) }}</div>
            <div class="mt-2 text-xs text-slate-500">Semua order untuk produk kamu</div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
            <div class="text-xs font-extrabold text-slate-500">Pending Orders</div>
            <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($pendingOrders) }}</div>
            <div class="mt-2 text-xs text-slate-500">Butuh follow-up customer/admin</div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
            <div class="text-xs font-extrabold text-slate-500">Revenue Bulan Ini</div>
            <div class="mt-2 text-2xl font-extrabold" style="color:#0194F3;">
                Rp {{ number_format((int)$monthRevenue,0,',','.') }}
            </div>
            <div class="mt-2 text-xs text-slate-500">Paid + Approved</div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
            <div class="text-xs font-extrabold text-slate-500">Paket Aktif</div>
            <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($activePackages) }}</div>
            <div class="mt-2 text-xs text-slate-500">
                Tour: {{ $activeTour }}, Rent: {{ $activeRent }}, Ship: {{ $activeShip }}
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <div class="text-lg font-extrabold text-slate-900">Wallet Overview</div>
            <div class="text-sm text-slate-600 mt-1">
                Saldo bersih sudah dipotong pajak partner:
                <span class="font-extrabold text-slate-900">{{ number_format($taxPercent, 2) }}%</span>
            </div>
        </div>

        @if(isset($pendingWithdrawRequests) && $pendingWithdrawRequests > 0)
            <div class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-extrabold"
                 style="background: rgba(245,158,11,0.10); border-color: rgba(245,158,11,0.25); color:#7c4a03;">
                <i data-lucide="alert-triangle" class="w-4 h-4" style="color:#f59e0b;"></i>
                {{ $pendingWithdrawRequests }} request withdraw pending
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <div class="text-xs font-extrabold text-slate-600 uppercase">Saldo Tersedia</div>
            <div class="mt-2 text-2xl font-extrabold text-slate-900">
                Rp {{ number_format((int)$availableBalance, 0, ',', '.') }}
            </div>
            <div class="mt-1 text-xs text-slate-500">Siap ditarik kapan saja</div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <div class="text-xs font-extrabold text-slate-600 uppercase">Saldo Pending</div>
            <div class="mt-2 text-2xl font-extrabold text-slate-900">
                Rp {{ number_format((int)$pendingBalance, 0, ',', '.') }}
            </div>
            <div class="mt-1 text-xs text-slate-500">Sedang diproses admin</div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <div class="text-xs font-extrabold text-slate-600 uppercase">Sudah Ditarik</div>
            <div class="mt-2 text-2xl font-extrabold text-slate-900">
                Rp {{ number_format((int)$withdrawnBalance, 0, ',', '.') }}
            </div>
            <div class="mt-1 text-xs text-slate-500">Total yang sudah approved</div>
        </div>
    </div>
</div>


    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            @if(isset($recentWithdrawRequests) && $recentWithdrawRequests->count())
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
        <div>
            <div class="font-extrabold text-slate-900">Recent Withdraw Requests</div>
            <div class="text-xs text-slate-500 mt-1">Riwayat request penarikan terbaru</div>
        </div>
        <a href="{{ route('partner.withdraw.requests') }}"
           class="text-xs font-extrabold hover:underline"
           style="color:#0194F3;">
            Lihat semua →
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs font-extrabold text-slate-600">
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3">Jumlah</th>
                    <th class="px-5 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($recentWithdrawRequests as $w)
                    @php
                        $badge = $w->status === 'pending'
                            ? 'bg-amber-50 text-amber-800 border-amber-200'
                            : ($w->status === 'approved'
                                ? 'bg-emerald-50 text-emerald-800 border-emerald-200'
                                : 'bg-rose-50 text-rose-800 border-rose-200');
                    @endphp
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-5 py-4 font-bold text-slate-800">{{ $w->created_at->format('d M Y H:i') }}</td>
                        <td class="px-5 py-4 font-extrabold text-slate-900">Rp {{ number_format((int)$w->amount,0,',','.') }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-extrabold border {{ $badge }}">
                                {{ strtoupper($w->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

            <div>
                <div class="font-extrabold text-slate-900">Recent Orders</div>
                <div class="text-xs text-slate-500 mt-1">Order terbaru untuk produk kamu</div>
            </div>
            <a href="{{ route('partner.orders.index') }}"
               class="text-xs font-extrabold hover:underline"
               style="color:#0194F3;">
                Lihat semua →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[900px] w-full">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-extrabold text-slate-600">
                        <th class="px-5 py-3">Invoice</th>
                        <th class="px-5 py-3">Customer</th>
                        <th class="px-5 py-3">Produk</th>
                        <th class="px-5 py-3">Total</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($recentOrders as $o)
                    <tr class="text-sm text-slate-700 hover:bg-slate-50/70 transition">
                        <td class="px-5 py-4 font-extrabold text-slate-900">{{ $o->invoice_number }}</td>
                        <td class="px-5 py-4">
                            <div class="font-bold text-slate-900">{{ $o->customer_name }}</div>
                            <div class="text-xs text-slate-500">{{ $o->customer_email }}</div>
                        </td>
                        <td class="px-5 py-4">{{ $o->product_name }}</td>
                        <td class="px-5 py-4 font-extrabold text-slate-900">
                            Rp {{ number_format((int)$o->final_price,0,',','.') }}
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-extrabold border border-slate-200 bg-white">
                                {{ $o->payment_status }} / {{ $o->order_status }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <a href="{{ route('partner.orders.show', $o) }}"
                               class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
                               style="background:#0194F3;"
                               onmouseover="this.style.background='#0186DB'"
                               onmouseout="this.style.background='#0194F3'">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-slate-500">
                            Belum ada order.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
