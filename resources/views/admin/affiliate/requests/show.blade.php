@extends('layouts.admin')

@section('content')
<div class="max-w-4xl space-y-5">
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">Detail Pengajuan</h1>
      <p class="mt-1 text-sm text-slate-600">Review data user dan lakukan approve/decline.</p>
    </div>
    <a href="{{ route('admin.affiliate.requests.index') }}"
       class="px-4 py-2 rounded-2xl border border-slate-200 font-extrabold text-slate-800 hover:bg-slate-50">
      Kembali
    </a>
  </div>

  <div class="bg-white border border-slate-200 rounded-2xl p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <div class="text-xs font-extrabold text-slate-600 uppercase">Nama</div>
        <div class="mt-1 font-bold text-slate-900">{{ $user->name }}</div>
      </div>
      <div>
        <div class="text-xs font-extrabold text-slate-600 uppercase">Kontak</div>
        <div class="mt-1 font-semibold text-slate-800">{{ $user->email }}  {{ $user->phone }}</div>
      </div>
      <div class="md:col-span-2">
        <div class="text-xs font-extrabold text-slate-600 uppercase">Catatan Pengajuan</div>
        <div class="mt-2 whitespace-pre-line text-sm text-slate-800 border border-slate-200 rounded-2xl p-4 bg-slate-50">
          {{ $user->affiliate_review_note ?? '-' }}
        </div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div class="bg-white border border-slate-200 rounded-2xl p-6">
  <h2 class="font-extrabold text-slate-900">Approve</h2>
  <p class="mt-1 text-sm text-slate-600">
    Approve hanya untuk mengaktifkan akses fitur affiliate. Komisi diatur per order.
  </p>

  <form method="POST" action="{{ route('admin.affiliate.requests.approve', $user->id) }}" class="mt-4 space-y-3">
    @csrf
    <div>
      <label class="text-xs font-extrabold text-slate-600 uppercase">Note (opsional)</label>
      <textarea name="note" rows="4"
        class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold"></textarea>
    </div>

    <button class="px-4 py-2.5 rounded-2xl font-extrabold text-white" style="background:#16a34a;">
      Approve
    </button>
  </form>
</div>


    <div class="bg-white border border-slate-200 rounded-2xl p-6">
      <h2 class="font-extrabold text-slate-900">Decline</h2>
      <form method="POST" action="{{ route('admin.affiliate.requests.decline', $user->id) }}" class="mt-4 space-y-3">
        @csrf
        <div>
          <label class="text-xs font-extrabold text-slate-600 uppercase">Alasan Penolakan</label>
          <textarea name="note" required rows="5"
            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold"
            placeholder="Tulis alasan jelas agar user bisa memperbaiki pengajuan."></textarea>
        </div>
        <button class="px-4 py-2.5 rounded-2xl font-extrabold text-white" style="background:#ef4444;">
          Decline
        </button>
      </form>
    </div>
  </div>
</div>
@endsection
