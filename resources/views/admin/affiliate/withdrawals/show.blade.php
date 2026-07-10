@extends('layouts.admin')

@section('content')
<div class="max-w-4xl space-y-5">
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">Detail Penarikan</h1>
      <p class="mt-1 text-sm text-slate-600">Status: {{ $req->status }}</p>
    </div>
    <a href="{{ route('admin.affiliate.withdrawals.index') }}"
       class="px-4 py-2 rounded-2xl border border-slate-200 font-extrabold hover:bg-slate-50">
      Kembali
    </a>
  </div>
  @if (session('success'))
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-extrabold text-emerald-800">
      {{ session('success') }}
    </div>
  @endif

  @if (session('error'))
    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-extrabold text-red-800">
      {{ session('error') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-extrabold text-red-800">
      {{ $errors->first() }}
    </div>
  @endif

  <div class="bg-white border border-slate-200 rounded-2xl p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <div class="text-xs font-extrabold text-slate-600 uppercase">User</div>
        <div class="mt-1 font-bold text-slate-900">{{ $req->user->name }}</div>
        <div class="text-sm text-slate-700">{{ $req->user->email }}</div>
         <div class="text-sm text-slate-700">{{ $req->user->phone }}</div>
      </div>
      <div>
        <div class="text-xs font-extrabold text-slate-600 uppercase">Amount</div>
        <div class="mt-1 font-bold text-slate-900">Rp {{ number_format((float)$req->amount, 0, ',', '.') }}</div>
      </div>
      <div class="md:col-span-2">
        <div class="text-xs font-extrabold text-slate-600 uppercase">Payout Detail</div>
        <div class="mt-2 border border-slate-200 rounded-2xl p-4 bg-slate-50 text-sm">
          <div><b>Method:</b> {{ strtoupper($req->payout_method) }}</div>
          <div><b>Provider:</b> {{ $req->payout_provider ?? '-' }}</div>
          <div><b>Account:</b> {{ $req->account_name }}</div>
          <div><b>NO:</b>{{ $req->account_number }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="bg-white border border-slate-200 rounded-2xl p-6">
    <h2 class="font-extrabold text-slate-900">Update Status</h2>
    <form method="POST" action="{{ route('admin.affiliate.withdrawals.status', $req->id) }}" class="mt-4 space-y-3">
      @csrf
      <div>
        <label class="text-xs font-extrabold text-slate-600 uppercase">Status</label>
        @php($st = old('status', $req->status))
<select name="status" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold">
  @if($req->status === 'pending')
    <option value="pending" selected disabled>Pending</option>
  @endif
  <option value="approved" {{ $st === 'approved' ? 'selected' : '' }}>Approved</option>
  <option value="declined" {{ $st === 'declined' ? 'selected' : '' }}>Declined</option>
  <option value="paid" {{ $st === 'paid' ? 'selected' : '' }}>Paid</option>
</select>

      </div>
      <div>
        <label class="text-xs font-extrabold text-slate-600 uppercase">Admin Note (opsional)</label>
        <textarea name="admin_note" rows="4"
  class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold">{{ old('admin_note', $req->admin_note) }}</textarea>

      </div>
      <button class="px-4 py-2.5 rounded-2xl font-extrabold text-white" style="background:#0194F3;">
        Simpan
      </button>
    </form>
  </div>
</div>
@endsection
