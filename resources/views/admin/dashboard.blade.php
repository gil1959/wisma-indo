@extends('layouts.admin')

@section('title', 'Dashboard Properti')
@section('page-title', 'Dashboard')

@section('content')
<div class="flex items-center justify-between mb-6">
    
    <h1 class="text-2xl font-extrabold text-slate-900">Dashboard</h1>
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
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
        
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition relative overflow-hidden">
            <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
                 style="background: radial-gradient(circle, rgba(16,185,129,0.20) 0%, transparent 65%);"></div>

            <div class="flex items-start justify-between relative">
                <div>
                    <div class="text-xs font-extrabold text-slate-500">Pendapatan Saldo</div>
                    <div class="mt-2 text-2xl font-extrabold text-emerald-600">
                       Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </div>
                    <div class="mt-1 text-xs text-slate-500">Dari penjualan top up koin</div>
                </div>

                <div class="h-11 w-11 rounded-2xl grid place-items-center border border-emerald-200 bg-emerald-50">
                    <i data-lucide="wallet" class="w-6 h-6 text-emerald-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition relative overflow-hidden">
            <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
                 style="background: radial-gradient(circle, rgba(1,148,243,0.16) 0%, transparent 65%);"></div>

            <div class="flex items-start justify-between relative">
                <div>
                    <div class="text-xs font-extrabold text-slate-500">Total Iklan Properti</div>
                    <div class="mt-2 text-2xl font-extrabold text-slate-900">
                        {{ $totalListings }}
                        <span class="text-base text-slate-500 font-extrabold">Iklan</span>
                    </div>
                    <div class="mt-1 text-xs text-slate-500">Semua yang pernah diposting</div>
                </div>

                <div class="h-11 w-11 rounded-2xl grid place-items-center border border-sky-200 bg-sky-50">
                    <i data-lucide="home" class="w-6 h-6 text-sky-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition relative overflow-hidden">
            <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
                 style="background: radial-gradient(circle, rgba(139,92,246,0.14) 0%, transparent 65%);"></div>

            <div class="flex items-start justify-between relative">
                <div>
                    <div class="text-xs font-extrabold text-slate-500">Iklan Aktif</div>
                    <div class="mt-2 text-2xl font-extrabold text-slate-900">
                        {{ $activeListings }}
                        <span class="text-base text-slate-500 font-extrabold">Aktif</span>
                    </div>
                    <div class="mt-1 text-xs text-slate-500">Iklan tayang di frontend</div>
                </div>

                <div class="h-11 w-11 rounded-2xl grid place-items-center border border-violet-200 bg-violet-50">
                    <i data-lucide="check-circle" class="w-6 h-6 text-violet-500"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition relative overflow-hidden">
            <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
                 style="background: radial-gradient(circle, rgba(245,158,11,0.14) 0%, transparent 65%);"></div>

            <div class="flex items-start justify-between relative">
                <div>
                    <div class="text-xs font-extrabold text-slate-500">Total Pengguna</div>
                    <div class="mt-2 text-2xl font-extrabold text-slate-900">
                        {{ $totalUsers }}
                        <span class="text-base text-slate-500 font-extrabold">User</span>
                    </div>
                    <div class="mt-1 text-xs text-slate-500">Termasuk agen terdaftar</div>
                </div>

                <div class="h-11 w-11 rounded-2xl grid place-items-center border border-amber-200 bg-amber-50">
                    <i data-lucide="users" class="w-6 h-6 text-amber-500"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- QUICK ACTIONS --}}
    <div class="grid grid-cols-1 gap-5">

        {{-- Quick Actions --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3 mb-4">
                <h2 class="text-sm font-extrabold text-slate-900">Aksi Cepat</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <a href="{{ route('admin.listings.index') }}"
                   class="rounded-2xl border border-slate-200 p-4 hover:shadow-md transition bg-white group">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl grid place-items-center border border-sky-200 bg-sky-50 group-hover:bg-sky-500 transition">
                            <i data-lucide="list" class="w-5 h-5 text-sky-500 group-hover:text-white transition"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-extrabold text-slate-900">Iklan Properti</div>
                            <div class="text-xs text-slate-500">Kelola dan moderasi</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.topups.index') }}"
                   class="rounded-2xl border border-slate-200 p-4 hover:shadow-md transition bg-white group">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl grid place-items-center border border-emerald-200 bg-emerald-50 group-hover:bg-emerald-500 transition">
                            <i data-lucide="credit-card" class="w-5 h-5 text-emerald-500 group-hover:text-white transition"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-extrabold text-slate-900">Top Up Saldo</div>
                            <div class="text-xs text-slate-500">Cek pembayaran</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.users.index') }}"
                   class="rounded-2xl border border-slate-200 p-4 hover:shadow-md transition bg-white group">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl grid place-items-center border border-amber-200 bg-amber-50 group-hover:bg-amber-500 transition">
                            <i data-lucide="users" class="w-5 h-5 text-amber-500 group-hover:text-white transition"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-extrabold text-slate-900">Data Pengguna</div>
                            <div class="text-xs text-slate-500">Kelola agen & user</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.settings.general') }}"
                   class="rounded-2xl border border-slate-200 p-4 hover:shadow-md transition bg-white group">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl grid place-items-center border border-slate-200 bg-slate-50 group-hover:bg-slate-800 transition">
                            <i data-lucide="settings" class="w-5 h-5 text-slate-500 group-hover:text-white transition"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-extrabold text-slate-900">Settings</div>
                            <div class="text-xs text-slate-500">Atur website</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

    </div>

@endsection
