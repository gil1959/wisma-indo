@extends('user.layouts.app')
@php $isEn = app()->getLocale() === 'en'; @endphp
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan aktivitas akun kamu')

@section('content')
@if(session('success'))
<div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
    <div class="font-extrabold text-emerald-700">{{ $isEn ? 'Success' : 'Sukses' }}</div>
    <div class="text-sm mt-1 text-emerald-700">{{ session('success') }}</div>
</div>
@endif

{{-- TOP STATS (ikuti admin) --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-5">

    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition relative overflow-hidden">
        <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
            style="background: radial-gradient(circle, rgba(1,148,243,0.16) 0%, transparent 65%);"></div>

        <div class="flex items-start justify-between relative">
            <div>
                <div class="text-xs font-extrabold text-slate-500">{{ $isEn ? 'Total Orders' : 'Total Pesanan' }}</div>
                <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ $totalOrders }}</div>
                <div class="mt-1 text-xs text-slate-500">{{ $isEn ? 'Recorded transactions count' : 'Jumlah transaksi yang tercatat' }}</div>
            </div>

            <div class="h-11 w-11 rounded-2xl grid place-items-center border"
                style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                <i data-lucide="receipt" class="w-6 h-6" style="color:#0194F3;"></i>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('user.orders') }}"
                class="text-xs font-extrabold hover:underline decoration-[#0194F3]"
                style="color:#0194F3;">
                {{ $isEn ? 'View Orders →' : 'Lihat Orders →' }}
            </a>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition relative overflow-hidden">
        <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
            style="background: radial-gradient(circle, rgba(1,148,243,0.14) 0%, transparent 65%);"></div>

        <div class="flex items-start justify-between relative">
            <div>
                <div class="text-xs font-extrabold text-slate-500">{{ $isEn ? 'Waiting Payment' : 'Menunggu Pembayaran' }}</div>
                <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ $waitingPayment }}</div>
                <div class="mt-1 text-xs text-slate-500">{{ $isEn ? 'Action required from you' : 'Butuh tindakan dari kamu' }}</div>
            </div>

            <div class="h-11 w-11 rounded-2xl grid place-items-center border"
                style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                <i data-lucide="wallet" class="w-6 h-6" style="color:#0194F3;"></i>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('user.orders', ['payment_status' => 'waiting_payment']) }}"
                class="text-xs font-extrabold hover:underline decoration-[#0194F3]"
                style="color:#0194F3;">
                {{ $isEn ? 'Filter →' : 'Filter →' }}
            </a>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition relative overflow-hidden">
        <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
            style="background: radial-gradient(circle, rgba(1,148,243,0.12) 0%, transparent 65%);"></div>

        <div class="flex items-start justify-between relative">
            <div>
                <div class="text-xs font-extrabold text-slate-500">{{ $isEn ? 'Verification' : 'Verifikasi' }}</div>
                <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ $waitingVerification }}</div>
                <div class="mt-1 text-xs text-slate-500">{{ $isEn ? 'Being reviewed by admin' : 'Sedang dicek admin' }}</div>
            </div>

            <div class="h-11 w-11 rounded-2xl grid place-items-center border"
                style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                <i data-lucide="shield-check" class="w-6 h-6" style="color:#0194F3;"></i>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('user.orders', ['payment_status' => 'waiting_verification']) }}"
                class="text-xs font-extrabold hover:underline decoration-[#0194F3]"
                style="color:#0194F3;">
                {{ $isEn ? 'Filter →' : 'Filter →' }}
            </a>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition relative overflow-hidden">
        <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
            style="background: radial-gradient(circle, rgba(1,148,243,0.18) 0%, transparent 65%);"></div>

        <div class="flex items-start justify-between relative">
            <div>
                <div class="text-xs font-extrabold text-slate-500">{{ $isEn ? 'Total Spend (Paid)' : 'Total Belanja (Lunas)' }}</div>
                <div class="mt-2 text-2xl font-extrabold" style="color:#0194F3;">
                    Rp {{ number_format($totalSpend ?? 0, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs text-slate-500">{{ $isEn ? 'Total of paid transactions' : 'Akumulasi transaksi lunas' }}</div>
            </div>

            <div class="h-11 w-11 rounded-2xl grid place-items-center border"
                style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                <i data-lucide="credit-card" class="w-6 h-6" style="color:#0194F3;"></i>
            </div>
        </div>
    </div>

</div>

{{-- GRID bawah (ikuti admin section title: text-sm font-extrabold) --}}
<div class="grid grid-cols-1 2xl:grid-cols-12 gap-5">

    <div class="2xl:col-span-7 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-sm font-extrabold text-slate-900">Recent Orders</h2>
            <a href="{{ route('user.orders') }}"
                class="text-xs font-extrabold hover:underline decoration-[#0194F3] shrink-0"
                style="color:#0194F3;">
                {{ $isEn ? 'View All →' : 'Lihat Semua →' }}
            </a>
        </div>

        <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200 bg-white">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-left px-5 py-3 font-bold">Invoice</th>
                        <th class="text-left px-5 py-3 font-bold">Produk</th>
                        <th class="text-left px-5 py-3 font-bold">Status</th>
                        <th class="text-left px-5 py-3 font-bold">Payment</th>
                        <th class="text-right px-5 py-3 font-bold">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentOrders as $o)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3">
                            <a class="font-bold hover:underline decoration-[#0194F3]" style="color:#0194F3;"
                                href="{{ route('user.orders.show', $o) }}">
                                {{ $o->invoice_number }}
                            </a>
                            <div class="text-xs text-slate-500 mt-0.5">
                                {{ optional($o->created_at)->format('d M Y H:i') }}
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <div class="font-semibold text-slate-900">{{ $o->product_name ?? '-' }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">Type: <span class="font-semibold">{{ strtoupper($o->type ?? '-') }}</span></div>
                        </td>
                        <td class="px-5 py-3">
                            @include('user.partials.order-status-badge', ['status' => $o->order_status])
                        </td>
                        <td class="px-5 py-3">
                            @include('user.partials.payment-status-badge', ['status' => $o->payment_status])
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-slate-900">
                            Rp {{ number_format($o->final_price ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-slate-500">
                            {{ $isEn ? 'No transactions yet.' : 'Belum ada transaksi.' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="2xl:col-span-5 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-sm font-extrabold text-slate-900">{{ $isEn ? 'Status Summary' : 'Ringkasan Status' }}</h2>
            <span class="text-xs font-extrabold px-3 py-1 rounded-full border shrink-0"
                style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                Overview
            </span>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-3">
            <div class="rounded-2xl border border-slate-200 p-4 bg-white">
                <div class="text-xs font-semibold text-slate-500">Pending</div>
                <div class="mt-1 text-xl font-extrabold text-slate-900">{{ $pendingOrders }}</div>
            </div>
            <div class="rounded-2xl border border-slate-200 p-4 bg-white">
                <div class="text-xs font-semibold text-slate-500">Approved</div>
                <div class="mt-1 text-xl font-extrabold text-slate-900">{{ $approvedOrders }}</div>
            </div>
            <div class="rounded-2xl border border-slate-200 p-4 bg-white">
                <div class="text-xs font-semibold text-slate-500">Rejected</div>
                <div class="mt-1 text-xl font-extrabold text-slate-900">{{ $rejectedOrders }}</div>
            </div>
            <div class="rounded-2xl border border-slate-200 p-4 bg-white">
                <div class="text-xs font-semibold text-slate-500">Paid</div>
                <div class="mt-1 text-xl font-extrabold text-slate-900">{{ $paidOrders }}</div>
            </div>
        </div>

        <div class="mt-4 text-xs text-slate-500">
            {{ $isEn ? 'Tip: check the Orders tab for payment status details.' : ' Tip: cek tab Orders untuk detail status pembayaran.' }}

        </div>
    </div>

</div>
@endsection