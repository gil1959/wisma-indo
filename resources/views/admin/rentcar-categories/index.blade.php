@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-5">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">Kategori Rental</h1>
            <p class="text-sm text-slate-600 mt-1">Kelola kategori untuk paket rent car.</p>
        </div>

        <a href="{{ route('admin.rent-car-categories.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
           style="background:#0194F3;"
           onmouseover="this.style.background='#0186DB'"
           onmouseout="this.style.background='#0194F3'">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Kategori
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800 text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <p class="text-sm font-extrabold text-slate-800">
                    Daftar Kategori ({{ $categories->count() }})
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-left px-5 py-3 font-extrabold">Nama</th>
                        <th class="text-left px-5 py-3 font-extrabold">Slug</th>
                        <th class="text-right px-5 py-3 font-extrabold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($categories as $c)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-5 py-3">
                                <div class="font-extrabold text-slate-900">{{ $c->name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">ID: {{ $c->id }}</div>
                            </td>
                            <td class="px-5 py-3">
                                <code class="text-xs rounded-lg bg-slate-100 px-2 py-1 text-slate-700">{{ $c->slug }}</code>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.rent-car-categories.edit', $c) }}"
                                       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-extrabold text-slate-800 hover:bg-slate-50">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.rent-car-categories.destroy', $c) }}"
                                          method="POST"
                                          onsubmit="return confirm('Hapus kategori ini? Pastikan tidak dipakai paket rental.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-extrabold text-rose-700 hover:bg-rose-100">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-10 text-center text-slate-500">
                                Belum ada kategori. Klik <span class="font-extrabold">Tambah Kategori</span>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
