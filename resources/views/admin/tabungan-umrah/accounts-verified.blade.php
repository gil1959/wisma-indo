@extends('layouts.admin')
@section('title', 'Tabungan Umrah - Akun Terverifikasi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-2xl font-extrabold text-slate-900">Tabungan Umrah</div>
            <div class="text-sm text-slate-600 font-bold mt-1">Akun yang sudah terverifikasi dan aktif menabung.</div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.tabungan-umrah.accounts.pending') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 bg-white font-extrabold text-slate-700 hover:bg-slate-50">
                <i data-lucide="clock-3" class="w-4 h-4"></i>
                Pending
            </a>

            <a href="{{ route('admin.tabungan-umrah.deposits.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl border border-slate-200 bg-white font-extrabold text-slate-700 hover:bg-slate-50">
                <i data-lucide="wallet" class="w-4 h-4"></i>
                Setoran
            </a>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.tabungan-umrah.accounts.pending') }}"
           class="px-4 py-2 rounded-2xl font-extrabold {{ request()->routeIs('admin.tabungan-umrah.accounts.pending') ? 'bg-brand-500 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
            Pending
        </a>

        <a href="{{ route('admin.tabungan-umrah.accounts.verified') }}"
           class="px-4 py-2 rounded-2xl font-extrabold {{ request()->routeIs('admin.tabungan-umrah.accounts.verified') ? 'bg-brand-500 text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
            Terverifikasi
            <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-extrabold {{ request()->routeIs('admin.tabungan-umrah.accounts.verified') ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">
                {{ $accounts->count() }}
            </span>
        </a>

        <a href="{{ route('admin.tabungan-umrah.deposits.index') }}"
           class="px-4 py-2 rounded-2xl font-extrabold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">
            Setoran
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
            <div class="font-extrabold text-emerald-700">Sukses</div>
            <div class="text-sm mt-1 text-emerald-700">{{ session('success') }}</div>
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <div class="font-extrabold text-slate-900">Akun Terverifikasi</div>
            <div class="text-sm text-slate-500 font-bold">Total: {{ $accounts->count() }}</div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                <tr class="text-slate-400 font-extrabold">
                    <th class="text-left px-6 py-4">Nama</th>
                    <th class="text-left px-6 py-4">Target</th>
                    <th class="text-left px-6 py-4">Keberangkatan</th>
                    <th class="text-left px-6 py-4">Status</th>
                    <th class="text-right px-6 py-4">Aksi</th>
                </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                @forelse($accounts as $a)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-6 py-4">
                            <div class="font-extrabold text-slate-900">{{ $a->full_name }}</div>
                            <div class="text-xs text-slate-500 font-bold">WhatsApp: {{ $a->whatsapp }}</div>
                        </td>
                        <td class="px-6 py-4 font-extrabold text-slate-900">
                            Rp {{ number_format((int)$a->target_amount,0,',','.') }}
                        </td>
                        <td class="px-6 py-4 text-slate-700 font-bold">
                            {{ $a->target_departure_date ? $a->target_departure_date->format('Y-m-d') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $badge = match($a->status){
                                    'verified' => ['bg-emerald-50 text-emerald-700 border-emerald-100','Terverifikasi'],
                                    'suspended' => ['bg-slate-100 text-slate-700 border-slate-200','Suspended'],
                                    default => ['bg-slate-100 text-slate-700 border-slate-200', ucfirst($a->status)]
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold border {{ $badge[0] }}">
                                {{ $badge[1] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
    <a href="{{ route('admin.tabungan-umrah.accounts.edit', $a->id) }}"
       class="px-4 py-2 rounded-xl bg-brand-500 text-white font-extrabold hover:bg-brand-600">
        Edit
    </a>

    <a href="{{ route('admin.tabungan-umrah.accounts.show', $a->id) }}"
       class="px-4 py-2 rounded-xl border border-slate-200 font-extrabold text-slate-700 hover:bg-slate-50">
        Detail →
    </a>
</td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center">
                                <i data-lucide="inbox" class="w-6 h-6 text-slate-500"></i>
                            </div>
                            <div class="mt-3 font-extrabold text-slate-900">Belum ada akun terverifikasi</div>
                            <div class="text-sm text-slate-600 font-bold mt-1">Akun yang sudah diverifikasi akan tampil di sini.</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
