@extends('layouts.admin')

@section('content')
<div class="space-y-5">

  @if(session('success'))
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
      <div class="font-extrabold">Sukses</div>
      <div class="text-sm mt-1">{{ session('success') }}</div>
    </div>
  @endif

  @if(session('error'))
    <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800">
      <div class="font-extrabold">Gagal</div>
      <div class="text-sm mt-1">{{ session('error') }}</div>
    </div>
  @endif

  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">Withdrawal #{{ $w->id }}</h1>
      <p class="mt-1 text-sm text-slate-600">
        Created: {{ $w->created_at?->format('d M Y H:i') }}
        @if($w->reviewed_at)
          • Reviewed: {{ $w->reviewed_at->format('d M Y H:i') }}
        @endif
      </p>
    </div>

    <a href="{{ route('admin.partner_withdrawals.index') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-extrabold text-slate-800 hover:bg-slate-50">
      <i data-lucide="arrow-left" class="w-4 h-4" style="color:#0194F3;"></i>
      Back
    </a>
  </div>

  @php
    $badge = [
      'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
      'approved' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
      'rejected' => 'bg-rose-50 text-rose-800 border-rose-200',
    ][$w->status] ?? 'bg-slate-50 text-slate-800 border-slate-200';
  @endphp

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
      <div class="text-xs font-extrabold text-slate-600 uppercase">Partner</div>
      <div class="mt-2 font-extrabold text-slate-900">{{ $w->partner?->name ?? '-' }}</div>
      <div class="text-sm text-slate-600 mt-1">{{ $w->partner?->email ?? '-' }}</div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
      <div class="text-xs font-extrabold text-slate-600 uppercase">Amount</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900">
        Rp {{ number_format($w->amount, 0, ',', '.') }}
      </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
      <div class="text-xs font-extrabold text-slate-600 uppercase">Status</div>
      <div class="mt-2">
        <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-extrabold {{ $badge }}">
          {{ strtoupper($w->status) }}
        </span>
      </div>
    </div>

    <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-lg font-extrabold text-slate-900">Payout Info</div>
          <div class="mt-1 text-sm text-slate-600">Data rekening yang di-submit partner (snapshot).</div>
        </div>
      </div>

      <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
          <div class="text-xs font-extrabold text-slate-600 uppercase">Email</div>
          <div class="mt-1 font-extrabold text-slate-900">{{ $w->email ?? '-' }}</div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
          <div class="text-xs font-extrabold text-slate-600 uppercase">Bank</div>
          <div class="mt-1 font-extrabold text-slate-900">{{ $w->bank_name ?? '-' }}</div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
          <div class="text-xs font-extrabold text-slate-600 uppercase">Nomor Rekening</div>
          <div class="mt-1 font-extrabold text-slate-900">{{ $w->account_number ?? '-' }}</div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
          <div class="text-xs font-extrabold text-slate-600 uppercase">Nama Pemilik</div>
          <div class="mt-1 font-extrabold text-slate-900">{{ $w->account_holder ?? '-' }}</div>
        </div>
      </div>

      <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4">
        <div class="text-xs font-extrabold text-slate-600 uppercase">Admin Note</div>
        <div class="mt-2 text-sm font-bold text-slate-800">{{ $w->admin_note ?: '-' }}</div>
      </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
      <div class="text-lg font-extrabold text-slate-900">Action</div>
      <p class="mt-1 text-sm text-slate-600">Hanya bisa diproses jika status masih pending.</p>

      <form method="POST" action="{{ route('admin.partner_withdrawals.update', $w->id) }}" class="mt-4 space-y-3">
        @csrf
        @method('PUT')

        <div>
          <label class="block text-sm font-bold text-slate-800 mb-2">Catatan Admin (opsional)</label>
          <textarea name="admin_note" rows="4"
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-200"
            placeholder="Tulis catatan untuk partner...">{{ old('admin_note', $w->admin_note) }}</textarea>
        </div>

        <div class="flex flex-col gap-2">
          <button name="action" value="approve"
                  class="inline-flex items-center justify-center gap-2 rounded-2xl px-4 py-3 text-sm font-extrabold text-white disabled:opacity-60"
                  style="background:#0194F3;"
                  onmouseover="this.style.background='#0186DB'"
                  onmouseout="this.style.background='#0194F3'"
                  {{ $w->status!=='pending' ? 'disabled' : '' }}>
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            Approve
          </button>

          <button name="action" value="reject"
                  class="inline-flex items-center justify-center gap-2 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-extrabold text-rose-700 hover:bg-rose-100 disabled:opacity-60"
                  {{ $w->status!=='pending' ? 'disabled' : '' }}>
            <i data-lucide="x-circle" class="w-4 h-4"></i>
            Reject
          </button>
        </div>
      </form>

      <form class="mt-3" method="POST" action="{{ route('admin.partner_withdrawals.destroy', $w->id) }}"
            onsubmit="return confirm('Hapus request ini?')">
        @csrf
        @method('DELETE')
        <button class="w-full inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-extrabold text-slate-800 hover:bg-slate-50">
          <i data-lucide="trash-2" class="w-4 h-4"></i>
          Delete Request
        </button>
      </form>
    </div>

  </div>
</div>
@endsection
