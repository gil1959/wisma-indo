@extends('user.layouts.app')
@php $isEn = app()->getLocale() === 'en'; @endphp

@section('content')
<div class="space-y-5">
  <div>
    <h1 class="text-2xl font-extrabold text-slate-900">{{ $isEn ? 'Affiliate' : 'Affiliate' }}</h1>
    <p class="mt-1 text-sm text-slate-600">{{ $isEn ? 'Your affiliate performance summary.' : 'Ringkasan performa affiliate kamu.' }}</p>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white p-4 rounded-2xl border border-slate-200">
      <div class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Links' : 'Link' }}</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($stats['links']) }}</div>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-slate-200">
      <div class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Clicks' : 'Klik' }}</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($stats['clicks']) }}</div>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-slate-200">
      <div class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Conversions' : 'Konversi' }}</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($stats['conversions']) }}</div>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white p-4 rounded-2xl border border-slate-200">
      <div class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Pending Commission' : 'Komisi Pending' }}</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900">Rp {{ number_format((int)$stats['commission_pending'], 0, ',', '.') }}</div>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-200">
      <div class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Approved Commission' : 'Komisi Approved' }}</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900">Rp {{ number_format((int)$stats['commission_approved'], 0, ',', '.') }}</div>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-200">
      <div class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Paid Commission' : 'Komisi Paid' }}</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900">Rp {{ number_format((int)$stats['commission_paid'], 0, ',', '.') }}</div>
    </div>
  </div>
</div>
@endsection