@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Users')

@section('content')
<div class="space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Users</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola akun user (search, lihat detail, edit, hapus)</p>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" class="w-full sm:w-auto">
            <div class="flex items-center gap-2">
                <div class="relative flex-1 sm:w-80">
                    <input type="text"
                           name="q"
                           value="{{ $q }}"
                           placeholder="Cari nama / email / no hp..."
                           class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 focus:ring-0 focus:outline-none">
                </div>
                <button class="px-4 py-2.5 rounded-2xl font-extrabold text-white"
                        style="background:#0194F3;">
                    Cari
                </button>
            </div>
        </form>
        <a href="{{ route('admin.users.create') }}"
   class="px-4 py-2.5 rounded-2xl font-extrabold text-white"
   style="background:#0194F3;">
   Tambah User
</a>

    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 font-bold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800 font-bold">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-extrabold text-slate-600 uppercase">
                        <th class="px-5 py-3">User</th>
                        <th class="px-5 py-3">No HP</th>
                        <th class="px-5 py-3">Verified</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="text-sm text-slate-700 hover:bg-slate-50/70 transition">
                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-900">{{ $u->name }}</div>
                                <div class="text-xs text-slate-500">{{ $u->email }}</div>
                            </td>
                            <td class="px-5 py-4">
                                {{ $u->phone ?? '-' }}
                            </td>
                            <td class="px-5 py-4">
                                @if($u->email_verified_at)
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                                          style="background: rgba(16,185,129,0.10); border-color: rgba(16,185,129,0.25); color:#059669;">
                                        Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                                          style="background: rgba(244,63,94,0.08); border-color: rgba(244,63,94,0.25); color:#e11d48;">
                                        Not Verified
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.show', $u) }}"
                                       class="px-3 py-2 rounded-xl font-extrabold text-slate-700 border border-slate-200 hover:bg-slate-50">
                                        Detail
                                    </a>

                                    <a href="{{ route('admin.users.edit', $u) }}"
                                       class="px-3 py-2 rounded-xl font-extrabold text-white"
                                       style="background:#0194F3;">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.users.destroy', $u) }}"
                                          method="POST"
                                          onsubmit="return confirm('Hapus user ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-2 rounded-xl font-extrabold text-white"
                                                style="background:#ef4444;">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-sm text-slate-500">
                                Tidak ada user.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-slate-200">
            {{ $users->links() }}
        </div>
    </div>

</div>
@endsection
