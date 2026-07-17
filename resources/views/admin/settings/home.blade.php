{{-- prettier-ignore-file --}}
@extends('layouts.admin')

@section('title', 'Home Setting')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Home Setting</h1>
            <p class="mt-1 text-sm text-slate-600">Kelola tab di bagian hero homepage (To Do, Jemputan Bandara, Ferry, Travel, Sewa Mobil, dan tab tambahan).</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
        <div class="font-extrabold">Periksa input:</div>
        <ul class="list-disc pl-5 mt-1 text-sm">
            @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.home.save') }}" class="mt-6" enctype="multipart/form-data">
        @csrf

        {{-- ================= HERO SECTION ================= --}}
        <div class="card p-0 overflow-hidden mb-6">
            <div class="px-5 py-4 border-b bg-slate-50">
                <div class="text-sm font-extrabold text-slate-900">Hero Section</div>
                <div class="text-xs text-slate-600 mt-1">Pengaturan background dan teks untuk Hero.</div>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-extrabold text-slate-700">Judul Hero</label>
                        <input type="text" name="hero_title" value="{{ old('hero_title', $heroSettings['title'] ?? '') }}" class="mt-1 w-full rounded-xl border-slate-200" />
                    </div>
                    <div>
                        <label class="text-xs font-extrabold text-slate-700">Subjudul Hero</label>
                        <input type="text" name="hero_subtitle" value="{{ old('hero_subtitle', $heroSettings['subtitle'] ?? '') }}" class="mt-1 w-full rounded-xl border-slate-200" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-extrabold text-slate-700">Background Image</label>
                        @if(!empty($heroSettings['bg_image']))
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$heroSettings['bg_image']) }}" class="h-20 w-auto rounded-lg border object-cover" />
                        </div>
                        @endif
                        <input type="file" name="hero_bg_image" accept="image/*" class="mt-1 w-full rounded-xl border-slate-200" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= CUSTOM LINKS (CTA) ================= --}}
        <div class="card p-0 overflow-hidden mb-6">
            <div class="px-5 py-4 border-b bg-slate-50">
                <div class="text-sm font-extrabold text-slate-900">Tombol CTA (Kiri)</div>
                <div class="text-xs text-slate-600 mt-1">Tombol Cari Properti, Pasang Iklan, dsb.</div>
            </div>
            <div class="p-5">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-slate-600">
                                <th class="text-left font-extrabold py-2">Label</th>
                                <th class="text-left font-extrabold py-2">Sublabel</th>
                                <th class="text-left font-extrabold py-2">URL / Link</th>
                                <th class="text-left font-extrabold py-2">Icon Image</th>
                                <th class="py-2"></th>
                            </tr>
                        </thead>
                        <tbody id="customLinksRows" class="align-top">
                            @foreach(($customLinks ?? []) as $i => $l)
                            <tr class="border-t">
                                <td class="py-3 pr-3">
                                    <input type="text" name="custom_links[{{ $i }}][label]" value="{{ old('custom_links.'.$i.'.label', $l['label'] ?? '') }}" class="w-full rounded-xl border-slate-200" />
                                </td>
                                <td class="py-3 pr-3">
                                    <input type="text" name="custom_links[{{ $i }}][sublabel]" value="{{ old('custom_links.'.$i.'.sublabel', $l['sublabel'] ?? '') }}" class="w-full rounded-xl border-slate-200" />
                                </td>
                                <td class="py-3 pr-3">
                                    <input type="text" name="custom_links[{{ $i }}][url]" value="{{ old('custom_links.'.$i.'.url', $l['url'] ?? '') }}" class="w-full rounded-xl border-slate-200" />
                                </td>
                                <td class="py-3 pr-3">
                                    @php $exImg = $l['icon_image'] ?? ''; @endphp
                                    @if($exImg)
                                    <img src="{{ asset('storage/'.$exImg) }}" class="h-8 w-8 mb-2 rounded border object-cover" />
                                    @endif
                                    <input type="file" name="custom_links[{{ $i }}][icon_image]" accept="image/*" class="w-full rounded-xl border-slate-200" />
                                    <input type="hidden" name="custom_links[{{ $i }}][icon_image_existing]" value="{{ $exImg }}" />
                                </td>
                                <td class="py-3 text-right">
                                    <button type="button" class="inline-flex items-center justify-center h-9 w-9 rounded-xl border border-slate-200 hover:bg-slate-50" onclick="this.closest('tr').remove()">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    <button type="button" class="btn btn-ghost" onclick="addCustomLinkRow()">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Tombol
                    </button>
                </div>
            </div>
        </div>

        {{-- ================= SIDE BANNERS ================= --}}
        <div class="card p-0 overflow-hidden mb-6">
            <div class="px-5 py-4 border-b bg-slate-50">
                <div class="text-sm font-extrabold text-slate-900">Side Banners (Kanan)</div>
                <div class="text-xs text-slate-600 mt-1">Banner di sebelah kanan tombol CTA.</div>
            </div>
            <div class="p-5">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-slate-600">
                                <th class="text-left font-extrabold py-2">Image</th>
                                <th class="text-left font-extrabold py-2">URL / Link</th>
                                <th class="py-2"></th>
                            </tr>
                        </thead>
                        <tbody id="sideBannersRows" class="align-top">
                            @foreach(($sideBanners ?? []) as $i => $b)
                            <tr class="border-t">
                                <td class="py-3 pr-3">
                                    @php $exBImg = $b['image'] ?? ''; @endphp
                                    @if($exBImg)
                                    <img src="{{ asset('storage/'.$exBImg) }}" class="h-16 w-32 mb-2 rounded border object-cover" />
                                    @endif
                                    <input type="file" name="side_banners[{{ $i }}][image]" accept="image/*" class="w-full rounded-xl border-slate-200" />
                                    <input type="hidden" name="side_banners[{{ $i }}][image_existing]" value="{{ $exBImg }}" />
                                </td>
                                <td class="py-3 pr-3">
                                    <input type="text" name="side_banners[{{ $i }}][url]" value="{{ old('side_banners.'.$i.'.url', $b['url'] ?? '') }}" class="w-full rounded-xl border-slate-200" />
                                </td>
                                <td class="py-3 text-right">
                                    <button type="button" class="inline-flex items-center justify-center h-9 w-9 rounded-xl border border-slate-200 hover:bg-slate-50" onclick="this.closest('tr').remove()">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    <button type="button" class="btn btn-ghost" onclick="addSideBannerRow()">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Banner
                    </button>
                </div>
            </div>
        </div>

        <div class="card p-0 overflow-hidden">
            <div class="px-5 py-4 border-b bg-slate-50">
                <div class="text-sm font-extrabold text-slate-900">Tab Hero</div>
                <div class="text-xs text-slate-600 mt-1">Icon pakai <b>Lucide icon name</b> (contoh: plane, ship, bus, car, clipboard-check).</div>
            </div>

            <div class="p-5">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-slate-600">
                                <th class="text-left font-extrabold py-2">Label</th>
                                <th class="text-left font-extrabold py-2">Icon</th>
                                <th class="text-left font-extrabold py-2">Link</th>
                                <th class="text-left font-extrabold py-2">Icon Image</th>

                                <th class="py-2"></th>
                            </tr>
                        </thead>
                        <tbody id="tabsRows" class="align-top">
                            @foreach($tabs as $i => $t)
                            <tr class="border-t">
                                <td class="py-3 pr-3">
                                    <input type="text" name="tabs[{{ $i }}][label]" value="{{ old('tabs.'.$i.'.label', $t['label'] ?? '') }}" class="w-full rounded-xl border-slate-200" placeholder="Mis: To Do" />
                                </td>
                                <td class="py-3 pr-3">
                                    <input type="text" name="tabs[{{ $i }}][icon]" value="{{ old('tabs.'.$i.'.icon', $t['icon'] ?? '') }}" class="w-full rounded-xl border-slate-200" placeholder="Mis: plane" />
                                </td>
                                <td class="py-3 pr-3">
                                    <input type="text" name="tabs[{{ $i }}][url]" value="{{ old('tabs.'.$i.'.url', $t['url'] ?? '') }}" class="w-full rounded-xl border-slate-200" placeholder="Mis: /rent-car atau {{ route('rentcar.index') }}" />
                                    <div class="mt-1 text-xs text-slate-500">Boleh absolute (https://...) atau relative (/path).</div>
                                </td>
                                <td class="py-3 text-right">
                                    <button type="button" class="inline-flex items-center justify-center h-9 w-9 rounded-xl border border-slate-200 hover:bg-slate-50" onclick="removeRow(this)">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </td>
                                <td class="py-3 pr-3">
                                    @php $existingIconImage = old("tabs.$i.icon_image_existing", $t['icon_image'] ?? ''); @endphp

                                    @if($existingIconImage)
                                    <div class="mb-2 flex items-center gap-2">
                                        <img src="{{ asset('storage/'.$existingIconImage) }}" class="h-8 w-8 rounded-lg object-cover border" alt="">
                                        <span class="text-xs text-slate-500 truncate">{{ $existingIconImage }}</span>
                                    </div>
                                    @endif

                                    <input type="file"
                                        name="tabs[{{ $i }}][icon_image]"
                                        accept="image/*"
                                        class="w-full rounded-xl border-slate-200" />

                                    <input type="hidden"
                                        name="tabs[{{ $i }}][icon_image_existing]"
                                        value="{{ $existingIconImage }}" />

                                    <div class="mt-1 text-xs text-slate-500">Opsional. Kalau kosong pakai Lucide icon name.</div>
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <button type="button" class="btn btn-ghost" onclick="addRow()">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Tambah Tab
                    </button>

                    <div class="text-xs text-slate-500">Baris yang kosong tidak akan disimpan.</div>
                </div>
            </div>


        </div>
        @php
        $as = $articlesSettings ?? [
        'enabled' => (old('home_articles_enabled') ?? '0') === '1',
        'title' => old('home_articles_title') ?? 'Baca dan bangkitkan semangat liburanmu',
        'desc' => old('home_articles_desc') ?? '',
        'button_text' => old('home_articles_button_text') ?? 'Baca Artikel Inspirasi',
        'button_url' => old('home_articles_button_url') ?? '/artikel',
        'mode' => old('home_articles_mode') ?? 'custom',
        'custom_ids' => [],
        ];

        $selected = $selectedArticles ?? collect();
        @endphp

        <div class="mt-6 card p-0 overflow-hidden" id="homeArticlesCard">
            <div class="px-5 py-4 border-b bg-slate-50">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-extrabold text-slate-900">Section: Artikel Inspirasi</div>
                        <div class="text-xs text-slate-600 mt-1">Kelola artikel yang tampil di homepage. Icon wajib Lucide, tidak ada emoji.</div>
                    </div>

                    <label class="inline-flex items-center gap-2 select-none">
                        <input type="checkbox" name="home_articles_enabled" value="1"
                            class="rounded border-slate-300"
                            {{ old('home_articles_enabled', $as['enabled'] ? '1' : '0') === '1' ? 'checked' : '' }}>
                        <span class="text-sm font-extrabold text-slate-800">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="p-5">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="text-xs font-extrabold text-slate-700">Judul</label>
                                <input type="text"
                                    name="home_articles_title"
                                    value="{{ old('home_articles_title', $as['title']) }}"
                                    class="mt-1 w-full rounded-xl border-slate-200"
                                    placeholder="Mis: Baca dan bangkitkan semangat liburanmu" />
                                <div class="mt-1 text-xs text-slate-500">Max 80 karakter.</div>
                            </div>

                            <div>
                                <label class="text-xs font-extrabold text-slate-700">Deskripsi (opsional)</label>
                                <input type="text"
                                    name="home_articles_desc"
                                    value="{{ old('home_articles_desc', $as['desc']) }}"
                                    class="mt-1 w-full rounded-xl border-slate-200"
                                    placeholder="Kalimat singkat pendukung judul." />
                                <div class="mt-1 text-xs text-slate-500">Max 160 karakter.</div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-extrabold text-slate-700">Teks Tombol</label>
                                    <input type="text"
                                        name="home_articles_button_text"
                                        value="{{ old('home_articles_button_text', $as['button_text']) }}"
                                        class="mt-1 w-full rounded-xl border-slate-200"
                                        placeholder="Mis: Baca Artikel Inspirasi" />
                                </div>
                                <div>
                                    <label class="text-xs font-extrabold text-slate-700">Link Tombol</label>
                                    <input type="text"
                                        name="home_articles_button_url"
                                        value="{{ old('home_articles_button_url', $as['button_url']) }}"
                                        class="mt-1 w-full rounded-xl border-slate-200"
                                        placeholder="/artikel atau https://..." />
                                </div>
                            </div>

                            <div>
                                <div class="text-xs font-extrabold text-slate-700">Mode Konten</div>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 hover:bg-slate-50 cursor-pointer">
                                        <input type="radio" name="home_articles_mode" value="custom"
                                            {{ old('home_articles_mode', $as['mode']) === 'custom' ? 'checked' : '' }}>
                                        <span class="text-sm font-extrabold text-slate-800">Custom (pilih manual)</span>
                                    </label>

                                    <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 hover:bg-slate-50 cursor-pointer">
                                        <input type="radio" name="home_articles_mode" value="auto"
                                            {{ old('home_articles_mode', $as['mode']) === 'auto' ? 'checked' : '' }}>
                                        <span class="text-sm font-extrabold text-slate-800">Auto (terbaru)</span>
                                    </label>
                                </div>
                                <div class="mt-1 text-xs text-slate-500">Auto akan ambil artikel terbaru yang published. Custom tampil sesuai urutan list.</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="rounded-2xl border border-slate-200 overflow-hidden">
                            <div class="px-4 py-3 bg-white border-b border-slate-200 flex items-center justify-between gap-3">
                                <div class="text-sm font-extrabold text-slate-900">Kelola Artikel</div>
                                <div class="text-xs text-slate-500">Max 12 item.</div>
                            </div>

                            <div class="p-4 bg-slate-50">
                                <div class="relative">
                                    <label class="text-xs font-extrabold text-slate-700">Cari artikel</label>
                                    <div class="mt-1 flex items-center gap-2">
                                        <div class="relative flex-1">
                                            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-500"></i>
                                            <input type="text"
                                                id="articleSearchInput"
                                                class="w-full rounded-xl border-slate-200 pl-10"
                                                placeholder="Ketik minimal 2 karakter (judul/slug)" />
                                        </div>
                                        <button type="button" class="btn btn-ghost" id="articleSearchClearBtn">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                            Clear
                                        </button>
                                    </div>

                                    <div id="articleSearchDropdown" class="hidden absolute z-20 mt-2 w-full rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden">
                                        <div class="px-4 py-2 border-b bg-slate-50 text-xs text-slate-600">
                                            Hasil pencarian
                                        </div>
                                        <div id="articleSearchResults" class="max-h-72 overflow-auto"></div>
                                    </div>
                                </div>

                                <input type="hidden" name="home_articles_custom_ids" id="homeArticlesCustomIds" value="" />

                                <div class="mt-4">
                                    <div class="text-xs font-extrabold text-slate-700">Artikel terpilih</div>
                                    <div class="mt-2" id="selectedArticlesWrap">
                                        <div id="selectedArticlesEmpty" class="rounded-xl border border-dashed border-slate-300 bg-white px-4 py-6 text-center text-sm text-slate-600 hidden">
                                            Belum ada artikel dipilih.
                                        </div>

                                        <div id="selectedArticlesList" class="space-y-2"></div>
                                    </div>
                                </div>

                                <div class="mt-3 text-xs text-slate-500">
                                    Tips: gunakan tombol panah untuk atur urutan. Urutan ini yang tampil di homepage.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                {{-- ================= HOME: Banner Titles ================= --}}
                <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-5">
                    <div class="text-lg font-extrabold text-slate-900">Banner di Home</div>
                    <p class="mt-1 text-sm text-slate-600">Mengatur judul teks di section banner Discount & Missions.</p>

                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-bold text-slate-700">Judul Banner Discount</label>
                            <input
                                type="text"
                                name="home_discount_banner_title"
                                value="{{ old('home_discount_banner_title', $siteSettings['home_discount_banner_title'] ?? '') }}"
                                class="mt-1 w-full rounded-xl border-slate-200"
                                placeholder="Discount up to 50% + instant cashback">
                        </div>

                        <div>
                            <label class="text-sm font-bold text-slate-700">Judul Banner Missions</label>
                            <input
                                type="text"
                                name="home_mission_banner_title"
                                value="{{ old('home_mission_banner_title', $siteSettings['home_mission_banner_title'] ?? '') }}"
                                class="mt-1 w-full rounded-xl border-slate-200"
                                placeholder="Earn up to IDR 850K from missions">
                        </div>
                    </div>
                </div>




                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
        @php
        $homeArticlesInitData = ($selected ?? collect())->map(function ($a) {
        return [
        'id' => $a->id,
        'title' => $a->title,
        'slug' => $a->slug,
        'cover_image' => $a->cover_image,
        'is_published' => (bool) $a->is_published,
        'published_at' => optional($a->published_at)->toISOString(),
        ];
        })->values()->all();
        @endphp

        <div id="homeArticlesInitData" data-items='@json($homeArticlesInitData)' class="hidden"></div>

        <div id="tabsMeta" data-index="{{ count($tabs) }}" class="hidden"></div>
        <script>
            // =========================
            // HOME ARTICLES ADMIN MANAGER
            // =========================
            const HOME_ARTICLES = (function() {
                const searchUrl = @json(route('admin.settings.home.articles.search'));
                const maxItems = 12;

                const state = {
                    selected: [],
                    searchTimer: null,
                };

                function formatDate(iso) {
                    if (!iso) return '';
                    const d = new Date(iso);
                    if (isNaN(d.getTime())) return '';
                    return d.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                }

                function escapeHtml(str) {
                    return String(str ?? '')
                        .replaceAll('&', '&amp;')
                        .replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;')
                        .replaceAll('"', '&quot;')
                        .replaceAll("'", '&#039;');
                }

                function refreshLucide() {
                    if (window.lucide && typeof window.lucide.createIcons === 'function') {
                        window.lucide.createIcons();
                    }
                }

                function setHiddenIds() {
                    const ids = state.selected.map(a => a.id);
                    document.getElementById('homeArticlesCustomIds').value = JSON.stringify(ids);
                }

                function renderSelected() {
                    const list = document.getElementById('selectedArticlesList');
                    const empty = document.getElementById('selectedArticlesEmpty');

                    if (!state.selected.length) {
                        list.innerHTML = '';
                        empty.classList.remove('hidden');
                        setHiddenIds();
                        return;
                    }

                    empty.classList.add('hidden');

                    list.innerHTML = state.selected.map((a, idx) => {
                        const cover = a.cover_image ? `/storage/${a.cover_image}` : '';
                        const status = a.is_published ? 'Published' : 'Draft';
                        const badgeClass = a.is_published ?
                            'bg-emerald-50 text-emerald-700 border-emerald-200' :
                            'bg-slate-100 text-slate-700 border-slate-200';

                        return `
                <div class="rounded-2xl border border-slate-200 bg-white px-3 py-3 flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="h-12 w-16 rounded-xl bg-slate-100 overflow-hidden flex-shrink-0">
                            ${cover
                                ? `<img src="${cover}" alt="" class="h-full w-full object-cover">`
                                : `<div class="h-full w-full grid place-items-center text-[10px] text-slate-500">No Image</div>`
                            }
                        </div>

                        <div class="min-w-0">
                            <div class="text-sm font-extrabold text-slate-900 truncate">${escapeHtml(a.title)}</div>
                            <div class="mt-1 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center rounded-lg border px-2 py-0.5 text-[11px] font-extrabold ${badgeClass}">
                                    ${status}
                                </span>
                                ${a.published_at ? `<span class="text-[11px] text-slate-500">${formatDate(a.published_at)}</span>` : ''}
                                <span class="text-[11px] text-slate-500 truncate">/${escapeHtml(a.slug)}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button type="button" class="inline-flex items-center justify-center h-9 w-9 rounded-xl border border-slate-200 hover:bg-slate-50"
                            title="Naik" data-action="up" data-index="${idx}">
                            <i data-lucide="chevron-up" class="w-4 h-4"></i>
                        </button>
                        <button type="button" class="inline-flex items-center justify-center h-9 w-9 rounded-xl border border-slate-200 hover:bg-slate-50"
                            title="Turun" data-action="down" data-index="${idx}">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </button>
                        <button type="button" class="inline-flex items-center justify-center h-9 w-9 rounded-xl border border-slate-200 hover:bg-slate-50"
                            title="Hapus" data-action="remove" data-index="${idx}">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>`;
                    }).join('');

                    setHiddenIds();
                    refreshLucide();
                }

                function openDropdown() {
                    document.getElementById('articleSearchDropdown').classList.remove('hidden');
                }

                function closeDropdown() {
                    document.getElementById('articleSearchDropdown').classList.add('hidden');
                }

                function renderResults(items) {
                    const wrap = document.getElementById('articleSearchResults');
                    if (!items.length) {
                        wrap.innerHTML = `<div class="px-4 py-3 text-sm text-slate-600">Tidak ada hasil.</div>`;
                        openDropdown();
                        return;
                    }

                    wrap.innerHTML = items.map(a => {
                        const cover = a.cover_image ? `/storage/${a.cover_image}` : '';
                        const status = a.is_published ? 'Published' : 'Draft';
                        const badgeClass = a.is_published ?
                            'bg-emerald-50 text-emerald-700 border-emerald-200' :
                            'bg-slate-100 text-slate-700 border-slate-200';

                        const already = state.selected.some(s => s.id === a.id);
                        const disabled = already || state.selected.length >= maxItems;

                        return `
                <div class="px-4 py-3 border-t hover:bg-slate-50 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="h-10 w-14 rounded-xl bg-slate-100 overflow-hidden flex-shrink-0">
                            ${cover
                                ? `<img src="${cover}" alt="" class="h-full w-full object-cover">`
                                : `<div class="h-full w-full grid place-items-center text-[10px] text-slate-500">No Image</div>`
                            }
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-extrabold text-slate-900 truncate">${escapeHtml(a.title)}</div>
                            <div class="mt-1 flex items-center gap-2">
                                <span class="inline-flex items-center rounded-lg border px-2 py-0.5 text-[11px] font-extrabold ${badgeClass}">
                                    ${status}
                                </span>
                                <span class="text-[11px] text-slate-500 truncate">/${escapeHtml(a.slug)}</span>
                            </div>
                        </div>
                    </div>

                    <button type="button"
                        class="btn btn-ghost ${disabled ? 'opacity-50 pointer-events-none' : ''}"
                        data-action="add"
                        data-id="${a.id}"
                        data-payload='${escapeHtml(JSON.stringify(a))}'>
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Tambah
                    </button>
                </div>`;
                    }).join('');

                    openDropdown();
                    refreshLucide();
                }

                async function doSearch(q) {
                    try {
                        const res = await fetch(`${searchUrl}?q=${encodeURIComponent(q)}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const json = await res.json();
                        renderResults(json.data || []);
                    } catch (e) {
                        renderResults([]);
                    }
                }

                function bind() {
                    const input = document.getElementById('articleSearchInput');
                    const clearBtn = document.getElementById('articleSearchClearBtn');

                    input.addEventListener('input', function() {
                        const q = input.value.trim();
                        if (state.searchTimer) clearTimeout(state.searchTimer);

                        if (q.length < 2) {
                            closeDropdown();
                            return;
                        }

                        state.searchTimer = setTimeout(() => doSearch(q), 250);
                    });

                    clearBtn.addEventListener('click', function() {
                        input.value = '';
                        closeDropdown();
                        input.focus();
                    });

                    document.addEventListener('click', function(e) {
                        const card = document.getElementById('homeArticlesCard');
                        if (!card.contains(e.target)) {
                            closeDropdown();
                        }
                    });

                    document.getElementById('articleSearchResults').addEventListener('click', function(e) {
                        const btn = e.target.closest('button[data-action="add"]');
                        if (!btn) return;

                        if (state.selected.length >= maxItems) return;

                        const payload = btn.getAttribute('data-payload');
                        let a = null;
                        try {
                            a = JSON.parse(payload);
                        } catch (_) {}

                        if (!a || !a.id) return;
                        if (state.selected.some(s => s.id === a.id)) return;

                        state.selected.push(a);
                        renderSelected();
                    });

                    document.getElementById('selectedArticlesList').addEventListener('click', function(e) {
                        const btn = e.target.closest('button[data-action]');
                        if (!btn) return;

                        const action = btn.getAttribute('data-action');
                        const idx = parseInt(btn.getAttribute('data-index'), 10);
                        if (Number.isNaN(idx)) return;

                        if (action === 'remove') {
                            state.selected.splice(idx, 1);
                            renderSelected();
                            return;
                        }

                        if (action === 'up' && idx > 0) {
                            const tmp = state.selected[idx - 1];
                            state.selected[idx - 1] = state.selected[idx];
                            state.selected[idx] = tmp;
                            renderSelected();
                            return;
                        }

                        if (action === 'down' && idx < state.selected.length - 1) {
                            const tmp = state.selected[idx + 1];
                            state.selected[idx + 1] = state.selected[idx];
                            state.selected[idx] = tmp;
                            renderSelected();
                            return;
                        }
                    });
                }

                function init(initialSelected) {
                    state.selected = Array.isArray(initialSelected) ? initialSelected : [];
                    renderSelected();
                    bind();
                    refreshLucide();
                }

                return {
                    init
                };
            })();
            // INIT dari server (selectedArticles)
            const initEl = document.getElementById('homeArticlesInitData');
            let initItems = [];
            try {
                initItems = JSON.parse(initEl.getAttribute('data-items') || '[]');
            } catch (e) {
                initItems = [];
            }
            HOME_ARTICLES.init(initItems);
        </script>

    </form>
    {{-- ================= FOOTER LOGOS (MAX 9 ACTIVE) ================= --}}
    <div class="mt-10 rounded-3xl border border-slate-200 bg-white p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">Footer Logos</h2>
                <p class="mt-1 text-sm text-slate-600">Logo di footer (maksimal 9 yang aktif). Bisa diurutkan pakai Sort Order.</p>

                @error('footer_logo')
                <div class="mt-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>

        {{-- Add form --}}
        <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-5">
            <div class="text-sm font-extrabold text-slate-900">Tambah Logo</div>

            <form class="mt-4 grid grid-cols-1 md:grid-cols-12 gap-4"
                action="{{ route('admin.settings.home.footer-logos.store') }}"
                method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Nama</label>
                    <input name="name" required
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-sky-200"
                        placeholder="Contoh: IATA" />
                </div>

                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Logo (PNG/JPG/WEBP/SVG)</label>
                    <input type="file" name="image" required
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
                </div>

                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">URL (optional)</label>
                    <input name="url"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-sky-200"
                        placeholder="https://..." />
                </div>

                <div class="md:col-span-1">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Order</label>
                    <input name="sort_order" type="number" min="0" max="999" value="0"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-sky-200" />
                </div>

                <div class="md:col-span-12 flex items-center justify-between gap-4">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300">
                        Aktifkan logo ini
                    </label>

                    <button class="inline-flex items-center justify-center rounded-2xl bg-sky-600 px-5 py-3 text-white font-semibold hover:bg-sky-700">
                        Tambah
                    </button>
                </div>
            </form>
        </div>

        {{-- List --}}
        <div class="mt-6">
            <div class="text-sm font-extrabold text-slate-900 mb-3">Daftar Logo</div>

            <div class="overflow-x-auto rounded-3xl border border-slate-200">
                <table class="min-w-full bg-white">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-semibold text-slate-600">
                            <th class="px-4 py-3">Preview</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">URL</th>
                            <th class="px-4 py-3">Order</th>
                            <th class="px-4 py-3">Aktif</th>
                            <th class="px-4 py-3 w-[260px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($footerLogos ?? [] as $lg)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="h-12 w-28 rounded-xl border border-slate-200 bg-white flex items-center justify-center p-2">
                                    <img src="{{ asset('storage/' . $lg->image_path) }}" alt="{{ $lg->name }}" class="h-8 w-auto object-contain">
                                </div>
                            </td>

                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $lg->name }}</td>

                            <td class="px-4 py-3 text-sm text-slate-600">
                                @if($lg->url)
                                <a href="{{ $lg->url }}" target="_blank" class="text-sky-700 hover:underline">{{ $lg->url }}</a>
                                @else
                                <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm text-slate-700">{{ $lg->sort_order }}</td>

                            <td class="px-4 py-3">
                                @if($lg->is_active)
                                <span class="inline-flex rounded-full bg-emerald-50 text-emerald-700 px-3 py-1 text-xs font-semibold">Aktif</span>
                                @else
                                <span class="inline-flex rounded-full bg-slate-100 text-slate-600 px-3 py-1 text-xs font-semibold">Nonaktif</span>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                {{-- Update inline form --}}
                                <form class="grid grid-cols-12 gap-2 items-end"
                                    action="{{ route('admin.settings.home.footer-logos.update', $lg->id) }}"
                                    method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="col-span-6">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama</label>
                                        <input name="name" value="{{ $lg->name }}" required
                                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                                    </div>

                                    <div class="col-span-6">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Ganti Logo (opsional)</label>
                                        <input type="file" name="image"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                    </div>

                                    <div class="col-span-6">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">URL</label>
                                        <input name="url" value="{{ $lg->url }}"
                                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                                    </div>

                                    <div class="col-span-2">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Order</label>
                                        <input name="sort_order" type="number" min="0" max="999" value="{{ $lg->sort_order }}"
                                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" />
                                    </div>

                                    <div class="col-span-4 flex items-center gap-3">
                                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                            <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300"
                                                @if($lg->is_active) checked @endif>
                                            Aktif
                                        </label>

                                        <button class="inline-flex items-center justify-center rounded-xl bg-sky-600 px-4 py-2 text-white text-sm font-semibold hover:bg-sky-700">
                                            Update
                                        </button>
                                    </div>
                                </form>

                                {{-- Delete --}}
                                <form class="mt-2"
                                    action="{{ route('admin.settings.home.footer-logos.destroy', $lg->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Hapus logo ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm font-semibold text-red-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-sm text-slate-500">Belum ada logo.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 text-xs text-slate-500">
                (max 9)
            </div>
        </div>
    </div>
</div>

<script>
    let tabsIndex = parseInt(document.getElementById('tabsMeta')?.getAttribute('data-index') || '0', 10);
    let customLinksIndex = {{ count($customLinks ?? []) }};
    let sideBannersIndex = {{ count($sideBanners ?? []) }};

    function addCustomLinkRow() {
        const tbody = document.getElementById('customLinksRows');
        const tr = document.createElement('tr');
        tr.className = 'border-t';
        tr.innerHTML = `
            <td class="py-3 pr-3">
                <input type="text" name="custom_links[${customLinksIndex}][label]" class="w-full rounded-xl border-slate-200" />
            </td>
            <td class="py-3 pr-3">
                <input type="text" name="custom_links[${customLinksIndex}][sublabel]" class="w-full rounded-xl border-slate-200" />
            </td>
            <td class="py-3 pr-3">
                <input type="text" name="custom_links[${customLinksIndex}][url]" class="w-full rounded-xl border-slate-200" />
            </td>
            <td class="py-3 pr-3">
                <input type="file" name="custom_links[${customLinksIndex}][icon_image]" accept="image/*" class="w-full rounded-xl border-slate-200" />
            </td>
            <td class="py-3 text-right">
                <button type="button" class="inline-flex items-center justify-center h-9 w-9 rounded-xl border border-slate-200 hover:bg-slate-50" onclick="this.closest('tr').remove()">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        customLinksIndex++;
        if(window.lucide) window.lucide.createIcons();
    }

    function addSideBannerRow() {
        const tbody = document.getElementById('sideBannersRows');
        const tr = document.createElement('tr');
        tr.className = 'border-t';
        tr.innerHTML = `
            <td class="py-3 pr-3">
                <input type="file" name="side_banners[${sideBannersIndex}][image]" accept="image/*" class="w-full rounded-xl border-slate-200" />
            </td>
            <td class="py-3 pr-3">
                <input type="text" name="side_banners[${sideBannersIndex}][url]" class="w-full rounded-xl border-slate-200" />
            </td>
            <td class="py-3 text-right">
                <button type="button" class="inline-flex items-center justify-center h-9 w-9 rounded-xl border border-slate-200 hover:bg-slate-50" onclick="this.closest('tr').remove()">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        sideBannersIndex++;
        if(window.lucide) window.lucide.createIcons();
    }

    function addRow() {
        const tbody = document.getElementById('tabsRows');
        const tr = document.createElement('tr');
        tr.className = 'border-t';
        tr.innerHTML = `
            <td class="py-3 pr-3">
                <input type="text" name="tabs[${tabsIndex}][label]" class="w-full rounded-xl border-slate-200" placeholder="Mis: To Do" />
            </td>
            <td class="py-3 pr-3">
                <input type="text" name="tabs[${tabsIndex}][icon]" class="w-full rounded-xl border-slate-200" placeholder="Mis: plane" />
            </td>
            <td class="py-3 pr-3">
                <input type="text" name="tabs[${tabsIndex}][url]" class="w-full rounded-xl border-slate-200" placeholder="Mis: /rent-car" />
                <div class="mt-1 text-xs text-slate-500">Boleh absolute (https://...) atau relative (/path).</div>
            </td>
            <td class="py-3 text-right">
                <button type="button" class="inline-flex items-center justify-center h-9 w-9 rounded-xl border border-slate-200 hover:bg-slate-50" onclick="removeRow(this)">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </td>
            <td class="py-3 pr-3">
  <input type="file" name="tabs[${tabsIndex}][icon_image]" accept="image/*" class="w-full rounded-xl border-slate-200" />
  <input type="hidden" name="tabs[${tabsIndex}][icon_image_existing]" value="" />
  <div class="mt-1 text-xs text-slate-500">Opsional. Kalau kosong pakai Lucide.</div>
</td>

        `;
        tbody.appendChild(tr);
        tabsIndex++;

        // refresh lucide icons if available
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    }

    function removeRow(btn) {
        const tr = btn.closest('tr');
        if (tr) tr.remove();
    }
</script>
@endsection