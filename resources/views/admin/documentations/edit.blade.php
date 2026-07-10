@extends('layouts.admin')

@section('title', 'Edit Dokumentasi')
@section('page-title', 'Edit Dokumentasi')

@section('content')
@php
    $isExternal = $documentation->is_external;
@endphp

<div class="space-y-5" x-data="{ source: '{{ old('source', $isExternal ? 'link' : 'upload') }}' }">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="text-sm font-extrabold text-slate-900">Edit Dokumentasi</div>
            <div class="mt-1 text-xs text-slate-500">
                Kamu bisa ganti sumber ke Upload atau Link. File lama akan dihapus kalau sebelumnya local.
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <div class="text-sm font-extrabold text-slate-900">Form Edit</div>
                        <div class="mt-1 text-xs text-slate-500">ID: <span class="font-extrabold text-slate-800">{{ $documentation->id }}</span></div>
                    </div>

                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-extrabold
                        {{ $isExternal ? 'bg-slate-100 text-slate-800 border-slate-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200' }}">
                        {{ $isExternal ? 'LINK' : 'UPLOAD' }}
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.documentations.update', $documentation->id) }}"
                      enctype="multipart/form-data" class="p-5 space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- IMPORTANT: kunci category supaya item ga nyasar pindah kategori --}}
                    <input type="hidden" name="category" value="{{ $documentation->category ?? 'tour' }}">

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">

                        {{-- Type (read-only biar aman, kalau mau editable bilang) --}}
                        <div class="sm:col-span-4">
                            <label class="block text-[11px] font-extrabold text-slate-700 mb-2">Tipe</label>
                            <input type="text" value="{{ $documentation->type }}" disabled
                                   class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                        </div>

                        {{-- Sort --}}
                        <div class="sm:col-span-4">
                            <label class="block text-[11px] font-extrabold text-slate-700 mb-2">Sort Order</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', $documentation->sort_order) }}"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                   min="0">
                        </div>

                        {{-- Active --}}
                        <div class="sm:col-span-4">
                            <label class="block text-[11px] font-extrabold text-slate-700 mb-2">Status</label>
                            <label class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $documentation->is_active) ? 'checked' : '' }}>
                                <span class="font-extrabold">Aktif</span>
                            </label>
                        </div>

                        {{-- Title --}}
                        <div class="sm:col-span-12">
                            <label class="block text-[11px] font-extrabold text-slate-700 mb-2">Judul (opsional)</label>
                            <input type="text" name="title" value="{{ old('title', $documentation->title) }}"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-200">
                        </div>

                        {{-- Source tabs --}}
                        <div class="sm:col-span-12">
                            <label class="block text-[11px] font-extrabold text-slate-700 mb-2">Sumber</label>

                            <div class="inline-flex w-full rounded-2xl border border-slate-200 bg-white p-1 shadow-sm">
                                <button type="button" @click="source='upload'"
                                        class="flex-1 text-center px-4 py-2 rounded-xl text-xs font-extrabold transition"
                                        :class="source==='upload' ? 'bg-[#0194F3] text-white' : 'text-slate-700 hover:bg-slate-50'">
                                    Upload
                                </button>
                                <button type="button" @click="source='link'"
                                        class="flex-1 text-center px-4 py-2 rounded-xl text-xs font-extrabold transition"
                                        :class="source==='link' ? 'bg-[#0194F3] text-white' : 'text-slate-700 hover:bg-slate-50'">
                                    Link / Embed
                                </button>
                            </div>

                            <input type="hidden" name="source" :value="source">
                        </div>

                        {{-- Replace file --}}
                        <div class="sm:col-span-12" x-show="source==='upload'" x-cloak>
                            <label class="block text-[11px] font-extrabold text-slate-700 mb-2">Ganti File (opsional)</label>
                            <input type="file"
                                   name="replace_file"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800"
                                   accept="image/jpeg,image/png,image/webp,video/mp4,video/webm,video/ogg">
                            <div class="mt-2 text-xs text-slate-500">
                                Kalau item sekarang link, upload baru akan menggantikan link. Kalau item sekarang file local, file lama dihapus.
                            </div>
                        </div>

                        {{-- Replace link --}}
                        <div class="sm:col-span-12" x-show="source==='link'" x-cloak>
                            <label class="block text-[11px] font-extrabold text-slate-700 mb-2">Ganti Link (opsional)</label>
                            <input type="text"
                                   name="embed_link"
                                   value="{{ old('embed_link', $isExternal ? $documentation->file_path : '') }}"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200"
                                   placeholder="https://... atau paste iframe">
                            <div class="mt-2 text-xs text-slate-500">
                                Kalau paste iframe, sistem akan ambil src="...".
                            </div>
                        </div>

                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-2 pt-1">
                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-2xl px-4 py-3 text-xs font-extrabold text-white bg-[#0194F3] hover:brightness-95 transition">
                            Simpan Perubahan
                        </button>

                        <a href="{{ route('admin.documentations.index') }}"
                           class="inline-flex items-center justify-center rounded-2xl px-4 py-3 text-xs font-extrabold border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition">
                            Batal
                        </a>
                    </div>

                </form>
            </div>
        </div>

        {{-- Side: Preview --}}
        <div class="lg:col-span-1 space-y-5">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200">
                    <div class="text-sm font-extrabold text-slate-900">Preview</div>
                    <div class="mt-1 text-xs text-slate-500">Preview berdasarkan data sekarang (sebelum disimpan).</div>
                </div>

                <div class="p-5 space-y-3">
                    @if($documentation->type === 'photo')
                        @if($documentation->is_external)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-700 break-words">
                                {{ $documentation->file_path }}
                            </div>
                            <a href="{{ $documentation->url }}" target="_blank"
                               class="w-full inline-flex items-center justify-center rounded-2xl px-4 py-3 text-xs font-extrabold border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition">
                                Buka Link
                            </a>
                        @else
                            <img src="{{ $documentation->url }}" class="w-full rounded-2xl border border-slate-200 object-cover" alt="photo">
                        @endif
                    @else
                        {{-- video --}}
                        @if($documentation->is_external)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-700 break-words">
                                {{ $documentation->file_path }}
                            </div>
                            <a href="{{ $documentation->url }}" target="_blank"
                               class="w-full inline-flex items-center justify-center rounded-2xl px-4 py-3 text-xs font-extrabold border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 transition">
                                Buka Link
                            </a>
                        @else
                            <video controls preload="metadata" class="w-full rounded-2xl border border-slate-200">
                                <source src="{{ $documentation->url }}" type="video/mp4">
                            </video>
                        @endif
                    @endif

                    <div class="text-xs text-slate-500">
                        Created: {{ optional($documentation->created_at)->format('d M Y H:i') }}
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200">
                    <div class="text-sm font-extrabold text-slate-900">Danger Zone</div>
                </div>
                <div class="p-5">
                    <form method="POST" action="{{ route('admin.documentations.destroy', $documentation->id) }}"
                          onsubmit="return confirm('Hapus dokumentasi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center rounded-2xl px-4 py-3 text-xs font-extrabold text-white bg-rose-600 hover:bg-rose-700 transition">
                            Hapus Dokumentasi
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
