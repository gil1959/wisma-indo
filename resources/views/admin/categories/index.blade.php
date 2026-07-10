@extends('layouts.admin')

@section('title', 'Kategori Tour')
@section('page-title', 'Kategori Tour')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-start sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Kategori Tour</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola kategori untuk paket tour.</p>
        </div>

        <a href="{{ route('admin.categories.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
           style="background:#0194F3;"
           onmouseover="this.style.background='#0186DB'"
           onmouseout="this.style.background='#0194F3'">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Kategori
        </a>
    </div>

    {{-- Flash message --}}
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
            <div class="font-extrabold">Berhasil</div>
            <div class="text-sm mt-1">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800">
            <div class="font-extrabold">Gagal</div>
            <div class="text-sm mt-1">{{ session('error') }}</div>
        </div>
    @endif

    {{-- Table --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-[720px] w-full text-left">
                <thead class="bg-slate-50">
                <tr class="text-xs font-extrabold text-slate-600">
                    <th class="px-5 py-3">Nama</th>
                    <th class="px-5 py-3">Slug</th>
                    <th class="px-5 py-3">Tipe</th>
                    <th class="px-5 py-3 text-right w-[190px]">Aksi</th>
                </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                @forelse($categories as $parent)
                    {{-- Parent row --}}
                    <tr class="text-sm text-slate-700 hover:bg-slate-50/70 transition">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div class="font-extrabold text-slate-900">{{ $parent->name }}</div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-mono text-slate-600">{{ $parent->slug }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center rounded-lg bg-emerald-50 px-2 py-0.5 text-[11px] font-extrabold text-emerald-700">
                                PARENT
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.categories.edit', $parent) }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
                                    <i data-lucide="pencil" class="w-4 h-4" style="color:#0194F3;"></i>
                                    Edit
                                </a>

                                <form action="{{ route('admin.categories.destroy', $parent) }}"
                                      method="POST" class="inline"
                                      onsubmit="return confirm('Hapus kategori ini?');">
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

                    {{-- Children rows --}}
                    @foreach($parent->children as $child)
                        <tr class="text-sm text-slate-700 bg-slate-50/50 hover:bg-slate-50 transition">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2 pl-6">
                                    <span class="inline-flex items-center rounded-lg bg-slate-200 px-2 py-0.5 text-[11px] font-extrabold text-slate-700">
                                        SUB
                                    </span>

                                    <div class="font-bold text-slate-900">{{ $child->name }}</div>

                                    <span class="text-xs text-slate-500">
                                        (Parent: <span class="font-semibold text-slate-600">{{ $parent->name }}</span>)
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="font-mono text-slate-600">{{ $child->slug }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-lg bg-slate-200 px-2 py-0.5 text-[11px] font-extrabold text-slate-700">
                                    SUB
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.categories.edit', $child) }}"
                                       class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
                                        <i data-lucide="pencil" class="w-4 h-4" style="color:#0194F3;"></i>
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.categories.destroy', $child) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Hapus sub kategori ini?');">
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
                    @endforeach

                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center">
                            <div class="mx-auto h-12 w-12 rounded-2xl border grid place-items-center"
                                 style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22);">
                                <i data-lucide="tags" class="w-6 h-6" style="color:#0194F3;"></i>
                            </div>
                            <div class="mt-3 font-extrabold text-slate-900">Belum ada kategori</div>
                            <div class="mt-1 text-sm text-slate-600">Buat kategori dulu biar paket tour bisa dikelompokkan.</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>
    </div>

    {{-- Pagination kalau paginate --}}
    @if(method_exists($categories, 'links'))
        <div>
            {{ $categories->links() }}
        </div>
    @endif

</div>
@endsection
