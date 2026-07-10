@extends('layouts.admin')
@section('title', 'Tabungan Umrah - Setoran')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-2xl font-extrabold text-slate-900">Setoran Tabungan Umrah</div>
            <div class="text-sm text-slate-600 font-bold mt-1">Kelola setoran dan verifikasi bukti pembayaran.</div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.tabungan-umrah.accounts.pending') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 bg-white font-extrabold text-slate-700 hover:bg-slate-50">
                <i data-lucide="users" class="w-4 h-4"></i>
                Akun
            </a>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.tabungan-umrah.accounts.pending') }}"
           class="px-4 py-2 rounded-2xl font-extrabold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">
            Pending
        </a>
        <a href="{{ route('admin.tabungan-umrah.accounts.verified') }}"
           class="px-4 py-2 rounded-2xl font-extrabold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">
            Terverifikasi
        </a>
        <a href="{{ route('admin.tabungan-umrah.deposits.index') }}"
           class="px-4 py-2 rounded-2xl font-extrabold bg-brand-500 text-white">
            Setoran
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
            <div class="font-extrabold text-emerald-700">Sukses</div>
            <div class="text-sm mt-1 text-emerald-700">{{ session('success') }}</div>
        </div>
    @endif

   {{-- Filter --}}
<div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <div class="text-sm font-extrabold text-slate-700">Filter Status</div>
            <div class="mt-3 flex flex-wrap items-center gap-2">

                <a href="{{ route('admin.tabungan-umrah.deposits.index', ['status' => 'all']) }}"
                   class="px-4 py-2 rounded-2xl font-extrabold
                   {{ $status==='all' ? 'bg-brand-500 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                    All
                </a>

                <a href="{{ route('admin.tabungan-umrah.deposits.index', ['status' => 'waiting_verification']) }}"
                   class="px-4 py-2 rounded-2xl font-extrabold
                   {{ $status==='waiting_verification' ? 'bg-brand-500 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                    Waiting
                </a>

                <a href="{{ route('admin.tabungan-umrah.deposits.index', ['status' => 'approved']) }}"
                   class="px-4 py-2 rounded-2xl font-extrabold
                   {{ $status==='approved' ? 'bg-brand-500 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                    Approved
                </a>

                <a href="{{ route('admin.tabungan-umrah.deposits.index', ['status' => 'rejected']) }}"
                   class="px-4 py-2 rounded-2xl font-extrabold
                   {{ $status==='rejected' ? 'bg-brand-500 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                    Rejected
                </a>

            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.tabungan-umrah.deposits.index', ['status' => 'all']) }}"
               class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl border border-slate-200 bg-white font-extrabold text-slate-700 hover:bg-slate-50">
                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                Reset
            </a>
        </div>

    </div>
</div>


    {{-- Table --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div class="font-extrabold text-slate-900">Daftar Setoran</div>
            <div class="text-sm text-slate-500 font-bold">Total: {{ $deposits->count() }}</div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                <tr class="text-slate-400 font-extrabold">
                    <th class="text-left px-6 py-4">Tanggal</th>
                    <th class="text-left px-6 py-4">User</th>
                    <th class="text-left px-6 py-4">Nominal</th>
                    <th class="text-left px-6 py-4">Status</th>
                    <th class="text-right px-6 py-4">Aksi</th>
                </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                @forelse($deposits as $d)
                    @php
                        $badge = match($d->status){
                            'waiting_verification' => ['bg-amber-50 text-amber-700 border-amber-100','Waiting'],
                            'approved' => ['bg-emerald-50 text-emerald-700 border-emerald-100','Approved'],
                            'rejected' => ['bg-rose-50 text-rose-700 border-rose-100','Rejected'],
                            default => ['bg-slate-100 text-slate-700 border-slate-200', ucfirst($d->status)]
                        };
                    @endphp

                    <tr class="hover:bg-slate-50/60">
                        <td class="px-6 py-4 text-slate-700 font-bold">
                            {{ optional($d->submitted_at)->format('Y-m-d') ?? $d->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-extrabold text-slate-900">{{ $d->account->full_name }}</div>
                            <div class="text-xs text-slate-500 font-bold">Account ID: {{ $d->account_id }}</div>
                        </td>
                        <td class="px-6 py-4 font-extrabold text-slate-900">
                            Rp {{ number_format((int)$d->amount,0,',','.') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold border {{ $badge[0] }}">
                                {{ $badge[1] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.tabungan-umrah.deposits.show', $d->id) }}"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 bg-white font-extrabold text-slate-700 hover:bg-slate-50">
                                Detail
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center">
                                <i data-lucide="inbox" class="w-6 h-6 text-slate-500"></i>
                            </div>
                            <div class="mt-3 font-extrabold text-slate-900">Tidak ada data setoran</div>
                            <div class="text-sm text-slate-600 font-bold mt-1">Coba ganti filter status atau cek lagi nanti.</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
