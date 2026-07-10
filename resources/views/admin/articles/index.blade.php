@extends('layouts.admin')

@section('title', 'Artikel')
@section('page-title', 'Artikel')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Artikel</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola konten artikel website</p>
        </div>

        <a href="{{ route('admin.articles.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
           style="background:#0194F3;"
           onmouseover="this.style.background='#0186DB'"
           onmouseout="this.style.background='#0194F3'">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Artikel
        </a>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-[820px] w-full text-left">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-extrabold text-slate-600">
                        <th class="px-5 py-3">Judul</th>
                        <th class="px-5 py-3 text-center w-[160px]">Status</th>
                        <th class="px-5 py-3 text-center w-[160px]">Tanggal</th>
                        <th class="px-5 py-3 text-right w-[220px]">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $item)
                        <tr class="text-sm text-slate-700 hover:bg-slate-50/70 transition">
                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-900">{{ $item->title }}</div>
                                <div class="text-xs text-slate-500">
                                    Slug: <span class="font-semibold">{{ $item->slug }}</span>
                                </div>
                            </td>

                            <td class="px-5 py-4 text-center">
                                @if($item->is_published)
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                                          style="background: rgba(16,185,129,0.10); border-color: rgba(16,185,129,0.25); color:#065f46;">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                                        Publish
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                                          style="background: rgba(148,163,184,0.15); border-color: rgba(148,163,184,0.35); color:#334155;">
                                        <i data-lucide="file-text" class="w-4 h-4"></i>
                                        Draft
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-center text-slate-600 font-semibold">
                                {{ $item->created_at->format('d M Y') }}
                            </td>

                            <td class="px-5 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.articles.edit', $item) }}"
                                       class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
                                        <i data-lucide="pencil" class="w-4 h-4" style="color:#0194F3;"></i>
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.articles.destroy', $item) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin hapus artikel ini?')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
                                                style="background:#ef4444"
                                                onmouseover="this.style.background='#dc2626'"
                                                onmouseout="this.style.background='#ef4444'">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-500">
                                Belum ada artikel.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div>
        {{ $items->links() }}
    </div>

</div>
@endsection
