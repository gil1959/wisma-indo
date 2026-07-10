@extends('partner.layouts.app')

@section('title', 'Withdraw Detail')
@section('page-subtitle', 'Detail Request')
@section('page-title', 'Withdraw Detail')

@section('content')
<div class="space-y-5">

  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xl font-extrabold text-slate-900">Request #{{ $w->id }}</div>
        <div class="mt-1 text-sm text-slate-600">Dibuat: {{ $w->created_at->format('d M Y H:i') }}</div>
      </div>
      <a href="{{ route('partner.withdraw.requests') }}"
         class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-extrabold text-slate-800 hover:bg-slate-50">
        <i data-lucide="arrow-left" class="w-4 h-4" style="color:#0194F3;"></i>
        Kembali
      </a>
    </div>

    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
        <div class="text-xs font-extrabold text-slate-600 uppercase">Jumlah</div>
        <div class="mt-2 text-2xl font-extrabold text-slate-900">Rp {{ number_format($w->amount, 0, ',', '.') }}</div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
        <div class="text-xs font-extrabold text-slate-600 uppercase">Status</div>
        @php
          $badge = $w->status === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : ($w->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200');
        @endphp
        <div class="mt-2">
          <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-extrabold {{ $badge }}">
            {{ strtoupper($w->status) }}
          </span>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4">
        <div class="text-xs font-extrabold text-slate-600 uppercase">Email</div>
        <div class="mt-2 font-bold text-slate-900">{{ $w->email }}</div>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-4">
        <div class="text-xs font-extrabold text-slate-600 uppercase">Rekening</div>
        <div class="mt-2 font-bold text-slate-900">
          {{ $w->bank_name }} — {{ $w->account_number }} ({{ $w->account_holder }})
        </div>
      </div>

      <div class="md:col-span-2 rounded-2xl border border-slate-200 bg-white p-4">
        <div class="text-xs font-extrabold text-slate-600 uppercase">Catatan Admin</div>
        <div class="mt-2 text-sm text-slate-800 font-bold">
          {{ $w->admin_note ?: '-' }}
        </div>
        @if ($w->reviewed_at)
          <div class="mt-2 text-xs text-slate-500">
            Direview: {{ $w->reviewed_at->format('d M Y H:i') }}
          </div>
        @endif
      </div>
    </div>
  </div>

</div>
@endsection
