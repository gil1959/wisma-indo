@extends('user.layouts.app')
@php $isEn = app()->getLocale() === 'en'; @endphp

@section('content')
<div class="space-y-5">
  <div class="flex items-start justify-between gap-3">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">{{ $isEn ? 'Commission Withdrawal' : 'Penarikan Komisi' }}</h1>
      <p class="mt-1 text-sm text-slate-600">{{ $isEn
      ? 'Request withdrawal of affiliate commission to a bank account or e-wallet. Track your request status below.'
      : 'Ajukan penarikan komisi affiliate ke rekening bank atau e-wallet. Pantau status request kamu di bawah.'
  }}</p>
    </div>
    <a href="{{ route('user.affiliate.commission') }}"
      class="px-4 py-2.5 rounded-2xl border border-slate-200 text-sm font-extrabold text-slate-700 bg-white hover:bg-slate-50">

      {{ $isEn ? 'View Commission' : 'Lihat Komisi' }}
    </a>
  </div>

  {{-- Alerts --}}
  @if(session('success'))
  <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 text-sm font-semibold">
    {{ session('success') }}
  </div>
  @endif

  @if($errors->any())
  <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl p-4 text-sm">
    <div class="font-extrabold mb-2">{{ $isEn ? 'Input errors:' : 'Ada kesalahan input:' }}</div>
    <ul class="list-disc pl-5 space-y-1">
      @foreach($errors->all() as $e)
      <li class="font-semibold">{{ $e }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  {{-- Balance + Form --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="bg-white border border-slate-200 rounded-2xl p-5">
      <div class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Available Commission Balance' : 'Saldo Komisi Tersedia' }}</div>
      <div class="mt-2 text-3xl font-extrabold text-slate-900">
        Rp {{ number_format((float)$balance, 0, ',', '.') }}
      </div>
      <div class="mt-3 text-xs text-slate-600 leading-relaxed">
        {{ $isEn ? 'Available balance is calculated from commissions' : 'Saldo tersedia dihitung dari komisi' }} <span class="font-extrabold text-slate-800">Approved</span>
        {{ $isEn ? 'minus the existing ones' : 'dikurangi yang sudah' }} <span class="font-extrabold text-slate-800">Paid</span> {{ $isEn ? 'and moderate' : 'dan yang sedang' }}
        <span class="font-extrabold text-slate-800">Pending/Approved</span>{{ $isEn ? 'in withdrawal.' : ' di penarikan.' }}
      </div>
      <div class="mt-4 p-3 rounded-2xl bg-slate-50 border border-slate-200">
        <div class="text-xs font-extrabold text-slate-700">{{ $isEn ? 'Note' : 'Catatan' }}</div>
        <div class="mt-1 text-xs text-slate-600">
          {{ $isEn
      ? 'Make sure payout details are valid. Incorrect requests may be declined by admin and slow down payout.'
      : 'Pastikan data payout valid. Request yang salah bisa ditolak admin dan memperlambat pencairan.'
  }}
        </div>
      </div>
    </div>

    <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-5">
      <div class="flex items-center justify-between gap-3">
        <div>
          <div class="text-sm font-extrabold text-slate-900">{{ $isEn ? 'Request Withdrawal' : 'Ajukan Penarikan' }}</div>
          <div class="text-xs text-slate-600 mt-1">{{ $isEn ? 'Minimum Rp 1. Amount must not exceed available balance.' : 'Minimal Rp 1. Nominal tidak boleh melebihi saldo tersedia.' }}</div>
        </div>
      </div>

      <form method="POST" action="{{ route('user.withdrawals.submit') }}"
        class="mt-4 space-y-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Amount' : 'Nominal' }}</label>
            <input name="amount" value="{{ old('amount') }}" required
              placeholder="{{ $isEn ? 'Example: 250000' : 'Contoh: 250000' }}"
              class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
            <div class="mt-2 text-xs text-slate-500">{{ $isEn ? 'Numbers only (no dots/commas).' : 'Gunakan angka saja (tanpa titik/koma).' }}</div>
          </div>

          <div>
            <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Method' : 'Metode' }}</label>
            <select name="payout_method" required
              class="mt-2 w-full rounded-2xl border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-800">
              <option value="bank" {{ old('payout_method','bank')==='bank' ? 'selected' : '' }}>Bank</option>
              <option value="ewallet" {{ old('payout_method')==='ewallet' ? 'selected' : '' }}>E-Wallet</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="text-xs font-extrabold text-slate-600 uppercase">Provider</label>
            <input name="payout_provider" value="{{ old('payout_provider') }}" required
              placeholder="Contoh: BCA / BRI / Mandiri / DANA / OVO / GoPay"
              class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
          </div>

          <div>
            <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Account Holder' : 'Atas Nama' }}</label>
            <input name="account_name" value="{{ old('account_name') }}" required
              placeholder="{{ $isEn ? 'Account holder name' : 'Nama pemilik rekening / e-wallet' }}"
              class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
          </div>
        </div>

        <div>
          <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Account No. / E-Wallet' : 'Nomor Rekening / E-Wallet' }}</label>
          <input name="account_number" value="{{ old('account_number') }}" required
            placeholder="Contoh: 1234567890"
            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
        </div>

        <div class="flex items-center justify-end gap-2">
          <button class="px-4 py-2.5 rounded-2xl font-extrabold text-white"
            style="background:#0194F3;">
            {{ $isEn ? 'Submit Request' : 'Kirim Permintaan' }}
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- Filter List --}}
  <div class="bg-white border border-slate-200 rounded-2xl p-4">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
      <div class="md:col-span-2">
        <label class="text-xs font-extrabold text-slate-600 uppercase">Search</label>
        <input name="q" value="{{ request('q') }}" {{ $isEn ? 'Search provider / name / number...' : 'Cari provider / nama / nomor...' }}"
          class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
      </div>

      <div>
        <label class="text-xs font-extrabold text-slate-600 uppercase">Status</label>
        <select name="status"
          class="mt-2 w-full rounded-2xl border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-800">
          <option value="" {{ request('status')==='' ? 'selected' : '' }}>{{ $isEn ? 'All' : 'Semua' }}</option>
          <option value="pending" {{ request('status')==='pending' ? 'selected' : '' }}>Pending</option>
          <option value="approved" {{ request('status')==='approved' ? 'selected' : '' }}>Approved</option>
          <option value="declined" {{ request('status')==='declined' ? 'selected' : '' }}>Declined</option>
          <option value="paid" {{ request('status')==='paid' ? 'selected' : '' }}>Paid</option>
        </select>
      </div>

      <div class="flex items-end gap-2">
        <button class="w-full px-4 py-2.5 rounded-2xl font-extrabold text-white"
          style="background:#0194F3;">
          {{ $isEn ? 'Apply' : 'Terapkan' }}
        </button>
        <a href="{{ route('user.withdrawals') }}"
          class="px-4 py-2.5 rounded-2xl border border-slate-200 text-sm font-extrabold text-slate-700 bg-white hover:bg-slate-50">
          Reset
        </a>
      </div>
    </form>
  </div>

  {{-- Requests --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="p-4 border-b border-slate-200">
      <div class="text-sm font-extrabold text-slate-900">{{ $isEn ? 'Withdrawal History' : 'Riwayat Penarikan' }}</div>
      <div class="text-xs text-slate-600 mt-1"> {{ $isEn
      ? 'Status is set by admin. If declined, check admin note (if any).'
      : 'Status ditentukan admin. Jika declined, cek catatan admin (jika ada).'
  }}</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">{{ $isEn ? 'Date' : 'Tanggal' }}</th>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">{{ $isEn ? 'Amount' : 'Nominal' }}</th>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Payout</th>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Status</th>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">{{ $isEn ? 'Admin Note' : 'Catatan Admin' }}</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-200">
          @forelse($reqs as $r)
          @php
          $badge = 'bg-slate-100 text-slate-700 border-slate-200';
          if($r->status==='pending') $badge='bg-amber-50 text-amber-800 border-amber-200';
          if($r->status==='approved') $badge='bg-blue-50 text-blue-800 border-blue-200';
          if($r->status==='declined') $badge='bg-rose-50 text-rose-800 border-rose-200';
          if($r->status==='paid') $badge='bg-emerald-50 text-emerald-800 border-emerald-200';
          @endphp

          <tr class="align-top">
            <td class="px-4 py-3 font-semibold text-slate-800">
              <div>{{ optional($r->created_at)->format('d M Y') }}</div>
              <div class="text-xs text-slate-600">{{ optional($r->created_at)->format('H:i') }}</div>
            </td>

            <td class="px-4 py-3 font-extrabold text-slate-900">
              Rp {{ number_format((float)$r->amount, 0, ',', '.') }}
            </td>

            <td class="px-4 py-3">
              <div class="text-xs font-extrabold uppercase text-slate-600">{{ $r->payout_method }}</div>
              <div class="mt-1 font-semibold text-slate-900">{{ $r->payout_provider }}</div>
              <div class="mt-1 text-xs text-slate-600">
                <span class="font-semibold">{{ $r->account_name }}</span> <br>
                <span class="font-mono">{{ $r->account_number }}</span>
              </div>
            </td>

            <td class="px-4 py-3">
              <span class="inline-flex items-center px-3 py-1 rounded-full border text-xs font-extrabold {{ $badge }}">
                {{ strtoupper($r->status) }}
              </span>
              @if($r->paid_at)
              <div class="mt-2 text-xs text-slate-600">Paid at: <span class="font-semibold">{{ \Carbon\Carbon::parse($r->paid_at)->format('d M Y, H:i') }}</span></div>
              @endif
            </td>

            <td class="px-4 py-3">
              <div class="text-xs text-slate-700">
                {{ $r->admin_note ?: '-' }}
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td class="px-4 py-6 text-slate-600" colspan="5">{{ $isEn ? 'No withdrawal requests yet.' : 'Belum ada permintaan penarikan.' }}</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-4">
      {{ $reqs->links() }}
    </div>
  </div>
</div>
@endsection