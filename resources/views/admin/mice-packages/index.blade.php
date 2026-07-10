@extends('layouts.admin')

@section('title', 'Paket MICE')
@section('page-title', 'Paket MICE')

@section('content')
<div class="space-y-5">


    <div class="flex items-start sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Paket MICE</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola paket Meetings, Incentives, Conferences, and Exhibitions.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.mice-categories.index') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50">
                Kategori MICE
            </a>

            <a href="{{ route('admin.mice-packages.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
               style="background:#0194F3;"
               onmouseover="this.style.opacity='0.9'"
               onmouseout="this.style.opacity='1'">
                + Tambah Paket
            </a>
        </div>
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

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
            <div class="font-extrabold">Berhasil</div>
            <div class="text-sm mt-1">{{ session('success') }}</div>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 font-extrabold text-white" style="background:#0194F3;">Daftar Paket</div>

        <div class="p-5 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                <tr class="text-left text-slate-600">
                    <th class="py-2 pr-4 font-extrabold">#</th>
                    <th class="py-2 pr-4 font-extrabold">Judul</th>
                    <th class="py-2 pr-4 font-extrabold">Kategori</th>
                    <th class="py-2 pr-4 font-extrabold">Status</th>
                    <th class="py-2 pr-4 font-extrabold">Dibuat</th>
                    <th class="py-2 pr-4 font-extrabold text-right">Aksi</th>
                </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">
                @forelse ($packages as $pkg)
                    <tr>
                        <td class="py-3 pr-4 font-bold text-slate-800">{{ $pkg->id }}</td>
                        <td class="py-3 pr-4">
                            <div class="font-extrabold text-slate-900">{{ $pkg->title }}</div>
                            <div class="text-xs text-slate-500 mt-1">{{ $pkg->slug }}</div>
                        </td>
                        <td class="py-3 pr-4 text-slate-700 font-semibold">{{ $pkg->category?->name ?? '-' }}</td>
                        <td class="py-3 pr-4">
                            @if ($pkg->is_active)
                                <span class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 text-xs font-extrabold">Aktif</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-slate-100 text-slate-700 border border-slate-200 px-3 py-1 text-xs font-extrabold">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4 text-slate-600">{{ optional($pkg->created_at)->format('d M Y') }}</td>
                        <td class="py-3 pr-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.mice-packages.edit', $pkg->id) }}"
                                   class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-extrabold border border-slate-200 bg-white hover:bg-slate-50">
                                    Edit
                                </a>
                                <form action="{{ route('admin.mice-packages.destroy', $pkg->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus paket ini?');">
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
                        <td colspan="6" class="py-8 text-center text-slate-500">Belum ada paket MICE.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="mt-5">
                {{ $packages->links() }}
            </div>
        </div>
    </div>

</div>
@endsection
