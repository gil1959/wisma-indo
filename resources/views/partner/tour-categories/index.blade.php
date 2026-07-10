@extends('partner.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-5">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">Kategori Tour</h1>
            <p class="text-sm text-slate-600 mt-1">Kelola kategori & subkategori untuk paket tour kamu.</p>
        </div>

        <a href="{{ route('partner.tour-categories.create') }}"
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

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-[900px] w-full">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-extrabold text-slate-600">
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Slug</th>
                        <th class="px-5 py-3">Subkategori</th>
                        <th class="px-5 py-3 w-[220px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($categories as $cat)
                        <tr class="text-sm text-slate-700 hover:bg-slate-50/70 transition">
                            <td class="px-5 py-4 font-extrabold text-slate-900">{{ $cat->name }}</td>
                            <td class="px-5 py-4">{{ $cat->slug }}</td>
                            <td class="px-5 py-4">
                                @if($cat->children && $cat->children->count())
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($cat->children as $sub)
                                            <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-extrabold">
                                                {{ $sub->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('partner.tour-categories.edit', $cat) }}"
                                       class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
                                       style="background:#0194F3;"
                                       onmouseover="this.style.background='#0186DB'"
                                       onmouseout="this.style.background='#0194F3'">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('partner.tour-categories.destroy', $cat) }}"
                                          onsubmit="return confirm('Yakin hapus kategori ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
                                                style="background:#ef4444;"
                                                onmouseover="this.style.background='#dc2626'"
                                                onmouseout="this.style.background='#ef4444'">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        @if($cat->children && $cat->children->count())
                            @foreach($cat->children as $sub)
                                <tr class="text-sm text-slate-700 bg-slate-50/40">
                                    <td class="px-5 py-3 pl-10 font-bold text-slate-900">↳ {{ $sub->name }}</td>
                                    <td class="px-5 py-3">{{ $sub->slug }}</td>
                                    <td class="px-5 py-3 text-slate-400">Sub</td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('partner.tour-categories.edit', $sub) }}"
                                               class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
                                               style="background:#0194F3;"
                                               onmouseover="this.style.background='#0186DB'"
                                               onmouseout="this.style.background='#0194F3'">
                                                <i data-lucide="pencil" class="w-4 h-4"></i>
                                                Edit
                                            </a>

                                            <form method="POST" action="{{ route('partner.tour-categories.destroy', $sub) }}"
                                                  onsubmit="return confirm('Yakin hapus subkategori ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
                                                        style="background:#ef4444;"
                                                        onmouseover="this.style.background='#dc2626'"
                                                        onmouseout="this.style.background='#ef4444'">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-slate-500">
                                Belum ada kategori tour.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
