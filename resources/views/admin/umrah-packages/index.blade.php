@extends('layouts.admin')

@section('title', 'Paket Umrah')
@section('page-title', 'Paket Umrah')

@section('content')
<div class="space-y-5">

    <div class="flex items-start sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Daftar Paket Umrah</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola paket umrah yang tampil di website.</p>
        </div>

        <a href="{{ route('admin.umrah-packages.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
           style="background:#0194F3;"
           onmouseover="this.style.background='#0186DB'"
           onmouseout="this.style.background='#0194F3'">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Paket
        </a>
    </div>
{{-- Filter --}}
<form method="GET" action="{{ url()->current() }}"
      class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4">
    <div class="grid gap-3 md:grid-cols-12 items-end">

        <div class="md:col-span-5">
            <label class="block text-sm font-extrabold text-slate-700 mb-2">Pencarian</label>
            <input type="text"
                   name="q"
                   value="{{ request('q') }}"
                   placeholder="Cari judul / slug..."
                   class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
        </div>

        <div class="md:col-span-3">
            <label class="block text-sm font-extrabold text-slate-700 mb-2">Kategori</label>
            <select name="category" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
                <option value="">Semua</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-extrabold text-slate-700 mb-2">Status</label>
            <select name="status" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
                <option value="">Semua</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
            </select>
        </div>

        <div class="md:col-span-2 flex gap-2">
            <button type="submit" class="w-full rounded-xl px-4 py-2.5 text-sm font-extrabold text-white"
                    style="background:#0194F3;"
                    onmouseover="this.style.background='#0186DB'"
                    onmouseout="this.style.background='#0194F3'">
                Terapkan
            </button>

            <a href="{{ url()->current() }}"
               class="w-full rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-center">
                Reset
            </a>
        </div>

    </div>
</form>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-[980px] w-full text-left">
                <thead class="bg-slate-50">
                <tr class="text-xs font-extrabold text-slate-600">
                    <th class="px-5 py-3">Judul</th>
                    <th class="px-5 py-3">Kategori</th>
                    <th class="px-5 py-3">Durasi</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right w-[190px]">Aksi</th>
                </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                @forelse($packages as $p)
                    <tr class="text-sm text-slate-700 hover:bg-slate-50/70 transition">
                        <td class="px-5 py-4">
                            <div class="font-extrabold text-slate-900">{{ $p->title }}</div>
                            <div class="text-xs text-slate-500 mt-1">
                                Slug: <span class="font-mono">{{ $p->slug ?? '-' }}</span>
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <span class="font-bold text-slate-900">
                                {{ $p->category?->name ?? '-' }}
                            </span>
                        </td>

                        <td class="px-5 py-4">
                            {{ $p->duration_text ?? '-' }}
                        </td>

                        <td class="px-5 py-4">
                            @if($p->is_active)
                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border border-emerald-200 bg-emerald-50 text-emerald-800">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border border-red-200 bg-red-50 text-red-800">
                                    <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.umrah-packages.edit', $p->id) }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
                                    <i data-lucide="pencil" class="w-4 h-4" style="color:#0194F3;"></i>
                                    Edit
                                </a>

                                <form action="{{ route('admin.umrah-packages.destroy', $p->id) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Yakin hapus paket ini?');">
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
                        <td colspan="5" class="px-5 py-12 text-center">
                            <div class="mx-auto h-12 w-12 rounded-2xl border grid place-items-center"
                                 style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22);">
                                <i data-lucide="landmark" class="w-6 h-6" style="color:#0194F3;"></i>
                            </div>
                            <div class="mt-3 font-extrabold text-slate-900">Belum ada paket umrah</div>
                            <div class="mt-1 text-sm text-slate-600">Klik “Tambah Paket” untuk membuat paket baru.</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($packages, 'links'))
        <div>
            {{ $packages->links() }}
        </div>
    @endif

</div>
@endsection
