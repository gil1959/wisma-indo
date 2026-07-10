@extends('layouts.admin')

@section('title', 'Tambah Dokumentasi')
@section('page-title', 'Tambah Dokumentasi')

@section('content')
@php
    // category diambil dari query string: ?category=tour|ship|umrah
    // default: tour
    $category = request('category', 'tour');
@endphp

<div class="space-y-5" x-data="{ source: '{{ old('source','upload') }}' }">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="text-sm font-extrabold text-slate-900">Tambah Dokumentasi</div>
            <div class="mt-1 text-xs text-slate-500">
                Pilih sumber: upload dari device atau link/embed (Cloudinary / YouTube / Vimeo / direct link).
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.documentations.index') }}"
               class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-xs font-extrabold border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition">
                Kembali
            </a>
        </div>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3">
            <div class="text-sm font-extrabold text-rose-800">Validasi gagal</div>
            <ul class="mt-2 list-disc pl-5 text-xs text-rose-700 space-y-1">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Card --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <div class="text-sm font-extrabold text-slate-900">Form</div>
            <div class="mt-1 text-xs text-slate-500">Upload bisa multiple. Link bisa banyak (1 baris = 1 item).</div>
        </div>

        <form method="POST" action="{{ route('admin.documentations.store') }}" enctype="multipart/form-data" class="p-5">
            @csrf

            {{-- IMPORTANT: kunci category sesuai section yang dipilih --}}
            <input type="hidden" name="category" value="{{ $category }}">

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">

                {{-- Type --}}
                <div class="sm:col-span-4">
                    <label class="block text-[11px] font-extrabold text-slate-700 mb-2">Tipe <span class="text-rose-600">*</span></label>
                    <select name="type"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-200"
                            required>
                        <option value="photo" {{ old('type')==='photo' ? 'selected' : '' }}>Photo</option>
                        <option value="video" {{ old('type')==='video' ? 'selected' : '' }}>Video</option>
                    </select>
                </div>

                {{-- Sort order --}}
                <div class="sm:col-span-4">
                    <label class="block text-[11px] font-extrabold text-slate-700 mb-2">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                           class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-200"
                           min="0">
                </div>

                {{-- Active --}}
                <div class="sm:col-span-4">
                    <label class="block text-[11px] font-extrabold text-slate-700 mb-2">Status</label>
                    <label class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                        <span class="font-extrabold">Aktif</span>
                    </label>
                </div>

                {{-- Title --}}
                <div class="sm:col-span-12">
                    <label class="block text-[11px] font-extrabold text-slate-700 mb-2">Judul (opsional)</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-200"
                           placeholder="Misal: Dokumentasi Trip Bromo">
                </div>

                {{-- Source tabs --}}
                <div class="sm:col-span-12">
                    <label class="block text-[11px] font-extrabold text-slate-700 mb-2">Sumber <span class="text-rose-600">*</span></label>

                    <div class="inline-flex w-full rounded-2xl border border-slate-200 bg-white p-1 shadow-sm">
                        <button type="button" @click="source='upload'"
                                class="flex-1 text-center px-4 py-2 rounded-xl text-xs font-extrabold transition"
                                :class="source==='upload' ? 'bg-[#0194F3] text-white' : 'text-slate-700 hover:bg-slate-50'">
                            Upload dari Device
                        </button>
                        <button type="button" @click="source='link'"
                                class="flex-1 text-center px-4 py-2 rounded-xl text-xs font-extrabold transition"
                                :class="source==='link' ? 'bg-[#0194F3] text-white' : 'text-slate-700 hover:bg-slate-50'">
                            Link / Embed
                        </button>
                    </div>

                    <input type="hidden" name="source" :value="source">
                </div>

                {{-- Upload block --}}
                <div class="sm:col-span-12" x-show="source==='upload'" x-cloak>
                    <label class="block text-[11px] font-extrabold text-slate-700 mb-2">Upload File <span class="text-rose-600">*</span></label>
                    <input type="file"
                           name="files[]"
                           multiple
                           class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800"
                           accept="image/jpeg,image/png,image/webp,video/mp4,video/webm,video/ogg">
                    <div class="mt-2 text-xs text-slate-500">
                        Upload multiple. Foto: jpg/png/webp. Video: mp4/webm/ogg. (Validasi detail tetap di controller)
                    </div>
                </div>

                {{-- Link block --}}
                <div class="sm:col-span-12" x-show="source==='link'" x-cloak>
                    <label class="block text-[11px] font-extrabold text-slate-700 mb-2">Link / Embed <span class="text-rose-600">*</span></label>
                    <textarea name="embed_links" rows="5"
                              class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                              placeholder="1 baris = 1 item. Bisa paste URL atau iframe embed (akan diambil src).">{{ old('embed_links') }}</textarea>

                    <div class="mt-2 text-xs text-slate-500 space-y-1">
                        <div>Contoh URL: https://res.cloudinary.com/.../video/upload/...mp4</div>
                        <div>Contoh iframe: &lt;iframe src="https://.../embed/..."&gt;&lt;/iframe&gt;</div>
                    </div>
                </div>

            </div>

            {{-- Actions --}}
            <div class="mt-5 flex flex-col sm:flex-row gap-2">
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-2xl px-4 py-3 text-xs font-extrabold text-white bg-[#0194F3] hover:brightness-95 transition">
                    Simpan
                </button>

                <a href="{{ route('admin.documentations.index') }}"
                   class="inline-flex items-center justify-center rounded-2xl px-4 py-3 text-xs font-extrabold border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
