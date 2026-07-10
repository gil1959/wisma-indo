@extends('layouts.front')
@php
$isEn = app()->getLocale() === 'en';

// Static labels (ngikut pola rentcar index: ternary, bukan key setting baru)
$i18n = [
'page_title' => $isEn ? 'MICE Packages - Bintang Wisata' : 'Paket MICE - Bintang Wisata',

'search' => $isEn ? 'Search' : 'Pencarian',
'search_ph' => $isEn ? 'Example: Meeting, Conference, Exhibition...' : 'Contoh: Meeting, Conference, Exhibition...',
'category' => $isEn ? 'Category' : 'Kategori',
'all_categories' => $isEn ? 'All Categories' : 'Semua Kategori',
'empty_title' => $isEn ? 'No packages found' : 'Paket tidak ditemukan',
'empty_desc' => $isEn ? 'Try changing the keyword or choosing another category.' : 'Silakan ubah kata kunci atau pilih kategori lain untuk melihat paket yang tersedia.',
'empty_reset' => $isEn ? 'Reset Search' : 'Reset Pencarian',

'sort' => $isEn ? 'Sort' : 'Urutkan',
'sort_default' => $isEn ? 'Default' : 'Default',
'sort_newest' => $isEn ? 'Newest' : 'Terbaru',
'sort_price_low' => $isEn ? 'Lowest Price' : 'Harga Terendah',
'sort_price_high' => $isEn ? 'Highest Price' : 'Harga Tertinggi',

'btn_filter' => $isEn ? 'Apply Filter' : 'Filter',
'btn_reset' => $isEn ? 'Clear' : 'Reset',


'start_from' => $isEn ? 'From ' : 'Mulai ',
'per_person' => $isEn ? '/person' : '/orang',
'view_detail' => $isEn ? 'View Details' : 'Lihat Detail',
];
@endphp

@section('title', $i18n['page_title'])


@section('content')

{{-- ================= PAGE HEADER ================= --}}
<section class="relative overflow-hidden bg-white">
    <div class="absolute inset-0 travel-grid opacity-70"></div>

    <svg class="absolute -top-16 -right-16 w-[520px] h-[520px] opacity-80" viewBox="0 0 600 600" fill="none" aria-hidden="true">
        <defs>
            <radialGradient id="miceHeroGlow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(310 290) rotate(90) scale(280)">
                <stop stop-color="#0194F3" stop-opacity="0.22" />
                <stop offset="1" stop-color="#0194F3" stop-opacity="0" />
            </radialGradient>
        </defs>
        <circle cx="310" cy="290" r="280" fill="url(#miceHeroGlow)" />
        <path d="M130 330c70-90 170-150 280-150 40 0 80 7 120 20" stroke="#0194F3" stroke-opacity="0.25" stroke-width="2" stroke-linecap="round" />
        <path d="M165 385c85-70 160-105 245-105 70 0 125 18 170 42" stroke="#0194F3" stroke-opacity="0.18" stroke-width="2" stroke-linecap="round" />
    </svg>

    <div class="max-w-7xl mx-auto px-4 pt-10 pb-10 lg:pt-14 lg:pb-12 relative">
        <div class="grid gap-8 lg:grid-cols-12 items-center">

            <div class="lg:col-span-7" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-extrabold"
                    style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                    <span class="h-2 w-2 rounded-full" style="background:#0194F3;"></span>
                    {{ $isEn
    ? ($siteSettings['mice_hero_badge_en'] ?? $siteSettings['mice_hero_badge'] ?? 'MICE Package')
    : ($siteSettings['mice_hero_badge'] ?? 'Paket MICE')
}}
                </div>

                <h1 class="mt-4 text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">
                    {{ $isEn
    ? ($siteSettings['mice_hero_title_en'] ?? $siteSettings['mice_hero_title'] ?? 'MICE packages for your corporate event')
    : ($siteSettings['mice_hero_title'] ?? 'Solusi Paket MICE untuk Event Perusahaan Anda')
}}
                </h1>

                <p class="mt-3 text-slate-600 max-w-2xl">
                    {{ $isEn
    ? ($siteSettings['mice_hero_desc_en'] ?? $siteSettings['mice_hero_desc'] ?? 'Meetings, Incentives, Conferences, and Exhibitions. Browse packages, view details, and checkout easily.')
    : ($siteSettings['mice_hero_desc'] ?? 'Meetings, Incentives, Conferences, and Exhibitions. Pilih paket, lihat detail, dan lanjut checkout dengan mudah.')
}}
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="#mice-list" class="btn btn-primary">
                        <i data-lucide="briefcase" class="w-4 h-4"></i>
                        {{ $isEn
    ? ($siteSettings['mice_cta_button_en'] ?? $siteSettings['mice_cta_button'] ?? 'View Packages')
    : ($siteSettings['mice_cta_button'] ?? 'Lihat Paket')
}}
                    </a>


                </div>
            </div>

            {{-- TIPS (4 BOX) --}}
            <div class="lg:col-span-5" data-aos="fade-up" data-aos-delay="100">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                        <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                            <i data-lucide="calendar-check" class="w-4 h-4" style="color:#0194F3;"></i>
                            {{ $isEn
    ? ($siteSettings['mice_tip1_title_en'] ?? $siteSettings['mice_tip1_title'] ?? 'Event Ready')
    : ($siteSettings['mice_tip1_title'] ?? 'Event Ready')
}}

                        </div>
                        <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                            {{ $isEn
    ? ($siteSettings['mice_tip1_desc_en'] ?? $siteSettings['mice_tip1_desc'] ?? 'Ready for meetings, conferences, and exhibitions.')
    : ($siteSettings['mice_tip1_desc'] ?? 'Paket siap untuk meeting, conference, dan exhibition.')
}}
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                        <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                            <i data-lucide="badge-check" class="w-4 h-4" style="color:#0194F3;"></i>
                            {{ $isEn
    ? ($siteSettings['mice_tip2_title_en'] ?? $siteSettings['mice_tip2_title'] ?? 'Trusted')
    : ($siteSettings['mice_tip2_title'] ?? 'Terpercaya')
}}

                        </div>
                        <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                            {{ $isEn
    ? ($siteSettings['mice_tip2_desc_en'] ?? $siteSettings['mice_tip2_desc'] ?? 'Clear package options, complete details, easy to choose.')
    : ($siteSettings['mice_tip2_desc'] ?? 'Pilihan paket jelas, detail lengkap, mudah dipilih.')
}}
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                        <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                            <i data-lucide="wallet" class="w-4 h-4" style="color:#0194F3;"></i>
                            {{ $isEn
    ? ($siteSettings['mice_tip3_title_en'] ?? $siteSettings['mice_tip3_title'] ?? 'Flexible Pricing')
    : ($siteSettings['mice_tip3_title'] ?? 'Harga Fleksibel')
}}
                        </div>
                        <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                            {{ $isEn
    ? ($siteSettings['mice_tip3_desc_en'] ?? $siteSettings['mice_tip3_desc'] ?? 'Domestic and foreign price tiers can be adjusted to your needs.')
    : ($siteSettings['mice_tip3_desc'] ?? 'Tier harga Domestik & WNA bisa multi baris sesuai kebutuhan.')
}}

                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                        <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                            <i data-lucide="headphones" class="w-4 h-4" style="color:#0194F3;"></i>
                            {{ $isEn
    ? ($siteSettings['mice_tip4_title_en'] ?? $siteSettings['mice_tip4_title'] ?? 'Support')
    : ($siteSettings['mice_tip4_title'] ?? 'Support')
}}
                        </div>
                        <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                            {{ $isEn
    ? ($siteSettings['mice_tip4_desc_en'] ?? $siteSettings['mice_tip4_desc'] ?? 'Consultation available for your event needs and itinerary.')
    : ($siteSettings['mice_tip4_desc'] ?? 'Bisa konsultasi kebutuhan event dan itinerary.')
}}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- wave divider (sama pola index lain) --}}
    <svg class="block w-full" viewBox="0 0 1440 60" fill="none" aria-hidden="true">
        <path d="M0 40C240 10 480 10 720 40C960 70 1200 70 1440 40V60H0V40Z" fill="#F8FAFC" />
    </svg>
</section>

{{-- ================= FILTER BAR ================= --}}
<section class="max-w-7xl mx-auto px-4">
    <div class="card p-5 -mt-8 relative z-10" data-aos="fade-up" data-aos-delay="100">
        <form method="GET" action="{{ route('mice.index') }}" class="grid gap-4 md:grid-cols-12 items-end">

            {{-- SEARCH --}}
            <div class="md:col-span-6">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">
                    {{ $i18n['search'] }}
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="{{
  $isEn
    ? ($siteSettings['mice_filter_search_placeholder_en'] ?? $siteSettings['mice_filter_search_placeholder'] ?? $i18n['search_ph'])
    : ($siteSettings['mice_filter_search_placeholder'] ?? $i18n['search_ph'])
}}"
                        class="w-full rounded-xl border-slate-200 pl-11">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </span>
                </div>
            </div>

            {{-- CATEGORY --}}
            <div class="md:col-span-3">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">
                    {{ $i18n['category'] }}
                </label>
                <select name="category"
                    class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
                    <option value="">{{ $i18n['all_categories'] }}</option>

                    @php
                    $clean = function ($v) {
                    if (!is_string($v)) return null;
                    $s = trim($v);
                    if ($s === '' || in_array($s, ['_', '-', '—'], true)) return null;
                    return $v;
                    };
                    @endphp

                    @foreach($categories as $category)
                    @php
                    $catLabel = $isEn
                    ? ($clean($category->name_en) ?: $clean($category->name))
                    : $clean($category->name);

                    // kalau label jelek ("_"), skip biar gak muncul di dropdown
                    if (!$catLabel) continue;
                    @endphp

                    <option value="{{ $category->id }}"
                        {{ (string)request('category') === (string)$category->id ? 'selected' : '' }}>
                        {{ $catLabel }}
                    </option>
                    @endforeach


                </select>
            </div>

            {{-- SORT --}}
            <div class="md:col-span-3">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">{{ $i18n['sort'] }}</label>
                <select name="sort"
                    class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
                    <option value="">{{ $i18n['sort_default'] }}</option>
                    <option value="newest" {{ request('sort')=='newest' ? 'selected' : '' }}>{{ $i18n['sort_newest'] }}</option>
                    <option value="price_low" {{ request('sort')=='price_low' ? 'selected' : '' }}>{{ $i18n['sort_price_low'] }}</option>
                    <option value="price_high" {{ request('sort')=='price_high' ? 'selected' : '' }}>{{ $i18n['sort_price_high'] }}</option>
                </select>
            </div>

            <div class="md:col-span-12 flex flex-wrap gap-3 justify-end">
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    {{ $i18n['btn_filter'] }}
                </button>

                <a href="{{ route('mice.index') }}" class="btn btn-ghost border-slate-200 text-slate-700 hover:bg-slate-50">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    {{ $i18n['btn_reset'] }}
                </a>

            </div>
        </form>
    </div>
</section>

{{-- ================= PACKAGES GRID ================= --}}
<section id="mice-list" class="max-w-7xl mx-auto px-4 pt-10 pb-16">
    @if($packages->count())
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4" data-aos="fade-up" data-aos-delay="120">
        @foreach($packages as $package)
        @php
        // min price dari tiers domestic (kalau ada)
        $tiers = $package->tiers ?? collect();
        $minDomestic = $tiers->where('type', 'domestic')->where('price', '>', 0)->min('price');

        // fallback kalau domestic nggak ada yang valid
        $minDomestic = $minDomestic ?? $tiers->where('price', '>', 0)->min('price');
        $ratingValue = $package->rating_value ?? 0;
        $ratingCount = $package->rating_count ?? 0;

        $clean = function ($v) {
        if (!is_string($v)) return null;
        $s = trim($v);
        if ($s === '' || in_array($s, ['_', '-', '—'], true)) return null;
        return $v;
        };

        $fallbackTitle = $package->title ?: ucwords(str_replace('-', ' ', (string)$package->slug));

        $title = $isEn
        ? ($clean($package->title_en) ?: $clean($package->title))
        : $clean($package->title);

        if (!$title) $title = $fallbackTitle;

        $label = $isEn
        ? ($clean($package->label_en) ?: $clean($package->label))
        : $clean($package->label);

        $durationText = $isEn
        ? ($clean($package->duration_text_en) ?: $clean($package->duration_text))
        : $clean($package->duration_text);

        $catBase = $clean(optional($package->category)->name) ?? 'MICE';
        $catName = $isEn
        ? ($clean(optional($package->category)->name_en) ?: $catBase)
        : $catBase;

        @endphp

        <a href="{{ route('mice.show', $package) }}"
            class="group block bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">

            <div class="relative h-44 overflow-hidden bg-slate-100">
                @if($package->thumbnail_path)
                <img
                    src="{{ asset('storage/' . $package->thumbnail_path) }}"
                    alt="{{ $title }}"
                    class="h-full w-full object-cover">
                @else
                <div class="absolute inset-0 bg-gradient-to-tr from-slate-100 via-white to-white"></div>
                @endif

                @if(!empty($label))
                <div class="absolute top-3 right-3">
                    <span class="inline-flex items-center rounded-full bg-white/90 backdrop-blur border border-white/60 px-3 py-1 text-xs font-extrabold text-slate-900 shadow">
                        {{ $label }}
                    </span>
                </div>
                @endif

                <div class="absolute top-3 left-3">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/92 border border-slate-200 px-3 py-1 text-xs font-extrabold text-slate-700 shadow">
                        <i data-lucide="tag" class="w-4 h-4" style="color:#0194F3;"></i>
                        {{ $catName }}
                    </span>
                </div>
            </div>

            <div class="px-4 pt-4 pb-3">
                <div class="text-[15px] font-extrabold text-[#0194F3] line-clamp-2">
                    {{ $title }}
                </div>

                <div class="mt-2 text-sm">
                    <span class="text-slate-600">{{ $i18n['start_from'] }} </span>
                    <span class="font-extrabold text-rose-600">
                        @if($minDomestic !== null)
                        Rp {{ number_format((int)$minDomestic, 0, ',', '.') }}
                        @else
                        -
                        @endif
                    </span>
                    <span class="text-slate-500">{{ $i18n['per_person'] }}</span>
                </div>

                <div class="mt-2 flex items-center gap-2 text-xs text-slate-600">
                    <div class="flex items-center gap-0.5" aria-label="Rating">
                        @for($i=0; $i<5; $i++)
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#FBBF24" class="w-4 h-4">
                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                            </svg>
                            @endfor
                    </div>
                    <span>({{ (int)($package->rating_count ?? 0) }})</span>
                </div>
            </div>

            <div class="border-t border-slate-200 px-4 pt-3 pb-4">
                <div class="flex items-center gap-2 text-xs text-slate-600">
                    <i data-lucide="calendar" class="w-4 h-4" style="color:#0194F3;"></i>
                    <span class="line-clamp-1">{{ $durationText ?? '' }}</span>
                </div>

                <div class="mt-3">
                    <div class="btn btn-primary w-full justify-center !rounded-md !py-2">{{ $i18n['view_detail'] }}</div>
                </div>
            </div>
        </a>


        @endforeach
    </div>

    <div class="mt-10">
        {{ $packages->links() }}
    </div>
    @else
    <div class="card p-10 text-center" data-aos="fade-up">
        <div class="mx-auto w-14 h-14 rounded-2xl flex items-center justify-center"
            style="background: rgba(1,148,243,0.10);">
            <i data-lucide="search-x" class="w-7 h-7" style="color:#0194F3;"></i>
        </div>
        <h3 class="mt-4 text-lg font-extrabold text-slate-900">{{ $i18n['empty_title'] }}</h3>
        <p class="mt-2 text-slate-600">{{ $i18n['empty_desc'] }}</p>
        <div class="mt-6">
            <a href="{{ route('mice.index') }}" class="btn btn-primary">{{ $i18n['empty_reset'] }}</a>
        </div>

    </div>
    @endif
</section>

@endsection