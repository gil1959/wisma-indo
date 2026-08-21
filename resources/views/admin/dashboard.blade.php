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
                    <div class="text-xs font-extrabold text-slate-500">Total Pendapatan</div>
                    <div class="mt-2 text-2xl font-extrabold text-emerald-600">
                       Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </div>
                    <div class="mt-1 text-xs text-slate-500">Dari semua transaksi sukses</div>
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

    {{-- VISITOR TRAFFIC --}}
    <div class="mb-5">
        <h2 class="text-sm font-extrabold text-slate-900 mb-3">Trafik Pengunjung (Unik IP)</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center gap-4 relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 opacity-5"><i data-lucide="users" style="width:100px;height:100px;"></i></div>
                <div class="h-12 w-12 rounded-2xl border border-indigo-200 bg-indigo-50 text-indigo-500 flex items-center justify-center shrink-0">
                    <i data-lucide="user-plus" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wide">Hari Ini</div>
                    <div class="text-2xl font-black text-slate-800">{{ number_format($todayVisitors, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center gap-4 relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 opacity-5"><i data-lucide="users" style="width:100px;height:100px;"></i></div>
                <div class="h-12 w-12 rounded-2xl border border-blue-200 bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                    <i data-lucide="bar-chart-2" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wide">Minggu Ini</div>
                    <div class="text-2xl font-black text-slate-800">{{ number_format($weekVisitors, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center gap-4 relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 opacity-5"><i data-lucide="users" style="width:100px;height:100px;"></i></div>
                <div class="h-12 w-12 rounded-2xl border border-purple-200 bg-purple-50 text-purple-500 flex items-center justify-center shrink-0">
                    <i data-lucide="globe" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wide">Total Keseluruhan</div>
                    <div class="text-2xl font-black text-slate-800">{{ number_format($totalVisitors, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        {{-- CHART VISITOR --}}
        <div class="mt-4 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h3 class="text-sm font-extrabold text-slate-800 mb-4">Grafik Pengunjung (7 Hari Terakhir)</h3>
            <div class="w-full h-64">
                <canvas id="visitorChart"></canvas>
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

        {{-- LATEST USERS --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden" x-data="{ impersonateModal: false, selectedUser: '', formAction: '' }">
            <div class="p-5 border-b border-slate-200 flex justify-between items-center">
                <h2 class="text-sm font-extrabold text-slate-900">Pengguna Terbaru (Login As)</h2>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-sky-600 hover:underline">Lihat Semua</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($latestUsers as $u)
                <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-200 overflow-hidden shrink-0 border border-slate-300">
                            @if($u->avatar)
                                <img src="{{ asset($u->avatar) }}" alt="{{ $u->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <i data-lucide="user" class="w-5 h-5"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-900">{{ $u->name }}</div>
                            <div class="text-xs text-slate-500">{{ $u->email }}</div>
                        </div>
                    </div>
                    <div>
                        @if($u->id !== auth()->id() && !$u->hasRole('admin'))
                            <button type="button" @click="impersonateModal = true; selectedUser = '{{ addslashes($u->name) }}'; formAction = '{{ route('admin.users.impersonate', $u) }}'" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-sky-200 bg-sky-50 text-sky-700 hover:bg-sky-600 hover:text-white transition text-xs font-bold shadow-sm">
                                <i data-lucide="log-in" class="w-3.5 h-3.5"></i> Login As
                            </button>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-slate-500 text-sm">
                    Belum ada pengguna.
                </div>
                @endforelse
            </div>

            {{-- MODAL IMPERSONATE --}}
            <div x-show="impersonateModal" style="display: none;" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
                <div x-show="impersonateModal" x-transition.opacity class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="impersonateModal = false"></div>
                <div x-show="impersonateModal" x-transition class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center z-10">
                    <div class="mx-auto w-16 h-16 rounded-full bg-sky-100 grid place-items-center mb-4">
                        <i data-lucide="log-in" class="w-8 h-8 text-sky-600"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">Konfirmasi Login</h3>
                    <p class="text-sm text-slate-500 mb-6">
                        Anda akan masuk dan mengendalikan akun 
                        <strong class="text-slate-800" x-text="selectedUser"></strong>. Lanjutkan?
                    </p>
                    <form :action="formAction" method="POST" class="flex items-center justify-center gap-3">
                        @csrf
                        <button type="button" @click="impersonateModal = false" class="px-4 py-2 rounded-xl font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl font-bold bg-sky-600 text-white hover:bg-sky-700 transition shadow-sm">Ya, Login</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('visitorChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels ?? []) !!},
                datasets: [{
                    label: 'Pengunjung (Unik IP)',
                    data: {!! json_encode($chartData ?? []) !!},
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
