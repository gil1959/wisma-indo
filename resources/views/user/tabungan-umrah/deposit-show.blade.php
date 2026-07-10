@extends('user.layouts.app')
@php $isEn = app()->getLocale() === 'en'; @endphp

@section('title', $isEn ? 'Deposit Details' : 'Detail Setoran')
@section('page-title', $isEn ? 'Deposit Details' : 'Detail Setoran')
@section('page-subtitle', $isEn ? 'View deposit status and payment proof' : 'Lihat status dan bukti pembayaran setoran')


@section('content')
@php
$statusMap = [
'waiting_verification' => [$isEn ? 'Waiting Verification' : 'Menunggu Verifikasi', 'bg-amber-50 text-amber-700 border-amber-200'],
'approved' => [$isEn ? 'Approved' : 'Berhasil', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
'rejected' => [$isEn ? 'Rejected' : 'Ditolak', 'bg-rose-50 text-rose-700 border-rose-200'],
];
[$badgeLabel, $badgeCls] = $statusMap[$deposit->status] ?? ['-', 'bg-slate-50 text-slate-700 border-slate-200'];
@endphp

<div class="card p-5 relative overflow-hidden max-w-4xl">
    <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
        style="background: radial-gradient(circle, rgba(1,148,243,0.14) 0%, transparent 65%);"></div>

    <div class="flex items-start justify-between gap-3 relative">
        <div>
            <h2 class="text-sm font-extrabold text-slate-900">{{ $isEn ? 'Deposit Information' : 'Informasi Setoran' }}</h2>
            <p class="text-sm text-slate-500 mt-1">{{ $isEn ? 'Umrah savings deposit transaction details.' : 'Detail transaksi setoran tabungan umrah.' }}</p>
        </div>
        <span class="pill pill-azure shrink-0">
            <i data-lucide="receipt" class="w-4 h-4"></i>
            Deposit
        </span>
    </div>

    <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-4 relative">
        <div class="rounded-2xl border border-slate-200 p-4 bg-white">
            <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Date' : 'Tanggal' }}</div>
            <div class="mt-1 text-sm font-semibold text-slate-900">
                {{ optional($deposit->submitted_at)->format('d M Y H:i') ?? optional($deposit->created_at)->format('d M Y H:i') }}
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 p-4 bg-white">
            <div class="text-xs font-semibold text-slate-500">Nominal</div>
            <div class="mt-1 text-sm font-semibold text-slate-900">
                Rp {{ number_format((int)$deposit->amount, 0, ',', '.') }}
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 p-4 bg-white">
            <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Status' : 'Status' }}</div>
            <div class="mt-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-extrabold border {{ $badgeCls }}">
                    {{ $badgeLabel }}
                </span>
            </div>
        </div>
    </div>

    <div class="mt-5 rounded-2xl border border-slate-200 p-4 bg-white relative">
        <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Recipient Account' : 'Rekening Tujuan' }}</div>
        <div class="mt-1 text-sm font-semibold text-slate-900">
            {{ optional($deposit->paymentMethod)->bank_name ?? '-' }} -
            {{ optional($deposit->paymentMethod)->account_number ?? '-' }}
            ({{ optional($deposit->paymentMethod)->account_holder ?? '-' }})
        </div>

        @if($deposit->note)
        <div class="mt-3 text-xs font-semibold text-slate-500">{{ $isEn ? 'Admin Note' : 'Catatan Admin' }}</div>
        <div class="mt-1 text-sm text-slate-800">{{ $deposit->note }}</div>
        @endif
    </div>

    @if($deposit->proof_image)
    <div class="mt-5 relative">
        <div class="text-sm font-extrabold text-slate-900">{{ $isEn ? 'Payment Proof' : 'Bukti Pembayaran' }}</div>
        <div class="mt-3 rounded-2xl border border-slate-200 bg-white overflow-hidden">
            <img src="{{ asset('storage/'.$deposit->proof_image) }}" alt="{{ $isEn ? 'Payment proof' : 'Bukti pembayaran' }}" class="w-full">
        </div>
    </div>
    @endif

    <div class="mt-5">
        <a href="{{ route('user.tabungan-umrah.index') }}" class="btn btn-ghost">
            <i data-lucide="arrow-left" class="w-4 h-4" style="color:#0194F3;"></i>
            {{ $isEn ? 'Back' : 'Kembali' }}
        </a>
    </div>
</div>
@endsection