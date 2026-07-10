@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="flex items-center justify-between mb-6">
    
    <h1 class="text-2xl font-extrabold">Dashboard</h1>
 @if(session('success'))
    <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
        <div class="font-extrabold text-emerald-700">Sukses</div>
        <div class="text-sm mt-1 text-emerald-700">
            {{ session('success') }}
        </div>
    </div>
@endif
    <form method="POST"
          action="{{ route('admin.system.clear-cache') }}"
          onsubmit="return confirm('Bersihkan cache sekarang?')">
        @csrf

        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-extrabold text-white transition"
                style="background:#ef4444"
                onmouseover="this.style.background='#dc2626'"
                onmouseout="this.style.background='#ef4444'">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
            Clear Cache
        </button>
    </form>
</div>

    {{-- TOP STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-5">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition relative overflow-hidden">
            <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
                 style="background: radial-gradient(circle, rgba(1,148,243,0.20) 0%, transparent 65%);"></div>

            <div class="flex items-start justify-between relative">
                <div>
                    <div class="text-xs font-extrabold text-slate-500">Total Pendapatan</div>
                    <div class="mt-2 text-2xl font-extrabold" style="color:#0194F3;">
                       Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </div>
                    <div class="mt-1 text-xs text-slate-500">Ringkasan pendapatan seluruh transaksi</div>
                </div>

                <div class="h-11 w-11 rounded-2xl grid place-items-center border"
                     style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                    <i data-lucide="wallet" class="w-6 h-6" style="color:#0194F3;"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition relative overflow-hidden">
            <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
                 style="background: radial-gradient(circle, rgba(1,148,243,0.16) 0%, transparent 65%);"></div>

            <div class="flex items-start justify-between relative">
                <div>
                    <div class="text-xs font-extrabold text-slate-500">Total Pesanan</div>
                    <div class="mt-2 text-2xl font-extrabold text-slate-900">
                        {{ $totalOrders }} Order
 <span class="text-base text-slate-500 font-extrabold">Order</span>
                    </div>
                    <div class="mt-1 text-xs text-slate-500">Jumlah order yang tercatat</div>
                </div>

                <div class="h-11 w-11 rounded-2xl grid place-items-center border"
                     style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                    <i data-lucide="shopping-bag" class="w-6 h-6" style="color:#0194F3;"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition relative overflow-hidden">
            <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
                 style="background: radial-gradient(circle, rgba(1,148,243,0.14) 0%, transparent 65%);"></div>

            <div class="flex items-start justify-between relative">
                <div>
                    <div class="text-xs font-extrabold text-slate-500">Paket Aktif</div>
                    <div class="mt-2 text-2xl font-extrabold text-slate-900">
                        {{ $totalPackages }} Paket
 <span class="text-base text-slate-500 font-extrabold">Paket</span>
                    </div>
                    <div class="mt-1 text-xs text-slate-500">Total paket yang ditampilkan</div>
                </div>

                <div class="h-11 w-11 rounded-2xl grid place-items-center border"
                     style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                    <i data-lucide="map" class="w-6 h-6" style="color:#0194F3;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- QUICK ACTIONS + INBOX --}}
<div class="grid grid-cols-1 2xl:grid-cols-12 gap-5">

    {{-- Quick Actions --}}
    <div class="2xl:col-span-5 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-sm font-extrabold text-slate-900">Aksi Cepat</h2>
            <span class="text-xs font-extrabold px-3 py-1 rounded-full border shrink-0"
                  style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                Admin Tools
            </span>
        </div>

        {{-- penting: jangan pakai sm:grid-cols-2 kalau kolomnya sempit --}}
        <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-2 gap-3">
            <a href="{{ route('admin.tour-packages.index') }}"
               class="rounded-2xl border border-slate-200 p-4 hover:shadow-md transition bg-white">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl grid place-items-center border shrink-0"
                         style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                        <i data-lucide="map" class="w-5 h-5" style="color:#0194F3;"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-extrabold text-slate-900">Paket Wisata</div>
                        <div class="text-xs text-slate-500">Kelola paket tour</div>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.rent-car-packages.index') }}"
               class="rounded-2xl border border-slate-200 p-4 hover:shadow-md transition bg-white">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl grid place-items-center border shrink-0"
                         style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                        <i data-lucide="car" class="w-5 h-5" style="color:#0194F3;"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-extrabold text-slate-900">Rental</div>
                        <div class="text-xs text-slate-500">Kelola unit rental</div>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.orders.index') }}"
               class="rounded-2xl border border-slate-200 p-4 hover:shadow-md transition bg-white">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl grid place-items-center border shrink-0"
                         style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                        <i data-lucide="shopping-bag" class="w-5 h-5" style="color:#0194F3;"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-extrabold text-slate-900">Orders</div>
                        <div class="text-xs text-slate-500">Pantau pesanan</div>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.settings.general') }}"
               class="rounded-2xl border border-slate-200 p-4 hover:shadow-md transition bg-white">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl grid place-items-center border shrink-0"
                         style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                        <i data-lucide="settings" class="w-5 h-5" style="color:#0194F3;"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-extrabold text-slate-900">Settings</div>
                        <div class="text-xs text-slate-500">Atur konten halaman</div>
                    </div>
                </div>
            </a>
        </div>

        <div class="mt-4 text-xs text-slate-500">
            Gunakan “Aksi Cepat” untuk mempercepat alur kerja harian Anda.
        </div>
    </div>

    {{-- Inbox / Orders Incoming --}}
    <div class="2xl:col-span-7 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-sm font-extrabold text-slate-900">Order Masuk</h2>
            <a href="{{ route('admin.orders.index') }}"
               class="text-xs font-extrabold hover:underline decoration-[#0194F3] shrink-0"
               style="color:#0194F3;">
                Lihat Semua →
            </a>
        </div>

        <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-5">
            <div class="flex items-start gap-4">
                <div class="h-11 w-11 rounded-2xl grid place-items-center border shrink-0"
                     style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22);">
                    <i data-lucide="inbox" class="w-6 h-6" style="color:#0194F3;"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-extrabold text-slate-900">Belum ada pesanan masuk</div>
                    <p class="mt-1 text-sm text-slate-600">
                        Jika sudah ada order baru, daftar akan muncul di sini untuk Anda tindak lanjuti.
                    </p>

                    <div class="mt-4 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('admin.payments.index') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-xs font-extrabold text-white transition"
                           style="background:#0194F3;"
                           onmouseover="this.style.background='#0186DB'"
                           onmouseout="this.style.background='#0194F3'">
                            <i data-lucide="credit-card" class="w-4 h-4"></i>
                            Cek Pembayaran
                        </a>

                        <a href="{{ route('admin.documentations.index') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-xs font-extrabold border border-slate-200 text-slate-800 bg-white hover:bg-slate-50 transition">
                            <i data-lucide="images" class="w-4 h-4" style="color:#0194F3;"></i>
                            Kelola Dokumentasi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-slate-500">
            <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1"
                  style="background: rgba(1,148,243,0.06); border-color: rgba(1,148,243,0.18);">
                <i data-lucide="activity" class="w-4 h-4" style="color:#0194F3;"></i>
                Sistem siap menerima order
            </span>
            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1 bg-white">
                <i data-lucide="shield-check" class="w-4 h-4" style="color:#0194F3;"></i>
                Panel admin aman
            </span>
        </div>
    </div>
   


</div>

@endsection
