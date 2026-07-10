@extends('partner.layouts.app')

@section('title', 'Withdraw')
@section('page-subtitle', 'Saldo & Penarikan')
@section('page-title', 'Withdraw')

@section('content')
<div class="space-y-5">

 {{-- Validation Errors only (flash success/error ditangani oleh layout agar tidak dobel) --}}
@if ($errors->any())
  <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800 text-sm">
    <div class="font-extrabold">Validasi gagal:</div>
    <ul class="mt-2 list-disc pl-5 space-y-1">
      @foreach ($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif


  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
      <div class="text-xs font-extrabold text-slate-600 uppercase">Saldo Tersedia</div>
      <div class="mt-2 text-3xl font-extrabold text-slate-900">
        Rp {{ number_format($available, 0, ',', '.') }}
      </div>
      <div class="mt-2 text-xs text-slate-600">
        Ini saldo bersih (sudah dipotong pajak partner).
      </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
      <div class="text-xs font-extrabold text-slate-600 uppercase">Saldo Pending</div>
      <div class="mt-2 text-3xl font-extrabold text-slate-900">
        Rp {{ number_format($pending, 0, ',', '.') }}
      </div>
      <div class="mt-2 text-xs text-slate-600">
        Total request withdraw yang masih menunggu diproses.
      </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
      <div class="text-xs font-extrabold text-slate-600 uppercase">Sudah Ditarik</div>
      <div class="mt-2 text-3xl font-extrabold text-slate-900">
        Rp {{ number_format($withdrawn, 0, ',', '.') }}
      </div>
      <div class="mt-2 text-xs text-slate-600">
        Total kumulatif request yang disetujui admin.
      </div>
    </div>
  </div>

  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <div>
        <div class="text-xl font-extrabold text-slate-900">Ajukan Penarikan Saldo</div>
        <div class="mt-1 text-sm text-slate-600">
          Pajak partner saat ini: <span class="font-extrabold text-slate-900">{{ number_format($taxPercent, 2) }}%</span>
        </div>
      </div>

      <a href="{{ route('partner.withdraw.requests') }}"
         class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-extrabold text-slate-800 hover:bg-slate-50">
        <i data-lucide="list" class="w-4 h-4" style="color:#0194F3;"></i>
        Lihat Request
      </a>
    </div>

    <form class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4" method="POST" action="{{ route('partner.withdraw.store') }}">
      @csrf

      <div class="md:col-span-2">
        <label class="text-xs font-extrabold text-slate-700 uppercase">Jumlah yang ingin ditarik</label>
        <input name="amount" type="number" min="10000" step="1000"
               value="{{ old('amount') }}"
               class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-300"
               placeholder="Contoh: 250000">
        <div class="mt-1 text-xs text-slate-500">Maks: Rp {{ number_format($available, 0, ',', '.') }}</div>
      </div>

      <div>
        <label class="text-xs font-extrabold text-slate-700 uppercase">Email</label>
        <input name="email" type="email"
               value="{{ old('email', $user->email) }}"
               class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-300">
      </div>

      <div>
        <label class="text-xs font-extrabold text-slate-700 uppercase">Nama Bank</label>
        <input name="bank_name" type="text"
               value="{{ old('bank_name', $user->partner_bank_name) }}"
               class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-300"
               placeholder="Contoh: BCA">
      </div>

      <div>
        <label class="text-xs font-extrabold text-slate-700 uppercase">Nomor Rekening</label>
        <input name="account_number" type="text"
               value="{{ old('account_number', $user->partner_bank_account_number) }}"
               class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-300"
               placeholder="Contoh: 1234567890">
      </div>

      <div>
        <label class="text-xs font-extrabold text-slate-700 uppercase">Nama Pemilik Rekening</label>
        <input name="account_holder" type="text"
               value="{{ old('account_holder', $user->partner_bank_account_holder ?? $user->name) }}"
               class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-300">
      </div>

      <div class="md:col-span-2">
        <label class="text-xs font-extrabold text-slate-700 uppercase">Konfirmasi Password</label>
        <input name="password" type="password"
               class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-sky-300"
               placeholder="Masukkan password untuk konfirmasi">
        
      </div>

      <div class="md:col-span-2 flex items-center justify-end gap-2">
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl px-5 py-3 text-sm font-extrabold text-white shadow-sm transition"
                style="background:#0194F3;"
                onmouseover="this.style.background='#0186DB'"
                onmouseout="this.style.background='#0194F3'">
          <i data-lucide="send" class="w-4 h-4"></i>
          Submit Request
        </button>
      </div>
    </form>
  </div>

  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <div class="flex items-center justify-between">
      <div class="text-lg font-extrabold text-slate-900">Request Terbaru</div>
      <a href="{{ route('partner.withdraw.requests') }}" class="text-sm font-extrabold text-sky-600 hover:underline">Lihat semua</a>
    </div>

    <div class="mt-4 overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left text-xs uppercase text-slate-500">
            <th class="py-2 pr-4 font-extrabold">Tanggal</th>
            <th class="py-2 pr-4 font-extrabold">Jumlah</th>
            <th class="py-2 pr-4 font-extrabold">Status</th>
            <th class="py-2 pr-4 font-extrabold">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse ($recent as $r)
            <tr>
              <td class="py-3 pr-4 font-bold text-slate-800">{{ $r->created_at->format('d M Y H:i') }}</td>
              <td class="py-3 pr-4 font-extrabold text-slate-900">Rp {{ number_format($r->amount, 0, ',', '.') }}</td>
              <td class="py-3 pr-4">
                @php
                  $badge = $r->status === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : ($r->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200');
                @endphp
                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-extrabold {{ $badge }}">
                  {{ strtoupper($r->status) }}
                </span>
              </td>
              <td class="py-3 pr-4">
                <a href="{{ route('partner.withdraw.show', $r->id) }}" class="text-sky-600 font-extrabold hover:underline">Detail</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="py-6 text-center text-slate-500 font-bold">Belum ada request.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
