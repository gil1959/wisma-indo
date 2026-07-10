@extends('partner.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-5">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">Kategori Sewa Kapal</h1>
            <p class="text-sm text-slate-600 mt-1">Kelola kategori untuk paket sewa kapal.</p>
        </div>

        <a href="{{ route('partner.ship-categories.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
           style="background:#0194F3;"
           onmouseover="this.style.background='#0186DB'"
           onmouseout="this.style.background='#0194F3'">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Kategori
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">
            <div class="font-extrabold">Berhasil</div>
            <div class="mt-1">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800 text-sm">
            <div class="font-extrabold">Gagal</div>
            <div class="mt-1">{{ session('error') }}</div>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left font-extrabold text-slate-700">Nama</th>
                        <th class="px-5 py-3 text-left font-extrabold text-slate-700">Slug</th>
                        <th class="px-5 py-3 text-right font-extrabold text-slate-700">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">
                    @forelse($categories as $c)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-5 py-4 font-bold text-slate-900">{{ $c->name }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $c->slug }}</td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('partner.ship-categories.edit', $c->id) }}"
                                   class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold border border-slate-200 hover:bg-slate-50 transition">
                                    <i data-lucide="pencil" class="w-4 h-4" style="color:#0194F3;"></i>
                                    Edit
                                </a>

                                <form action="{{ route('partner.ship-categories.destroy', $c->id) }}"
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
                                            style="background:#ef4444;"
                                            onmouseover="this.style.background='#dc2626'"
                                            onmouseout="this.style.background='#ef4444'">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        Hapus
                                    </button>
                                </form>
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
