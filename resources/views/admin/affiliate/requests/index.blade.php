@extends('layouts.admin')

@section('content')
<div class="space-y-5">
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">Pengajuan Affiliate</h1>
      <p class="mt-1 text-sm text-slate-600">Daftar user yang meminta akses fitur affiliate.</p>
    </div>
    <form class="flex gap-2" method="GET">
      <input name="q" value="{{ $q }}" placeholder="Cari nama/email/telepon"
        class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-800">
      <button class="px-4 py-2 rounded-2xl font-extrabold text-white" style="background:#0194F3;">Cari</button>
    </form>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50">
        <tr>
          <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">User</th>
          <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Diajukan</th>
          <th class="text-right px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-200">
        @forelse($users as $u)
          <tr>
            <td class="px-4 py-3">
              <div class="font-bold text-slate-900">{{ $u->name }}</div>
              <div class="text-xs text-slate-600">{{ $u->email }} • {{ $u->phone }}</div>
            </td>
            <td class="px-4 py-3 text-slate-700 font-semibold">
              {{ optional($u->affiliate_requested_at)->format('d M Y H:i') ?? '-' }}
            </td>
            <td class="px-4 py-3 text-right">
              <a href="{{ route('admin.affiliate.requests.show', $u->id) }}"
                 class="px-3 py-2 rounded-2xl border border-slate-200 font-extrabold text-slate-800 hover:bg-slate-50">
                Detail
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="px-4 py-8 text-slate-600">Tidak ada pengajuan.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div class="p-4">
      {{ $users->links() }}
    </div>
  </div>
</div>
@endsection
