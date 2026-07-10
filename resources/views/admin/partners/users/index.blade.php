@extends('layouts.admin')

@section('content')
<div class="space-y-5">
    @if(session('success'))
    <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
        <div class="font-extrabold">Sukses</div>
        <div class="text-sm mt-1">{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800">
        <div class="font-extrabold">Gagal</div>
        <div class="text-sm mt-1">{{ session('error') }}</div>
    </div>
@endif

    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Users Partner</h1>
            <p class="mt-1 text-sm text-slate-600">Kelola akun partner: pajak, suspend, dan edit akun.</p>
        </div>

        <form class="flex gap-2" method="GET">
            <input name="q" value="{{ $q }}" placeholder="Cari nama/email/telepon"
                   class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-800 bg-white">
            <button class="px-4 py-2 rounded-2xl font-extrabold text-white" style="background:#0194F3;">Cari</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Partner</th>
                    <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Pajak</th>
                    <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Status</th>
                    <th class="text-right px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $u)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-4 py-3">
                            <div class="font-extrabold text-slate-900">{{ $u->name }}</div>
                            <div class="text-sm text-slate-700 font-semibold">{{ $u->email }}</div>
                            <div class="text-xs text-slate-500">{{ $u->phone ?? '-' }}</div>
                        </td>

                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.partners.users.tax', $u->id) }}" class="flex items-center gap-2">
                                @csrf
                                <input type="number" step="0.01" name="tax_percent"
                                       value="{{ (float)$u->partner_tax_percent }}"
                                       class="w-28 rounded-2xl border border-slate-200 px-3 py-2 text-sm font-extrabold text-slate-800 bg-white">
                                <button class="px-3 py-2 rounded-2xl font-extrabold text-white" style="background:#0194F3;">
                                    Set
                                </button>
                            </form>
                            <div class="mt-1 text-xs text-slate-500">Dalam persen (%).</div>
                        </td>

                        <td class="px-4 py-3">
                            @if($u->is_suspended)
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-red-100 text-red-800">SUSPENDED</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-green-100 text-green-800">AKTIF</span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-right space-y-2">
                            <div class="flex justify-end gap-2 flex-wrap">
                                @if($u->is_suspended)
                                    <form method="POST" action="{{ route('admin.partners.users.unsuspend', $u->id) }}">
                                        @csrf
                                        <button class="px-4 py-2 rounded-2xl font-extrabold text-white" style="background:#16a34a;">
                                            Unsuspend
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.partners.users.suspend', $u->id) }}">
                                        @csrf
                                        <button class="px-4 py-2 rounded-2xl font-extrabold text-white" style="background:#dc2626;">
                                            Suspend
                                        </button>
                                    </form>
                                @endif
<form method="POST" action="{{ route('admin.partners.users.destroy', $u->id) }}"
      onsubmit="return confirm('Yakin hapus user partner ini? Aksi ini tidak bisa dibatalkan.');">
    @csrf
    @method('DELETE')
    <button class="px-4 py-2 rounded-2xl font-extrabold text-white" style="background:#111827;">
        Hapus
    </button>
</form>
<a href="{{ route('admin.partners.users.show', $u->id) }}"
   class="px-3 py-2 rounded-2xl border border-slate-200 font-extrabold text-slate-800 hover:bg-slate-50">
   Detail
</a>

                                {{-- mengikuti pola admin existing: edit user pakai menu Users yg sudah ada --}}
                                <a href="{{ route('admin.partners.users.edit', $u->id) }}"
                                   class="px-4 py-2 rounded-2xl border border-slate-200 font-extrabold text-slate-800 hover:bg-slate-50">
                                    Edit Akun
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-6 text-center text-slate-600" colspan="4">
                            Tidak ada data partner.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $users->links() }}</div>
</div>
@endsection
