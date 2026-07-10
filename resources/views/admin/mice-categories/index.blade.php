@extends('layouts.admin')

@section('title', 'Kategori MICE')
@section('page-title', 'Kategori MICE')

@section('content')
<div class="space-y-5">

    <div class="flex items-start sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Kategori MICE</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola kategori untuk paket MICE.</p>
        </div>

        <a href="{{ route('admin.mice-categories.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
           style="background:#0194F3;"
           onmouseover="this.style.opacity='0.9'"
           onmouseout="this.style.opacity='1'">
            + Tambah Kategori
        </a>
    </div>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
            <div class="font-extrabold">Berhasil</div>
            <div class="text-sm mt-1">{{ session('success') }}</div>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 font-extrabold text-white" style="background:#0194F3;">Daftar Kategori</div>

        <div class="p-5 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                <tr class="text-left text-slate-600">
                    <th class="py-2 pr-4 font-extrabold">#</th>
                    <th class="py-2 pr-4 font-extrabold">Nama</th>
                    <th class="py-2 pr-4 font-extrabold">Slug</th>
                    <th class="py-2 pr-4 font-extrabold">Dibuat</th>
                    <th class="py-2 pr-4 font-extrabold text-right">Aksi</th>
                </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">
                @forelse ($categories as $cat)
                    <tr>
                        <td class="py-3 pr-4 font-bold text-slate-800">{{ $cat->id }}</td>
                        <td class="py-3 pr-4 font-extrabold text-slate-900">{{ $cat->name }}</td>
                        <td class="py-3 pr-4 text-slate-600">{{ $cat->slug }}</td>
                        <td class="py-3 pr-4 text-slate-600">{{ optional($cat->created_at)->format('d M Y') }}</td>
                        <td class="py-3 pr-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.mice-categories.edit', $cat->id) }}"
                                   class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-extrabold border border-slate-200 bg-white hover:bg-slate-50">
                                    Edit
                                </a>

                                <form action="{{ route('admin.mice-categories.destroy', $cat->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus kategori ini? Jika masih dipakai paket, bisa gagal.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-extrabold border border-red-200 bg-red-50 text-red-700 hover:bg-red-100">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-500">
                            Belum ada kategori MICE.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
