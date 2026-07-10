@extends('layouts.front')
@php $isEn = app()->getLocale() === 'en'; @endphp
@section('title', $isEn ? 'Tour Packages - Bintang Wisata' : 'Paket Tour - Bintang Wisata')

@section('content')


{{-- ================= PAGE HEADER ================= --}}
<section class="relative overflow-hidden bg-white">
    <div class="absolute inset-0 travel-grid opacity-70"></div>
    <svg class="absolute -top-16 -right-16 w-[520px] h-[520px] opacity-80" viewBox="0 0 600 600" fill="none" aria-hidden="true">
        <defs>
            <radialGradient id="toursHeroGlow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(310 290) rotate(90) scale(280)">
                <stop stop-color="#0194F3" stop-opacity="0.22" />
                <stop offset="1" stop-color="#0194F3" stop-opacity="0" />
            </radialGradient>
        </defs>
        <circle cx="310" cy="290" r="280" fill="url(#toursHeroGlow)" />
        <path d="M130 330c70-90 170-150 280-150 40 0 80 7 120 20" stroke="#0194F3" stroke-opacity="0.25" stroke-width="2" stroke-linecap="round" />
        <path d="M165 385c85-70 160-105 245-105 70 0 125 18 170 42" stroke="#0194F3" stroke-opacity="0.18" stroke-width="2" stroke-linecap="round" />
    </svg>

    <div class="max-w-7xl mx-auto px-4 pt-10 pb-10 lg:pt-14 lg:pb-12 relative">
        <div class="grid gap-8 lg:grid-cols-12 items-center">
            <div class="lg:col-span-7" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-extrabold"
                    style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                    <span class="h-2 w-2 rounded-full" style="background:#0194F3;"></span>
                    {{ $siteSettings['tour_hero_badge'] ?? 'Paket Tour' }}

                </div>

                <h1 class="mt-4 text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">
                    {{ $siteSettings['tour_hero_title'] ?? 'Temukan Paket Tour yang Sesuai Kebutuhan Anda' }}
                </h1>


                <p class="mt-3 text-slate-600 max-w-2xl">
                    {{ $siteSettings['tour_hero_desc'] ?? 'Gunakan pencarian dan filter untuk menyaring paket berdasarkan destinasi maupun kategori.' }}
                </p>


                <div class="mt-6 flex flex-wrap gap-2">
                    <span class="pill pill-azure"><i data-lucide="map-pin" class="w-4 h-4"></i> {{ $siteSettings['tour_filter_dest_label'] ?? 'Destinasi' }}</span>
                    <span class="pill pill-azure"><i data-lucide="tag" class="w-4 h-4"></i> {{ $siteSettings['tour_filter_cat_label'] ?? 'Kategori' }}</span>
                    <span class="pill pill-azure"><i data-lucide="clock" class="w-4 h-4"></i> {{ $siteSettings['tour_filter_dur_label'] ?? 'Durasi' }}</span>
                    <span class="pill pill-azure"><i data-lucide="shield-check" class="w-4 h-4"></i> {{ $siteSettings['tour_filter_trans_label'] ?? 'Transparan' }}</span>
                </div>

            </div>

            {{-- right illustration --}}
            <div class="lg:col-span-5" data-aos="fade-up" data-aos-delay="80">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft relative overflow-hidden">
                    <div class="absolute inset-0 travel-dots opacity-60 pointer-events-none"></div>

                    <div class="relative">
                        {{-- Header --}}
                        <div class="flex items-start gap-3">
                            <div class="icon-badge shrink-0">
                                <i data-lucide="compass" class="w-5 h-5"></i>
                            </div>

                            <div>
                                <div class="font-extrabold text-slate-900">
                                    {{ $siteSettings['tour_tips_title'] ?? 'Tips Cepat' }}
                                </div>
                                <div class="text-sm text-slate-600 mt-0.5">
                                    {{ $siteSettings['tour_tips_desc'] ?? 'Gunakan kata kunci destinasi untuk hasil lebih akurat.' }}
                                </div>
                            </div>
                        </div>

                        {{-- Cards --}}
                        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {{-- Card 1 --}}
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="sparkles" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['tour_tip1_title'] ?? 'Rekomendasi' }}
                                </div>
                                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                                    {{ $siteSettings['tour_tip1_desc'] ?? 'Paket favorit pelanggan' }}
                                </div>
                            </div>

                            {{-- Card 2 --}}
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="route" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['tour_tip2_title'] ?? 'Itinerary' }}
                                </div>
                                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                                    {{ $siteSettings['tour_tip2_desc'] ?? 'Alur perjalanan jelas' }}
                                </div>
                            </div>

                            {{-- Card 3 --}}
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="users" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['tour_tip3_title'] ?? 'Grup' }}
                                </div>
                                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                                    {{ $siteSettings['tour_tip3_desc'] ?? 'Cocok untuk rombongan' }}
                                </div>
                            </div>

                            {{-- Card 4 --}}
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="headphones" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['tour_tip4_title'] ?? 'Support' }}
                                </div>
                                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                                    {{ $siteSettings['tour_tip4_desc'] ?? 'Bisa konsultasi trip' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- wave divider --}}
    <svg class="block w-full" viewBox="0 0 1440 100" fill="none" aria-hidden="true">
        <path d="M0 40C180 90 360 90 540 55C720 20 900 20 1080 55C1260 90 1350 85 1440 60V100H0V40Z" fill="#F8FAFC" />
    </svg>
</section>

{{-- ================= FILTER BAR ================= --}}
<section class="max-w-7xl mx-auto px-4">
    <div class="card p-5 -mt-8 relative z-10" data-aos="fade-up" data-aos-delay="100">
        <form method="GET" action="{{ route('tours.index') }}" class="grid gap-4 md:grid-cols-12 items-end"
            x-data="{
                submit(){
                  const base = '{{ url('/paket-tour') }}';
                  const raw = (this.$refs.categorySelect?.value || '').trim();

                  let path = base;

                  if(raw){
                    const parts = raw.split(':');
                    const type = parts[0];
                    const val  = parts[1] || '';

                    if(type === 'cat' && val){
                      path += '/' + encodeURIComponent(val);
                    }

                    if(type === 'sub' && val){
                      const segs = val.split('/').filter(Boolean);
                      path += '/' + segs.map(s => encodeURIComponent(s)).join('/');
                    }
                  }

                  const params = new URLSearchParams();

                  const q = (this.$refs.q?.value || '').trim();
                  if(q) params.set('q', q);

                  const sort = (this.$refs.sort?.value || '').trim();
                  if(sort) params.set('sort', sort);

                  const qs = params.toString();
                  window.location.href = qs ? (path + '?' + qs) : path;
                }
              }"
            @submit.prevent="submit()">

            {{-- SEARCH --}}
            <div class="md:col-span-5">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">
                    {{ $isEn ? 'Search' : 'Pencarian' }}
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="q"

                        value="{{ request('q') }}"
                        placeholder="{{ $isEn ? 'Example: Bali, Lombok, Japan...' : 'Contoh: Bali, Lombok, Jepang...' }}"
                        class="w-full rounded-xl border-slate-200 pl-11">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </span>
                </div>
            </div>

            {{-- CATEGORY --}}

            <div class="md:col-span-3">
                <label class="text-[11px] font-extrabold text-slate-600">
                    {{ $siteSettings['tour_filter_cat_label'] ?? ($isEn ? 'Category' : 'Kategori') }}
                </label>

                <div class="mt-1">
                    <select
                        x-ref="categorySelect"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/25 bg-white">
                        <option value="">{{ $isEn ? 'All Categories' : 'Semua Kategori' }}</option>

                        @foreach(($tourMainCategories ?? collect()) as $cat)
                        <option
                            value="cat:{{ $cat->slug }}"
                            {{ (($activeCategory && $activeCategory->id == $cat->id) && !$activeSubcategory) ? 'selected' : '' }}>
                            {{ strtoupper($cat->name) }}
                        </option>

                        @foreach(($cat->children ?? collect()) as $sub)
                        <option
                            value="sub:{{ $cat->slug }}/{{ $sub->slug }}"
                            {{ ($activeSubcategory && $activeSubcategory->id == $sub->id) ? 'selected' : '' }}>
                            ㅤㅤ{{ $sub->name }}
                        </option>
                        @endforeach
                        @endforeach

                    </select>
                </div>
            </div>




            {{-- SORT --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">
                    {{ $isEn ? 'Sort' : 'Urutkan' }}
                </label>
                <select
                    name="sort"

                    class="w-full rounded-xl border-slate-200">
                    <option value="title_asc" @selected(request('sort','title_asc')==='title_asc' )>
                        {{ $isEn ? 'Name (A–Z)' : 'Nama (A–Z)' }}
                    </option>
                    <option value="newest" @selected(request('sort')==='newest' )>
                        {{ $isEn ? 'Newest' : 'Terbaru' }}
                    </option>
                    <option value="oldest" @selected(request('sort')==='oldest' )>
                        {{ $isEn ? 'Oldest' : 'Terlama' }}
                    </option>
                </select>
            </div>

            {{-- ACTIONS --}}
            <div class="md:col-span-2 flex gap-3">
                <button class="btn btn-primary w-full" type="submit">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                    {{ $isEn ? 'Apply' : 'Terapkan' }}
                </button>

                <a class="btn btn-ghost w-full"
                    href="{{ route('tours.index') }}">
                    {{ $isEn ? 'Reset' : 'Reset' }}
                </a>

            </div>
        </form>

        {{-- Active filters summary --}}
        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-slate-600">
            <span class="font-extrabold text-slate-700">
                {{ $isEn ? 'Active filters:' : 'Filter aktif:' }}
            </span>

            @if(request('q'))
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-1">
                <i data-lucide="type" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                {{ $isEn ? 'Keyword:' : 'Kata kunci:' }}
                <span class="font-extrabold">{{ request('q') }}</span>
            </span>
            @endif

            @if($activeCategory)
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-1">
                <i data-lucide="tag" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                {{ $isEn ? 'Category:' : 'Kategori:' }}
                <span class="font-extrabold">{{ $activeCategory->name }}</span>
            </span>
            @endif

            @if($activeSubcategory)
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-1">
                <i data-lucide="tag" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                {{ $isEn ? 'Subcategory:' : 'Sub Kategori:' }}
                <span class="font-extrabold">{{ $activeSubcategory->name }}</span>
            </span>
            @endif

            @if(!request('q') && !$activeCategory && !$activeSubcategory)
            <span class="text-slate-500">{{ $isEn ? 'None' : 'Tidak ada' }}</span>
            @endif

        </div>
    </div>
</section>

{{-- ================= LIST ================= --}}
<section class="max-w-7xl mx-auto px-4 py-12 lg:py-14">
    <div class="flex items-end justify-between gap-4 mb-6" data-aos="fade-up">
        <div>
            <h2 class="text-xl lg:text-2xl font-extrabold text-slate-900">
                {{ $isEn ? 'Tour Packages' : 'Daftar Paket Tour' }}
            </h2>
            <p class="mt-1 text-slate-600 text-sm">
                {{ $isEn ? 'Showing' : 'Menampilkan' }}
                {{ $packages->total() }}
                {{ $isEn ? 'packages.' : 'paket.' }}
            </p>

        </div>
    </div>

    @if($packages->count())
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 items-stretch" data-aos="fade-up" data-aos-delay="120">
        @foreach($packages as $package)
        @php
        $minPrice = ($package->tiers ?? collect())->min('price');

        // rating
        $ratingValue = (float)($package->rating_value ?? 5);
        $ratingCount = (int)($package->rating_count ?? 0);

        $title = $isEn ? ($package->title_en ?: $package->title) : $package->title;
        $label = $isEn ? ($package->label_en ?: $package->label) : $package->label;
        $durationText = $isEn ? ($package->duration_text_en ?: $package->duration_text) : $package->duration_text;
        @endphp
        <a href="{{ route('tour.show', $package) }}"
            class="group block bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">

            {{-- Thumbnail + overlay badge/label --}}
            <div class="relative h-44 overflow-hidden bg-slate-100">
                @if($package->thumbnail_path)
                <img
                    src="{{ asset('storage/' . $package->thumbnail_path) }}"
                    alt="{{ $title }}"
                    class="h-full w-full object-cover">
                @else
                <div class="absolute inset-0 bg-gradient-to-tr from-slate-100 via-white to-white"></div>
                @endif

                {{-- LABEL (top-right) --}}
                @if(!empty($label))
                <div class="absolute top-3 right-3">
                    <span class="inline-flex items-center rounded-full bg-red-600 border border-red-600 px-3 py-1 text-xs font-extrabold text-white shadow">
                        {{ $label }}
                    </span>
                </div>
                @endif

                {{-- BADGE KATEGORI (top-left) --}}
                <div class="absolute top-3 left-3">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/92 border border-slate-200 px-3 py-1 text-xs font-extrabold text-slate-700 shadow">
                        <i data-lucide="tag" class="w-4 h-4" style="color:#0194F3;"></i>
                        {{ $package->category?->name ?? 'Tour' }}
                    </span>
                </div>
            </div>

            {{-- Content --}}
            <div class="px-4 pt-4 pb-3">
                <div class="text-[15px] font-extrabold text-[#0194F3] line-clamp-2">
                    {{ $title }}
                </div>

                <div class="mt-2 text-sm">
                    <span class="text-slate-600">{{ $isEn ? 'From' : 'Mulai' }} </span>
                    <span class="font-extrabold text-rose-600">
                        @if($minPrice !== null)
                        Rp {{ number_format((int) $minPrice, 0, ',', '.') }}
                        @else
                        -
                        @endif
                    </span>
                    <span class="text-slate-500">{{ $isEn ? '/person' : '/orang' }}</span>
                </div>

                <div class="mt-2 flex items-center gap-2 text-xs text-slate-600">
                    <div class="flex items-center gap-0.5" aria-label="Rating">
                        @for($i=0; $i<5; $i++)
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#FBBF24" class="w-4 h-4">
                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                            </svg>
                            @endfor
                    </div>
                    <span>({{ $ratingCount }})</span>
                </div>
            </div>

            <div class="border-t border-slate-200 px-4 pt-3 pb-4">
                <div class="flex items-center gap-2 text-xs text-slate-600">
                    <i data-lucide="calendar" class="w-4 h-4" style="color:#0194F3;"></i>
                    <span class="line-clamp-1">{{ $durationText }}</span>
                </div>

                <div class="mt-3">
                    <div class="btn btn-primary w-full justify-center !rounded-md !py-2">
                        {{ $isEn ? 'View Details' : 'Lihat Detail' }}
                    </div>
                </div>
            </div>
        </a>

        @endforeach
    </div>

    <div class="mt-10">
        {{ $packages->links() }}
    </div>
    @else
    {{-- empty state --}}
    <div class="card p-10 text-center" data-aos="fade-up">
        <div class="mx-auto h-14 w-14 rounded-2xl border flex items-center justify-center"
            style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22);">
            <i data-lucide="search-x" class="w-6 h-6" style="color:#0194F3;"></i>
        </div>
        <h3 class="mt-4 text-lg font-extrabold text-slate-900">
            {{ $isEn ? 'No packages found' : 'Paket tidak ditemukan' }}
        </h3>
        <p class="mt-2 text-slate-600">
            {{ $isEn ? 'Try adjusting your keyword or selecting a different category.' : 'Silakan ubah kata kunci atau pilih kategori lain untuk melihat paket yang tersedia.' }}
        </p>
        <a href="{{ route('tours.index') }}" class="btn btn-primary">
            {{ $isEn ? 'Reset Search' : 'Reset Pencarian' }}
        </a>
    </div>
    @endif
</section>

{{-- ================= CTA ================= --}}
<section class="max-w-7xl mx-auto px-4 pb-16">
    <div class="rounded-3xl text-white p-8 lg:p-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 relative overflow-hidden"
        style="background: linear-gradient(90deg, #0194F3 0%, rgba(1,148,243,0.70) 100%);"
        data-aos="fade-up">
        <div class="absolute inset-0 opacity-60 pointer-events-none">
            <svg class="absolute -top-10 -right-10 w-72 h-72" viewBox="0 0 300 300" fill="none" aria-hidden="true">
                <circle cx="150" cy="150" r="120" fill="#FFFFFF" fill-opacity="0.10" />
                <path d="M70 160c35-45 80-70 130-70 20 0 40 4 60 12" stroke="#FFFFFF" stroke-opacity="0.22" stroke-width="3" stroke-linecap="round" />
                <path d="M95 205c42-34 78-50 115-50 30 0 55 8 80 19" stroke="#FFFFFF" stroke-opacity="0.18" stroke-width="3" stroke-linecap="round" />
            </svg>
        </div>

        <div class="max-w-2xl relative">
            <h2 class="text-2xl font-extrabold">
                {{ $siteSettings['tour_cta_title'] ?? 'Membutuhkan Rekomendasi Paket yang Tepat?' }}
            </h2>
            <p class="mt-2 text-white/90">
                {{ $siteSettings['tour_cta_desc'] ?? 'Tim kami siap membantu memilih itinerary yang sesuai dengan waktu, preferensi, dan anggaran Anda.' }}
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 relative">
            <a href="{{ $siteSettings['tour_cta_link'] ?? '#' }}"
                class="btn bg-white text-slate-900 hover:bg-white/90">
                <i data-lucide="messages-square" class="w-4 h-4"></i>
                {{ $siteSettings['tour_cta_button'] ?? 'Konsultasi' }}
            </a>

            <a href="{{ $siteSettings['tour_cta_secondary_link'] ?? route('rentcar.index') }}"
                class="btn btn-ghost border-white/30 text-white hover:bg-white/10">
                <i data-lucide="car" class="w-4 h-4"></i>
                {{ $siteSettings['tour_cta_secondary_button'] ?? 'Lihat Rental' }}
            </a>

        </div>

    </div>
</section>

@endsection