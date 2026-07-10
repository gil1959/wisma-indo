@extends('layouts.admin')

@section('content')
<div class="space-y-5">
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">Penarikan Komisi</h1>
      <p class="mt-1 text-sm text-slate-600">Approval permintaan withdraw dari affiliate.</p>
    </div>

    <form method="GET" class="flex flex-wrap gap-2">
      <input name="q" value="{{ $q }}" placeholder="Cari user"
        class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold">
      <select name="status" class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold">
  <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
  @foreach(['pending','approved','declined','paid'] as $st)
    <option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
  @endforeach
</select>

      <button class="px-4 py-2 rounded-2xl font-extrabold text-white" style="background:#0194F3;">Filter</button>
    </form>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50">
        <tr>
          <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">User</th>
          <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Amount</th>
          <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Payout</th>
          <th class="text-right px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-200">
        @forelse($requests as $r)
          <tr>
            <td class="px-4 py-3">
              <div class="font-bold text-slate-900">{{ $r->user->name }}</div>
              <div class="text-xs text-slate-600">{{ $r->user->email }}</div>
              @php
  $badge = [
    'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
    'approved' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
    'declined' => 'bg-red-50 text-red-800 border-red-200',
    'paid' => 'bg-blue-50 text-blue-800 border-blue-200',
  ][$r->status] ?? 'bg-slate-50 text-slate-700 border-slate-200';
@endphp

<div class="mt-1 inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-extrabold {{ $badge }}">
  {{ strtoupper($r->status) }}
</div>
            </td>
            <td class="px-4 py-3 font-semibold text-slate-800">
              Rp {{ number_format((float)$r->amount, 0, ',', '.') }}
            </td>
            <td class="px-4 py-3 text-slate-700 font-semibold">
              {{ strtoupper($r->payout_method) }} : {{ $r->payout_provider ?? '-' }}<br>
              <span class="text-xs text-slate-600">Atas nama :{{ $r->account_name }}</span><br>
              <span class="text-xs text-slate-600">Nomor :{{ $r->account_number }}</span>
            </td>
            <td class="px-4 py-3 text-right">
              <a href="{{ route('admin.affiliate.withdrawals.show', $r->id) }}"
                 class="px-3 py-2 rounded-2xl border border-slate-200 font-extrabold hover:bg-slate-50">
                Detail
              </a>
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="px-4 py-8 text-slate-600">Tidak ada data.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="p-4">{{ $requests->links() }}</div>
  </div>
</div>
@endsection
