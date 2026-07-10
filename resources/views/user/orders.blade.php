@extends('user.layouts.app')
@php $isEn = app()->getLocale() === 'en'; @endphp

@section('title', $isEn ? 'Orders' : 'Pesanan')
@section('page-title', $isEn ? 'Orders' : 'Pesanan')
@section('page-subtitle', $isEn ? 'Manage & track your transactions' : 'Kelola & pantau transaksi kamu')


@section('content')
<div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm mb-5">
    <div class="flex items-center justify-between gap-3">
        <h2 class="text-sm font-extrabold text-slate-900">{{ $isEn ? 'Filter' : 'Filter' }}</h2>
        <span class="text-xs font-extrabold px-3 py-1 rounded-full border shrink-0"
            style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
            Orders
        </span>
    </div>

    <form method="GET" class="mt-4 grid grid-cols-1 md:grid-cols-12 gap-3">
        <div class="md:col-span-5">
            <label class="label">Search</label>
            <input class="input" name="search" value="{{ request('search') }}" placeholder="{{ $isEn ? 'Invoice / Product name...' : 'Invoice / Nama produk...' }}">
        </div>

        <div class="md:col-span-2">
            <label class="label">Type</label>
            <select class="input" name="type">
                <option value="">{{ $isEn ? 'All' : 'Semua' }}</option>
                <option value="tour" @selected(request('type')==='tour' )>{{ $isEn ? 'Tour' : 'Tour' }}</option>
                <option value="rent_car" @selected(request('type')==='rent_car' )>{{ $isEn ? 'Car Rental' : 'Rental Mobil' }}</option>

            </select>
        </div>

        <div class="md:col-span-2">
            <label class="label">{{ $isEn ? 'Order Status' : 'Status Order' }}</label>
            <select class="input" name="order_status">
                <option value="">{{ $isEn ? 'All' : 'Semua' }}</option>
                <option value="pending" @selected(request('order_status')==='pending' )>{{ $isEn ? 'Pending' : 'Menunggu' }}</option>
                <option value="approved" @selected(request('order_status')==='approved' )>{{ $isEn ? 'Approved' : 'Disetujui' }}</option>
                <option value="rejected" @selected(request('order_status')==='rejected' )>{{ $isEn ? 'Rejected' : 'Ditolak' }}</option>

            </select>
        </div>

        <div class="md:col-span-3">
            <label class="label">{{ $isEn ? 'Payment Status' : 'Status Pembayaran' }}</label>
            <select class="input" name="payment_status">
                <option value="">{{ $isEn ? 'All' : 'Semua' }}</option>
                <option value="waiting_payment" @selected(request('payment_status')==='waiting_payment' )>{{ $isEn ? 'Waiting Payment' : 'Menunggu Pembayaran' }}</option>
                <option value="waiting_verification" @selected(request('payment_status')==='waiting_verification' )>{{ $isEn ? 'Waiting Verification' : 'Menunggu Verifikasi' }}</option>
                <option value="paid" @selected(request('payment_status')==='paid' )>{{ $isEn ? 'Paid' : 'Lunas' }}</option>
                <option value="failed" @selected(request('payment_status')==='failed' )>{{ $isEn ? 'Failed' : 'Gagal' }}</option>

            </select>
        </div>

        <div class="md:col-span-12 flex flex-wrap gap-2 pt-2">
            <button class="btn btn-primary px-4 py-2.5">
                <i data-lucide="search" class="w-4 h-4"></i>
                {{ $isEn ? 'Apply' : 'Terapkan' }}
            </button>

            <a href="{{ route('user.orders') }}" class="btn btn-ghost px-4 py-2.5">
                <i data-lucide="rotate-ccw" class="w-4 h-4" style="color:#0194F3;"></i>
                {{ $isEn ? 'Reset' : 'Reset' }}
            </a>
        </div>
    </form>
</div>

<div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
    <div class="flex items-center justify-between gap-3">
        <h2 class="text-sm font-extrabold text-slate-900">{{ $isEn ? 'Orders List' : 'Daftar Orders' }}</h2>

        <div class="text-xs text-slate-500">
            Total: <span class="font-semibold text-slate-900">{{ $orders->total() }}</span>
        </div>
    </div>

    <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="text-left px-5 py-3 font-bold">Invoice</th>
                    <th class="text-left px-5 py-3 font-bold">{{ $isEn ? 'Product' : 'Produk' }}</th>
                    <th class="text-left px-5 py-3 font-bold">Order Status</th>
                    <th class="text-left px-5 py-3 font-bold">Payment</th>
                    <th class="text-right px-5 py-3 font-bold">Total</th>
                    <th class="text-right px-5 py-3 font-bold">{{ $isEn ? 'Action' : 'Aksi' }}</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
                @forelse($orders as $o)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3">
                        <a class="font-bold hover:underline decoration-[#0194F3]" style="color:#0194F3;"
                            href="{{ route('user.orders.show', $o) }}">
                            {{ $o->invoice_number ?? '-' }}
                        </a>
                        <div class="text-xs text-slate-500 mt-0.5">
                            {{ optional($o->created_at)->format('d M Y H:i') }}
                        </div>
                    </td>

                    <td class="px-5 py-3">
                        <div class="font-semibold text-slate-900">{{ $o->product_name ?? '-' }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">
                            Type: <span class="font-semibold">{{ strtoupper($o->type ?? '-') }}</span>
                        </div>
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

                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('user.orders.show', $o) }}" class="btn btn-primary px-4 py-2.5">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            {{ $isEn ? 'Details' : 'Detail' }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-slate-500">
                        {{ $isEn ? 'No orders yet.' : 'Belum ada order.' }}

                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection