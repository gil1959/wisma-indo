@extends('user.layouts.app')
@php $isEn = app()->getLocale() === 'en'; @endphp

@section('title', $isEn ? 'Umrah Savings' : 'Tabungan Umrah')
@section('page-title', $isEn ? 'Umrah Savings' : 'Tabungan Umrah')
@section('page-subtitle', $isEn ? 'Track savings progress and deposit history' : 'Pantau progres tabungan dan riwayat setoran')


@section('content')
@if(session('success'))
<div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
    <div class="font-extrabold text-emerald-700">{{ $isEn ? 'Success' : 'Sukses' }}</div>
    <div class="text-sm mt-1 text-emerald-700">{{ session('success') }}</div>
</div>
@endif

@php
$lastAmount = (int) optional($lastDeposit)->amount;
$lastStatus = optional($lastDeposit)->status;

$statusMap = [
'waiting_verification' => [$isEn ? 'Waiting Verification' : 'Menunggu Verifikasi', 'bg-amber-50 text-amber-700 border-amber-200'],
'approved' => [$isEn ? 'Approved' : 'Berhasil', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
'rejected' => [$isEn ? 'Rejected' : 'Ditolak', 'bg-rose-50 text-rose-700 border-rose-200'],
];

@endphp

{{-- TOP STATS (ikut pattern dashboard user) --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-5">

    {{-- Progres --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition relative overflow-hidden">
        <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
            style="background: radial-gradient(circle, rgba(1,148,243,0.16) 0%, transparent 65%);"></div>

        <div class="flex items-start justify-between relative">
            <div class="min-w-0">
                <div class="text-xs font-extrabold text-slate-500">{{ $isEn ? 'Savings Progress' : 'Progres Tabungan' }}</div>
                <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ (int) $progress }}%</div>
                <div class="mt-1 text-xs text-slate-500">
                    {{ $isEn ? 'Target' : 'Target' }}: <span class="font-semibold">Rp {{ number_format((int)$target, 0, ',', '.') }}</span>

                </div>
            </div>

            <div class="icon-badge">
                <i data-lucide="line-chart" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="mt-4 w-full h-2 rounded-full bg-slate-100 overflow-hidden relative">
            <div class="h-2 rounded-full" style="background:#0194F3; width: {{ (int)$progress }}%;"></div>
        </div>
    </div>

    {{-- Saldo --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition relative overflow-hidden">
        <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
            style="background: radial-gradient(circle, rgba(1,148,243,0.14) 0%, transparent 65%);"></div>

        <div class="flex items-start justify-between relative">
            <div class="min-w-0">
                <div class="text-xs font-extrabold text-slate-500">{{ $isEn ? 'Current Balance (Approved)' : 'Saldo Saat Ini (Approved)' }}</div>
                <div class="mt-2 text-2xl font-extrabold" style="color:#0194F3;">
                    Rp {{ number_format((int)$approvedTotal, 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs text-slate-500">
                    Keberangkatan:
                    <span class="font-semibold text-slate-900">
                        {{ $account->target_departure_date ? $account->target_departure_date->format('d M Y') : '-' }}
                    </span>
                </div>
            </div>

            <div class="icon-badge">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    {{-- Setoran terakhir --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition relative overflow-hidden">
        <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
            style="background: radial-gradient(circle, rgba(1,148,243,0.12) 0%, transparent 65%);"></div>

        <div class="flex items-start justify-between relative">
            <div class="min-w-0">
                <div class="text-xs font-extrabold text-slate-500">{{ $isEn ? 'Latest Deposit' : 'Setoran Terakhir' }}</div>
                <div class="mt-2 text-2xl font-extrabold text-slate-900">
                    Rp {{ number_format($lastAmount, 0, ',', '.') }}
                </div>

                @php
                $badge = $statusMap[$lastStatus] ?? ['-', 'bg-slate-50 text-slate-700 border-slate-200'];
                [$badgeLabel, $badgeCls] = $badge;
                @endphp

                <div class="mt-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-extrabold border {{ $badgeCls }}">
                        {{ $lastDeposit ? $badgeLabel : ($isEn ? 'No deposits yet' : 'Belum ada setoran') }}
                    </span>
                </div>
            </div>

            <div class="icon-badge">
                <i data-lucide="receipt" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    {{-- Tambah setoran --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition relative overflow-hidden">
        <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
            style="background: radial-gradient(circle, rgba(1,148,243,0.18) 0%, transparent 65%);"></div>

        <div class="flex items-start justify-between relative">
            <div class="min-w-0">
                <div class="text-xs font-extrabold text-slate-500">{{ $isEn ? 'Action' : 'Aksi' }}</div>
                <div class="mt-2 text-sm font-extrabold text-slate-900">{{ $isEn ? 'Add Deposit' : 'Tambah Setoran' }}</div>

                <div class="mt-1 text-xs text-slate-500"> {{ $isEn ? 'Upload proof of payment' : 'Upload bukti pembayaran' }}</div>

                <div class="mt-4">
                    <a href="{{ route('user.tabungan-umrah.deposits.create') }}"
                        class="text-xs font-extrabold hover:underline decoration-[#0194F3]"
                        style="color:#0194F3;">
                        {{ $isEn ? 'Make Deposit →' : 'Buat Setoran →' }}
                    </a>
                </div>
            </div>

            <div class="icon-badge">
                <i data-lucide="plus" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    {{-- Cetak rekening koran --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition relative overflow-hidden">
        <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
            style="background: radial-gradient(circle, rgba(15,23,42,0.10) 0%, transparent 65%);"></div>

        <div class="flex items-start justify-between relative">
            <div class="min-w-0">
                <div class="text-xs font-extrabold text-slate-500"> {{ $isEn ? 'Document' : 'Dokumen' }}</div>
                <div class="mt-2 text-sm font-extrabold text-slate-900">{{ $isEn ? 'bank statement' : 'Rekening Koran' }}</div>
                <div class="mt-1 text-xs text-slate-500">{{ $isEn ? 'Print savings statement (A4)' : 'Cetak mutasi tabungan (A4)' }}</div>

                <div class="mt-4">
                    <a href="{{ route('user.tabungan-umrah.statement.print') }}" target="_blank"
                        class="text-xs font-extrabold hover:underline decoration-slate-900 text-slate-900">
                        {{ $isEn ? 'Print now →' : 'Cetak sekarang →' }}
                    </a>
                </div>
            </div>

            <div class="icon-badge">
                <i data-lucide="printer" class="w-6 h-6"></i>
            </div>
        </div>
    </div>


</div>

{{-- Riwayat transaksi (ikut pattern tabel dashboard) --}}
<div class="card p-5">
    <div class="flex items-center justify-between gap-3">
        <h2 class="text-sm font-extrabold text-slate-900">{{ $isEn ? 'Deposit History' : 'Riwayat Setoran' }}</h2>
        <span class="text-xs font-extrabold px-3 py-1 rounded-full border shrink-0"
            style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
            History
        </span>
    </div>

    <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="text-left px-5 py-3 font-bold">{{ $isEn ? 'Date' : 'Tanggal' }}</th>
                    <th class="text-left px-5 py-3 font-bold">{{ $isEn ? 'Nominal' : 'Nominal' }}</th>
                    <th class="text-left px-5 py-3 font-bold">{{ $isEn ? 'Status' : 'Status' }}</th>
                    <th class="text-right px-5 py-3 font-bold">{{ $isEn ? 'Action' : 'Aksi' }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($deposits as $d)
                @php
                $badge = $statusMap[$d->status] ?? ['-', 'bg-slate-50 text-slate-700 border-slate-200'];
                [$badgeLabel, $badgeCls] = $badge;
                @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3">
                        <div class="font-semibold text-slate-900">
                            {{ optional($d->submitted_at)->format('d M Y H:i') ?? optional($d->created_at)->format('d M Y H:i') }}
                        </div>
                        <div class="text-xs text-slate-500 mt-0.5">ID: {{ $d->id }}</div>
                    </td>
                    <td class="px-5 py-3 font-semibold text-slate-900">
                        Rp {{ number_format((int)$d->amount, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-extrabold border {{ $badgeCls }}">
                            {{ $badgeLabel }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <a class="font-bold hover:underline decoration-[#0194F3]" style="color:#0194F3;"
                            href="{{ route('user.tabungan-umrah.deposits.show', $d) }}">
                            Detail →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-8 text-center text-slate-500">
                        {{ $isEn ? 'No deposits yet.' : 'Belum ada setoran.' }}

                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $deposits->links() }}
    </div>
</div>
@endsection