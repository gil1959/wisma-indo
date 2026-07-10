@extends('layouts.admin')

@section('title', 'Dokumentasi')
@section('page-title', 'Dokumentasi')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Dokumentasi</h2>
            <p class="mt-1 text-sm text-slate-600">
                Kelola foto dan video untuk: Paket Tour, Sewa Kapal, dan Umrah.
            </p>
        </div>
    </div>

    {{-- Filter (tetap global buat 3 section) --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-4">
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Tipe</label>
                <select name="type"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                    <option value="">Semua</option>
                    <option value="photo" @selected(request('type')==='photo')>Foto</option>
                    <option value="video" @selected(request('type')==='video')>Video</option>
                </select>
            </div>

            <div class="sm:col-span-3">
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
                    <i data-lucide="filter" class="w-4 h-4" style="color:#0194F3;"></i>
                    Filter
                </button>
            </div>

            @if(request()->filled('type'))
                <div class="sm:col-span-3">
                    <a href="{{ url()->current() }}"
                       class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                       style="background:#ef4444"
                       onmouseover="this.style.background='#dc2626'"
                       onmouseout="this.style.background='#ef4444'">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        Reset
                    </a>
                </div>
            @endif
        </form>
    </div>

    {{-- ===================== SECTION: TOUR ===================== --}}
    <div class="space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="text-sm font-extrabold text-slate-900">Dokumentasi Paket Tour</div>
                <div class="mt-1 text-xs text-slate-500">Data khusus paket tour.</div>
            </div>

            <a href="{{ route('admin.documentations.create', ['category' => 'tour']) }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
               style="background:#0194F3;"
               onmouseover="this.style.background='#0186DB'"
               onmouseout="this.style.background='#0194F3'">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Tour
            </a>
        </div>

        @include('admin.documentations._table', ['items' => $tourItems])

        <div>
            {{ $tourItems->links() }}
        </div>
    </div>

    {{-- ===================== SECTION: SEWA KAPAL ===================== --}}
    <div class="space-y-3 pt-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="text-sm font-extrabold text-slate-900">Dokumentasi Sewa Kapal</div>
                <div class="mt-1 text-xs text-slate-500">Data khusus sewa kapal.</div>
            </div>

            <a href="{{ route('admin.documentations.create', ['category' => 'ship']) }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
               style="background:#0194F3;"
               onmouseover="this.style.background='#0186DB'"
               onmouseout="this.style.background='#0194F3'">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Sewa Kapal
            </a>
        </div>

        @include('admin.documentations._table', ['items' => $shipItems])

        <div>
            {{ $shipItems->links() }}
        </div>
    </div>

    {{-- ===================== SECTION: UMRAH ===================== --}}
    <div class="space-y-3 pt-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="text-sm font-extrabold text-slate-900">Dokumentasi Umrah</div>
                <div class="mt-1 text-xs text-slate-500">Data khusus umrah.</div>
            </div>

            <a href="{{ route('admin.documentations.create', ['category' => 'umrah']) }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
               style="background:#0194F3;"
               onmouseover="this.style.background='#0186DB'"
               onmouseout="this.style.background='#0194F3'">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Umrah
            </a>
        </div>

        @include('admin.documentations._table', ['items' => $umrahItems])

        <div>
            {{ $umrahItems->links() }}
        </div>
    </div>

</div>
@endsection
