@extends('layouts.admin')
@section('title', 'Halaman Document')

@section('content')
@php
// Downloads JSON
$raw = old('travel_docs_downloads', $settings['travel_docs_downloads'] ?? '[]');
$arr = json_decode($raw, true);
$arr = is_array($arr) ? $arr : [];
$rows = max(5, count($arr));
@endphp

<div class="max-w-6xl mx-auto px-4 py-6">

    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">Halaman Document</h1>
            <p class="text-sm text-slate-600 mt-1">
                Kelola konten page <code>/Document</code> (Paspor, Visa, Harga, Info Imigrasi, Pemesanan, Download).
            </p>
        </div>

        <a href="{{ route('travel-documents') }}" target="_blank"
            class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            Preview Page
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900">
        <div class="font-semibold">Berhasil</div>
        <div class="text-sm text-emerald-800">{{ session('success') }}</div>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-900">
        <div class="font-semibold">Validasi gagal</div>
        <ul class="mt-2 list-disc pl-5 text-sm text-rose-800">
            @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.travel-documents.update') }}" class="space-y-6">
        @csrf

        {{-- SEO (BIARKAN DI LUAR TAB) --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-4 font-extrabold text-white" style="background:var(--brand);">SEO</div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-bold text-slate-700">Meta Title</label>
                    <input type="text" name="travel_docs_meta_title"
                        value="{{ old('travel_docs_meta_title', $settings['travel_docs_meta_title'] ?? '') }}"
                        class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200" />
                </div>
                <div>
                    <label class="text-sm font-bold text-slate-700">Meta Description</label>
                    <input type="text" name="travel_docs_meta_desc"
                        value="{{ old('travel_docs_meta_desc', $settings['travel_docs_meta_desc'] ?? '') }}"
                        class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200" />
                </div>
            </div>
        </div>

        {{-- HERO (BIARKAN DI LUAR TAB) --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-4 font-extrabold text-white" style="background:var(--brand);">Hero</div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-bold text-slate-700">Badge</label>
                    <input type="text" name="travel_docs_hero_badge"
                        value="{{ old('travel_docs_hero_badge', $settings['travel_docs_hero_badge'] ?? '') }}"
                        class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200" />
                </div>
                <div>
                    <label class="text-sm font-bold text-slate-700">Title</label>
                    <input type="text" name="travel_docs_hero_title"
                        value="{{ old('travel_docs_hero_title', $settings['travel_docs_hero_title'] ?? '') }}"
                        class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200" />
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-bold text-slate-700">Description</label>
                    <input type="text" name="travel_docs_hero_desc"
                        value="{{ old('travel_docs_hero_desc', $settings['travel_docs_hero_desc'] ?? '') }}"
                        class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200" />
                </div>
            </div>
        </div>

        {{-- MAIN: TAB LAYOUT (RAPI, NGGAK PADET) --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden"
            x-data="{ tab: 'passport' }">

            <div class="px-5 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3"
                style="background: rgba(1,148,243,.08);">
                <div>
                    <div class="text-base font-extrabold text-slate-800">Konten & Section</div>
                    <div class="text-xs text-slate-600">Pilih tab untuk mengedit konten tiap section. Lebih rapi, nggak numpuk.</div>
                </div>

                {{-- Pengaturan label tab (kecil, tapi penting) --}}
                <div class="w-full md:w-[420px] rounded-2xl border border-slate-200 bg-white p-3">
                    <div class="text-xs font-extrabold text-slate-700 mb-2">Label Tab (di halaman depan)</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs font-bold text-slate-600">Paspor</label>
                            <input type="text" name="travel_docs_tab_passport_title"
                                value="{{ old('travel_docs_tab_passport_title', $settings['travel_docs_tab_passport_title'] ?? '') }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-600">Visa</label>
                            <input type="text" name="travel_docs_tab_visa_title"
                                value="{{ old('travel_docs_tab_visa_title', $settings['travel_docs_tab_visa_title'] ?? '') }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabs bar (mirip foto) --}}
            <div class="px-5 pt-5">
                <div class="w-full overflow-x-auto">
                    <div class="inline-flex rounded-2xl overflow-hidden border"
                        style="border-color: rgba(1,148,243,.20); background: rgba(1,148,243,.08);">

                        @php
                        $tabs = [
                        ['key' => 'passport', 'label' => 'Paspor'],
                        ['key' => 'visa', 'label' => 'Visa'],
                        ['key' => 'passport_price', 'label' => 'Harga Paspor'],
                        ['key' => 'visa_price', 'label' => 'Harga Visa'],
                        ['key' => 'immigration', 'label' => 'Info Imigrasi'],
                        ['key' => 'order', 'label' => 'Pemesanan'],
                        ['key' => 'download', 'label' => 'Download'],
                        ];
                        @endphp

                        @foreach($tabs as $t)
                        <button type="button"
                            @click="tab='{{ $t['key'] }}'"
                            class="px-5 py-3 text-sm font-extrabold whitespace-nowrap border-r last:border-r-0 transition"
                            :class="tab==='{{ $t['key'] }}' ? 'bg-white text-slate-800' : 'text-white'"
                            :style="tab==='{{ $t['key'] }}' ? 'background:#fff;' : 'background:var(--brand);'"
                            style="border-color: rgba(255,255,255,.25);">
                            {{ $t['label'] }}
                        </button>
                        @endforeach

                    </div>
                </div>
            </div>

            {{-- Panels --}}
            <div class="px-5 py-5">

                {{-- PASPOR --}}
                <div x-show="tab==='passport'" x-cloak class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-4 py-3 font-extrabold text-white" style="background:var(--brand);">
                        Konten Paspor
                    </div>
                    <div class="p-4">
                        <div class="text-xs text-slate-600 mb-3">Isi konten untuk tab Paspor (HTML).</div>
                        <textarea name="travel_docs_passport_html"
                            class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                            rows="12">{!! old('travel_docs_passport_html', $settings['travel_docs_passport_html'] ?? '') !!}</textarea>
                    </div>
                </div>

                {{-- VISA --}}
                <div x-show="tab==='visa'" x-cloak class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-4 py-3 font-extrabold text-white" style="background:var(--brand);">
                        Konten Visa
                    </div>
                    <div class="p-4">
                        <div class="text-xs text-slate-600 mb-3">Isi konten untuk tab Visa (HTML).</div>
                        <textarea name="travel_docs_visa_html"
                            class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                            rows="12">{!! old('travel_docs_visa_html', $settings['travel_docs_visa_html'] ?? '') !!}</textarea>
                    </div>
                </div>

                {{-- HARGA PASPOR --}}
                <div x-show="tab==='passport_price'" x-cloak class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-4 py-3 font-extrabold text-white" style="background:var(--brand);">
                        Harga Paspor
                    </div>
                    <div class="p-4 space-y-3">
                        <div>
                            <label class="text-sm font-bold text-slate-700">Judul</label>
                            <input type="text" name="travel_docs_passport_price_title"
                                value="{{ old('travel_docs_passport_price_title', $settings['travel_docs_passport_price_title'] ?? '') }}"
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200" />
                        </div>
                        <div>
                            <label class="text-sm font-bold text-slate-700">Konten</label>
                            <textarea name="travel_docs_passport_price_html"
                                class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm mt-2"
                                rows="10">{!! old('travel_docs_passport_price_html', $settings['travel_docs_passport_price_html'] ?? '') !!}</textarea>
                        </div>
                    </div>
                </div>

                {{-- HARGA VISA --}}
                <div x-show="tab==='visa_price'" x-cloak class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-4 py-3 font-extrabold text-white" style="background:var(--brand);">
                        Harga Visa
                    </div>
                    <div class="p-4 space-y-3">
                        <div>
                            <label class="text-sm font-bold text-slate-700">Judul</label>
                            <input type="text" name="travel_docs_visa_price_title"
                                value="{{ old('travel_docs_visa_price_title', $settings['travel_docs_visa_price_title'] ?? '') }}"
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200" />
                        </div>
                        <div>
                            <label class="text-sm font-bold text-slate-700">Konten</label>
                            <textarea name="travel_docs_visa_price_html"
                                class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm mt-2"
                                rows="10">{!! old('travel_docs_visa_price_html', $settings['travel_docs_visa_price_html'] ?? '') !!}</textarea>
                        </div>
                    </div>
                </div>

                {{-- INFO IMIGRASI --}}
                <div x-show="tab==='immigration'" x-cloak class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-4 py-3 font-extrabold text-white" style="background:var(--brand);">
                        Info Imigrasi
                    </div>
                    <div class="p-4 space-y-3">
                        <div>
                            <label class="text-sm font-bold text-slate-700">Judul</label>
                            <input type="text" name="travel_docs_immigration_title"
                                value="{{ old('travel_docs_immigration_title', $settings['travel_docs_immigration_title'] ?? '') }}"
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200" />
                        </div>
                        <div>
                            <label class="text-sm font-bold text-slate-700">Konten</label>
                            <textarea name="travel_docs_immigration_html"
                                class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm mt-2"
                                rows="10">{!! old('travel_docs_immigration_html', $settings['travel_docs_immigration_html'] ?? '') !!}</textarea>
                        </div>
                    </div>
                </div>

                {{-- PEMESANAN --}}
                <div x-show="tab==='order'" x-cloak class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-4 py-3 font-extrabold text-white" style="background:var(--brand);">
                        Pemesanan
                    </div>
                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-bold text-slate-700">Judul</label>
                            <input type="text" name="travel_docs_order_title"
                                value="{{ old('travel_docs_order_title', $settings['travel_docs_order_title'] ?? '') }}"
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200" />
                        </div>
                        <div>
                            <label class="text-sm font-bold text-slate-700">WhatsApp (contoh: 62812xxxx)</label>
                            <input type="text" name="travel_docs_order_whatsapp"
                                value="{{ old('travel_docs_order_whatsapp', $settings['travel_docs_order_whatsapp'] ?? '') }}"
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm font-bold text-slate-700">Konten</label>
                            <textarea name="travel_docs_order_html"
                                class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm mt-2"
                                rows="12">{!! old('travel_docs_order_html', $settings['travel_docs_order_html'] ?? '') !!}</textarea>
                        </div>
                    </div>
                </div>

                {{-- DOWNLOAD --}}
                <div x-show="tab==='download'" x-cloak class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-4 py-3 font-extrabold text-white" style="background:var(--brand);">
                        Download
                    </div>

                    <div class="p-4 space-y-4">
                        <div>
                            <label class="text-sm font-bold text-slate-700">Judul Section</label>
                            <input type="text" name="travel_docs_download_title"
                                value="{{ old('travel_docs_download_title', $settings['travel_docs_download_title'] ?? '') }}"
                                class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200" />
                        </div>

                        <div class="text-xs text-slate-600">
                            Isi list download: <span class="font-bold">Label</span> + <span class="font-bold">URL</span> (Google Drive / URL public).
                        </div>

                        <div class="space-y-3">
                            @for($i=0; $i<$rows; $i++)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <input type="text" name="download_label[]"
                                    value="{{ old('download_label.'.$i, $arr[$i]['label'] ?? '') }}"
                                    placeholder="Label (contoh: Form Visa Jepang)"
                                    class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200" />
                                <input type="text" name="download_url[]"
                                    value="{{ old('download_url.'.$i, $arr[$i]['url'] ?? '') }}"
                                    placeholder="URL (contoh: https://...)"
                                    class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200" />
                        </div>
                        @endfor
                    </div>
                </div>
            </div>

        </div>
</div>

{{-- ACTION --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
    <button type="submit"
        class="rounded-xl px-6 py-3 text-sm font-extrabold text-white shadow-sm"
        style="background:var(--brand);">
        Simpan
    </button>
</div>

</form>
</div>

@include('admin.partials.wysiwyg')
@endsection