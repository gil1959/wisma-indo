@extends('layouts.front')
@php $isEn = app()->getLocale() === 'en'; @endphp
@section('title', ($isEn ? 'Home - ' : 'Beranda - ') . ($siteSettings['seo_site_title'] ?? 'Wisma Indo'))



@section('content')

{{-- ================= HERO ================= --}}
<section class="relative min-h-[88vh] flex items-center justify-center text-center overflow-hidden">
    {{-- background --}}
    <div class="absolute inset-0">
        <img
            src="{{ $siteSettings['hero_image'] ?? '' }}"
            alt="Wisma Indo"
            class="h-full w-full object-cover">
    </div>

    {{-- content --}}
    <div class="relative max-w-4xl mx-auto px-4" data-aos="fade-up">


        <h1 class="mt-6 text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-900 drop-shadow-md">
            {{ $siteSettings['hero_title'] ?? ($isEn ? 'Comfortable & Trusted Trips' : 'Perjalanan Nyaman & Terpercaya') }}
        </h1>

        <p class="mt-4 text-sm md:text-base text-slate-800 max-w-2xl mx-auto drop-shadow-sm font-semibold">
            {{ $siteSettings['hero_subtitle'] ?? ($isEn ? 'We help you plan your trip with professional service and transparent pricing.' : 'Kami membantu Anda merencanakan perjalanan dengan layanan profesional dan harga transparan.') }}
        </p>
    </div>
</section>




        @php
        $homeTabs = $siteSettings['home_tabs'] ?? [];
        $homeTabs = is_array($homeTabs) ? $homeTabs : [];

        $tabsWithId = [];
        foreach ($homeTabs as $t) {
        $label = (string)($t['label'] ?? '');
        $id = \Illuminate\Support\Str::slug($label ?: 'tab');

        $iconImage = (string)($t['icon_image'] ?? '');
        $iconSrc = $iconImage ? asset('storage/'.$iconImage) : null;

        $tabsWithId[] = [
        'id' => $id,
        'label' => $label,
        'url' => (string)($t['url'] ?? '#'),
        'icon' => (string)($t['icon'] ?? 'sparkles'),
        'icon_image'=> $iconImage,
        'icon_src' => $iconSrc,
        'is_todo' => (mb_strtolower(trim($label)) === 'to do') || ($id === 'to-do'),
        ];
        }
        @endphp


        @if(count($tabsWithId) > 0)
        <section class="relative z-20 -mt-16 md:-mt-24 lg:-mt-32 w-full max-w-6xl mx-auto px-4 mb-10">
            <div
                x-data="{
  tabs: @js($tabsWithId),
  active: (@js(($tabsWithId[0]['id'] ?? ''))),

 canPrev: false,
canNext: false,
isOverflow: false,

init() {
    // default aktif: To Do kalau ada
    const todo = this.tabs.find(t => t.is_todo);
    if (todo) this.active = todo.id;

    this.$nextTick(() => {
      if (window.lucide) window.lucide.createIcons();
      this.syncArrows();
      window.addEventListener('resize', () => this.syncArrows());
    });
  },

  clickTab(e, t) {
    if (t.is_todo) {
      e.preventDefault();
      this.active = t.id;
      this.$nextTick(() => {
        if (window.lucide) window.lucide.createIcons();
      });
      return;
    }
    // selain To Do: navigasi normal via href
  },

  scrollTabs(dir) {
    const el = this.$refs.tabsScroller;
    if (!el) return;

    const step = Math.max(240, Math.floor(el.clientWidth * 0.65));
    el.scrollBy({ left: dir * step, behavior: 'smooth' });

    // update arrow state setelah animasi scroll
    setTimeout(() => this.syncArrows(), 220);
  },

 syncArrows() {
  const el = this.$refs.tabsScroller;
  if (!el) return;

  const maxLeft = el.scrollWidth - el.clientWidth;

  // overflow kalau konten lebih lebar dari viewport
  this.isOverflow = el.scrollWidth > (el.clientWidth + 2);

  this.canPrev = el.scrollLeft > 4;
  this.canNext = el.scrollLeft < (maxLeft - 4);
}
}"
                class="relative mx-auto w-full">
                {{-- TABS BAR --}}
                {{-- TABS BAR (NO SCROLLBAR + ARROWS) --}}
                <div class="absolute left-1/2 -translate-x-1/2 w-[min(100%,980px)] px-3" style="top:-28px;">
                    <div class="relative rounded-full bg-white/95 backdrop-blur border border-white/70 shadow-[0_16px_40px_rgba(2,6,23,0.18)] px-3 py-2">

                        {{-- Left Arrow --}}
                        <button
                            type="button"
                            class="absolute left-1 top-1/2 -translate-y-1/2 h-10 w-10 rounded-full bg-white shadow border border-slate-200 hidden md:grid place-items-center z-10"
                            x-show="canPrev"
                            x-cloak
                            @click="scrollTabs(-1)"
                            aria-label="{{ $isEn ? 'Previous' : 'Sebelumnya' }}">
                            <i data-lucide="chevron-left" class="w-5 h-5"></i>
                        </button>

                        {{-- Right Arrow --}}
                        <button
                            type="button"
                            class="absolute right-1 top-1/2 -translate-y-1/2 h-10 w-10 rounded-full bg-white shadow border border-slate-200 hidden md:grid place-items-center z-10"
                            x-show="canNext"
                            x-cloak
                            @click="scrollTabs(1)"
                            aria-label="{{ $isEn ? 'Next' : 'Berikutnya' }}"
                            aria-label="{{ $isEn ? 'Next' : 'Berikutnya' }}">
                            <i data-lucide="chevron-right" class="w-5 h-5"></i>
                        </button>

                        {{-- Viewport --}}
                        <div class="overflow-hidden px-0 md:px-11">
                            <div
                                class="flex items-center gap-2 no-scrollbar"
                                :class="isOverflow ? 'justify-start' : 'justify-center'"
                                x-ref="tabsScroller"
                                style="overflow-x:auto; -webkit-overflow-scrolling:touch;"
                                @scroll.throttle.50ms="syncArrows()">
                                <template x-for="t in tabs" :key="t.id">
                                    <a
                                        :href="t.url"
                                        @click="clickTab($event, t)"
                                        class="group shrink-0 inline-flex items-center gap-1.5 md:gap-2 rounded-full px-3 py-1.5 md:px-4 md:py-2 text-xs md:text-sm font-extrabold border transition-all duration-200"
                                        :class="active === t.id
      ? 'bg-[rgba(1,148,243,0.12)] border-[rgba(1,148,243,0.55)] text-[#055a93] shadow-[0_10px_18px_rgba(1,148,243,0.18)]'
      : 'bg-white border-slate-200/70 text-slate-700 hover:-translate-y-[1px] hover:bg-[rgba(1,148,243,0.06)] hover:border-[rgba(1,148,243,0.45)] hover:text-[#055a93] hover:shadow-[0_10px_18px_rgba(1,148,243,0.16)]'
    ">
                                        <span
                                            class="h-7 w-7 md:h-9 md:w-9 rounded-full grid place-items-center border transition overflow-hidden"
                                            :class="active === t.id
            ? 'bg-white border-[rgba(1,148,243,0.35)]'
            : 'bg-slate-50 border-slate-200/70 group-hover:bg-[rgba(1,148,243,0.10)] group-hover:border-[rgba(1,148,243,0.35)]'
        ">
                                            {{-- ICON IMAGE (kalau ada) --}}
                                            <template x-if="t.icon_src">
                                                <img :src="t.icon_src" alt="" class="h-full w-full object-cover">
                                            </template>

                                            {{-- FALLBACK LUCIDE --}}
                                            <template x-if="!t.icon_src">
                                                <i :data-lucide="t.icon" class="w-3.5 h-3.5 md:w-4 md:h-4" style="color:#0194F3;"></i>
                                            </template>
                                        </span>

                                        <span class="whitespace-nowrap" x-text="t.label"></span>
                                    </a>
                                </template>
                            </div>
                        </div>

                    </div>
                </div>


                {{-- PANEL CARD: CUMA UNTUK TO DO --}}
                <div class="pt-[45px] hidden md:block">
                    <div class="rounded-[24px] bg-white/95 backdrop-blur border border-white/70 shadow-[0_12px_40px_rgba(2,6,23,0.15)] overflow-hidden">
                        <div class="px-5 pt-5 pb-4 border-b border-slate-200/70">
                            <div class="text-base font-extrabold text-slate-900">
                                {{ $siteSettings['home_search_title'] ?? ($isEn ? 'Find Tour Packages' : 'Cari Paket Wisata') }}
                            </div>
                            <div class="text-xs text-slate-600 mt-1">
                                {{ $siteSettings['home_search_desc'] ?? ($isEn ? 'Find packages by destination, category, and departure date.' : 'Temukan paket sesuai destinasi, kategori, dan tanggal keberangkatan.') }}
                            </div>
                        </div>

                        <div class="p-5">
                            <div x-show="tabs.find(x => x.id === active)?.is_todo" x-cloak>
                                <form method="GET" action="{{ route('tours.index') }}"
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

                  const date = (this.$refs.date?.value || '').trim();
                  if(date) params.set('date', date);

                  const qs = params.toString();
                  window.location.href = qs ? (path + '?' + qs) : path;
                }
              }"
                                    @submit.prevent="submit()">

                                    <div class="grid gap-2.5 md:grid-cols-12 items-end">

                                        <div class="md:col-span-5">
                                            <label class="text-[10px] uppercase tracking-wider font-extrabold text-slate-600">
                                                {{ $isEn ? 'Destination / Keywords' : 'Destinasi / Kata Kunci' }}
                                            </label>
                                            <div class="mt-1 relative">
                                                <input type="text" name="q" x-ref="q"
                                                    placeholder="{{ $isEn ? 'Example: Bali, Lombok, Japan, Labuan Bajo...' : 'Contoh: Bali, Lombok, Jepang, Labuan Bajo...' }}"
                                                    class="w-full rounded-xl border border-slate-200 pl-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/25">
                                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                                    <i data-lucide="search" class="w-4 h-4"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="md:col-span-3">
                                            <label class="text-[10px] uppercase tracking-wider font-extrabold text-slate-600">
                                                {{ $isEn ? 'Category' : 'Kategori' }}
                                            </label>

                                            <div class="mt-1">
                                                <select
                                                    x-ref="categorySelect"
                                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/25 bg-white">
                                                    <option value="">{{ $isEn ? 'All Categories' : 'Semua Kategori' }}</option>

                                                    @foreach(($tourMainCategories ?? collect()) as $cat)
                                                    <option value="cat:{{ $cat->slug }}">{{ strtoupper($cat->name) }}</option>

                                                    @foreach(($cat->children ?? collect()) as $sub)
                                                    <option value="sub:{{ $cat->slug }}/{{ $sub->slug }}">ㅤㅤ{{ $sub->name }}</option>
                                                    @endforeach
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="text-[10px] uppercase tracking-wider font-extrabold text-slate-600">
                                                {{ $isEn ? 'Departure Date' : 'Tanggal Berangkat' }}
                                            </label>
                                            <input type="date" name="date" x-ref="date"
                                                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/25">
                                        </div>

                                        <div class="md:col-span-2">
                                            <button type="submit"
                                                class="w-full rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition-all duration-200
                           shadow-[0_10px_20px_rgba(1,148,243,0.25)] hover:-translate-y-[1px]
                           hover:shadow-[0_14px_24px_rgba(1,148,243,0.30)]"
                                                style="background:#0194F3"
                                                onmouseover="this.style.background='#0186DB'"
                                                onmouseout="this.style.background='#0194F3'">
                                                {{ $isEn ? 'Search' : 'Ayo Cari' }}
                                            </button>
                                        </div>

                                    </div>

                                    <div class="mt-4 flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5">
                                        <span class="mt-0.5">
                                            <i data-lucide="sparkles" class="w-4 h-4" style="color:#0194F3;"></i>
                                        </span>
                                        <span class="text-xs text-slate-600">
                                            {{ $siteSettings['home_search_hint'] ?? ($isEn ? 'Use specific keywords to get more relevant results.' : 'Pakai kata kunci yang spesifik agar hasil lebih relevan.') }}
                                        </span>
                                    </div>
                                </form>
                            </div>

                            {{-- kalau aktif bukan To Do, panelnya gak usah tampil apa-apa (karena tab lain navigasi halaman) --}}
                            <div x-show="!tabs.find(x => x.id === active)?.is_todo" x-cloak class="hidden"></div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

@include('front.partials.home-banner-discount')
@include('front.partials.home-banner-missions')

{{-- ================= STATS / HIGHLIGHTS ================= --}}
<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 py-12 lg:py-16">
        <div class="grid gap-6 lg:grid-cols-12 items-center" data-aos="fade-up">
            <div class="lg:col-span-5">
                <div class="pill pill-azure">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    {{ $siteSettings['home_highlight_label'] ?? ($isEn ? 'Why we’re different' : 'Kenapa layanan kami beda') }}
                </div>

                <h2 class="mt-4 text-2xl lg:text-3xl font-extrabold text-slate-900">
                    {{ $siteSettings['home_highlight_title'] ?? ($isEn ? 'Detailed, organized, and focused on your travel experience.' : 'Detail, rapi, dan fokus ke pengalaman perjalanan.') }}
                </h2>

                <p class="mt-3 text-slate-600">
                    {{ $siteSettings['home_highlight_desc'] ?? ($isEn ? 'We make your trip feel “done right” from the start: clear info, easy-to-follow itinerary, and responsive team.' : 'Kami bikin trip terasa “beres” dari awal: informasi jelas, itinerary enak diikuti, dan tim responsif.') }}
                </p>

                <div class="mt-6 grid grid-cols-2 gap-4">
                    <div class="card p-5">
                        <div class="flex items-center gap-3">
                            <div class="icon-badge">
                                <i data-lucide="badge-check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="text-slate-900 font-extrabold">
                                    {{ $siteSettings['home_highlight_left1_title'] ?? ($isEn ? 'Transparent Pricing' : 'Harga Transparan') }}

                                </div>
                                <div class="text-slate-600 text-xs mt-0.5">
                                    {{ $siteSettings['home_highlight_left1_desc'] ?? ($isEn ? 'No hidden fees' : 'Tanpa biaya tersembunyi') }}

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card p-5">
                        <div class="flex items-center gap-3">
                            <div class="icon-badge">
                                <i data-lucide="route" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="text-slate-900 font-extrabold">
                                    {{ $siteSettings['home_highlight_left2_title'] ?? ($isEn ? 'Clear Itinerary' : 'Itinerary Jelas') }}

                                </div>
                                <div class="text-slate-600 text-xs mt-0.5">
                                    {{ $siteSettings['home_highlight_left2_desc'] ?? ($isEn ? 'Structured route & timing' : 'Rute & waktu terstruktur') }}

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card p-5">
                        <div class="flex items-center gap-3">
                            <div class="icon-badge">
                                <i data-lucide="calendar-check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="text-slate-900 font-extrabold">
                                    {{ $siteSettings['home_highlight_left3_title'] ?? ($isEn ? 'Fast Booking' : 'Booking Cepat') }}

                                </div>
                                <div class="text-slate-600 text-xs mt-0.5">
                                    {{ $siteSettings['home_highlight_left3_desc'] ?? ($isEn ? 'Short & clear form' : 'Form ringkas & jelas') }}

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card p-5">
                        <div class="flex items-center gap-3">
                            <div class="icon-badge">
                                <i data-lucide="messages-square" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="text-slate-900 font-extrabold">
                                    {{ $siteSettings['home_highlight_left4_title'] ?? ($isEn ? 'Active Support' : 'Support Aktif') }}

                                </div>
                                <div class="text-slate-600 text-xs mt-0.5">
                                    {{ $siteSettings['home_highlight_left4_desc'] ?? ($isEn ? 'Trip consultation available' : 'Bisa konsultasi trip') }}

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- illustration panel --}}
            <div class="lg:col-span-7">
                <div class="relative rounded-3xl overflow-hidden border border-slate-200 bg-slate-50 travel-dots p-6 lg:p-8 shadow-soft">
                    <div class="absolute inset-0 pointer-events-none">
                        <svg class="absolute -top-10 -right-8 w-64 h-64 opacity-70" viewBox="0 0 300 300" fill="none" aria-hidden="true">
                            <path d="M40 170c30-70 95-110 170-110 20 0 40 3 60 9" stroke="#0194F3" stroke-opacity="0.35" stroke-width="2" stroke-linecap="round" />
                            <path d="M60 200c45-55 90-80 145-80 35 0 65 10 95 26" stroke="#0194F3" stroke-opacity="0.22" stroke-width="2" stroke-linecap="round" />
                            <path d="M100 240l18-22 18 22" stroke="#0194F3" stroke-opacity="0.35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2 relative">
                        <div class="card p-6">
                            <div class="flex items-start gap-4">
                                <div class="icon-badge">
                                    <i data-lucide="map-pin" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="font-extrabold text-slate-900">
                                        {{ $siteSettings['home_highlight_right1_title'] ?? ($isEn ? 'Popular Destinations' : 'Destinasi Favorit') }}

                                    </div>
                                    <div class="text-sm text-slate-600 mt-1">
                                        {{ $siteSettings['home_highlight_right1_desc'] ?? ($isEn ? 'Bali, Lombok, Yogyakarta, Bandung, and international destinations (depending on the package).' : 'Bali, Lombok, Jogja, Bandung, sampai destinasi luar negeri (tergantung paket).') }}

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card p-6">
                            <div class="flex items-start gap-4">
                                <div class="icon-badge">
                                    <i data-lucide="users" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="font-extrabold text-slate-900">
                                        {{ $siteSettings['home_highlight_right2_title'] ?? ($isEn ? 'Great for Groups' : 'Cocok untuk Grup') }}

                                    </div>
                                    <div class="text-sm text-slate-600 mt-1">
                                        {{ $siteSettings['home_highlight_right2_desc'] ?? ($isEn ? 'Family, office, or community trips — tailored to your needs.' : 'Trip keluarga, kantor, komunitas — tinggal sesuaikan kebutuhan.') }}

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card p-6">
                            <div class="flex items-start gap-4">
                                <div class="icon-badge">
                                    <i data-lucide="wallet" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="font-extrabold text-slate-900">
                                        {{ $siteSettings['home_highlight_right3_title'] ?? 'Budget Friendly' }}

                                    </div>
                                    <div class="text-sm text-slate-600 mt-1">
                                        {{ $siteSettings['home_highlight_right3_desc'] ?? ($isEn ? 'Flexible packages with clear pricing from the start.' : 'Paket fleksibel dengan informasi harga jelas sejak awal.') }}

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card p-6">
                            <div class="flex items-start gap-4">
                                <div class="icon-badge">
                                    <i data-lucide="camera" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="font-extrabold text-slate-900">
                                        {{ $siteSettings['home_highlight_right4_title'] ?? ($isEn ? 'Best Photo Spots' : 'Spot Wisata Terbaik') }}

                                    </div>
                                    <div class="text-sm text-slate-600 mt-1">
                                        {{ $siteSettings['home_highlight_right4_desc'] ?? ($isEn ? 'Experience-first: great views, iconic places, and a comfortable trip flow.' : 'Fokus pengalaman: view bagus, tempat ikonik, dan alur perjalanan nyaman.') }}

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('tours.index') }}" class="btn btn-primary">
                            <i data-lucide="compass" class="w-5 h-5"></i>
                            {{ $siteSettings['home_highlight_cta_primary_text'] ?? ($isEn ? 'Explore Packages' : 'Mulai Jelajah Paket') }}

                        </a>
                        <a href="{{ route('rentcar.index') }}" class="btn btn-ghost">
                            <i data-lucide="car-front" class="w-5 h-5"></i>
                            {{ $siteSettings['home_highlight_cta_secondary_text'] ?? ($isEn ? 'View Rental Fleet' : 'Cek Armada Rental') }}

                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



{{-- ================= WHY US ================= --}}
<section class="bg-slate-50">
    @include('front.partials.home-promo-tours')
    @include('front.partials.home-promo-ships')

    @include('front.partials.home-promo-umrah')
    @include('front.partials.home-promo-mice')
    @include('front.partials.home-inspiration-articles')

    {{-- ================= INSPIRASI DESTINASI ================= --}}
    <div class="mt-10 rounded-3xl border border-slate-200 bg-white p-6 lg:p-8 travel-grid shadow-soft" data-aos="fade-up" data-aos-delay="140">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <div class="pill pill-azure">
                    <i data-lucide="map" class="w-4 h-4"></i>
                    {{ $isEn ? 'Destination inspiration' : 'Inspirasi destinasi' }}
                </div>
                <div class="mt-3 text-xl lg:text-2xl font-extrabold text-slate-900">
                    {{ $isEn ? 'Travel vibes ready for you to explore' : 'Nuansa wisata yang siap kamu jelajahi' }}
                </div>
                <p class="mt-2 text-slate-600">
                    {{ $isEn ? 'Pick a destination and view tour packages by category.' : 'Pilih destinasi favorit, lalu lihat daftar paket tour sesuai kategori.' }}
                </p>

            </div>

            <div class="hidden lg:block text-sm text-slate-500">
                Total: <span class="font-extrabold text-slate-900">{{ $inspirations->count() }}</span>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
            @forelse($inspirations as $it)
            @php
            $href = route('tours.index', array_filter([
            'category' => $it->tour_category_id,
            'subcategory' => $it->tour_subcategory_id,
            ]));


            $img = $it->image_path ? asset('storage/'.$it->image_path) : null;
            @endphp

            <a href="{{ $href }}" class="group block rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition bg-white">
                <div class="relative aspect-[4/5]">
                    @if($img)
                    <img
                        src="{{ $img }}"
                        alt="{{ $it->title }}"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.03] transition duration-500">
                    @else
                    <div class="absolute inset-0 bg-slate-200"></div>
                    <div class="absolute inset-0 grid place-items-center text-slate-500 text-xs">
                        No Image
                    </div>
                    @endif

                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>

                    <div class="absolute left-3 bottom-10 text-[10px] font-extrabold tracking-wider text-white/90">
                        {{ $isEn ? 'TOUR PACKAGE' : 'PAKET WISATA' }}
                    </div>

                    <div class="absolute left-3 bottom-3 right-3 text-white">
                        <div class="text-lg font-black leading-tight uppercase">
                            {{ $isEn ? ($it->title_en ?: $it->title) : $it->title }}
                        </div>

                        <div class="mt-2 inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold bg-orange-500 text-white">
                            {{ $isEn ? 'Details' : 'Detail' }}
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-2 md:col-span-4 rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center text-slate-600">
                {{ $isEn ? 'No destination inspirations yet. Add them via the Admin Panel.' : 'Belum ada inspirasi destinasi. Tambahkan lewat Admin Panel.' }}
            </div>
            @endforelse
        </div>
    </div>


    </div>
</section>

{{-- ================= FEATURED TOURS ================= --}}
<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 py-14 lg:py-20">
        <div class="flex items-end justify-between gap-4" data-aos="fade-up">
            <div>
                <div class="pill pill-azure">
                    <i data-lucide="bookmark" class="w-4 h-4"></i>
                    {{ $isEn ? 'Recommended' : 'Rekomendasi' }}
                </div>
                <h2 class="mt-4 text-2xl lg:text-3xl font-extrabold text-slate-900">
                    {{ $isEn ? 'Featured Tour Packages' : 'Paket Tour Pilihan' }}
                </h2>
                <p class="mt-2 text-slate-600">
                    {{ $isEn ? 'Our most popular trip recommendations.' : 'Rekomendasi perjalanan yang paling diminati oleh pelanggan kami.' }}
                </p>
            </div>
            <a href="{{ route('tours.index') }}" class="hidden sm:inline-flex btn btn-ghost">
                {{ $isEn ? 'View All' : 'Lihat Semua' }}
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-5" data-aos="fade-up" data-aos-delay="120">

            @forelse($packages as $package)
            @php
            $minPrice = ($package->tiers ?? collect())->min('price');
            $ratingCount = (int)($package->rating_count ?? 0);
            $durationText = $isEn ? ($package->duration_text_en ?: ($package->duration_text ?? '')) : ($package->duration_text ?? '');
            @endphp

            <a href="{{ route('tour.show', $package) }}"
                class="group block bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">

                <div class="relative h-44 overflow-hidden bg-slate-100">
                    @if($package->thumbnail_path)
                    <img
                        src="{{ asset('storage/'.$package->thumbnail_path) }}"
                        alt="{{ $package->title }}"
                        class="h-full w-full object-cover">
                    @else
                    <div class="absolute inset-0 bg-gradient-to-tr from-slate-100 via-white to-white"></div>
                    <div class="absolute inset-0 grid place-items-center text-slate-500 text-sm">
                        <div class="text-center">
                            <i data-lucide="image" class="w-8 h-8 mx-auto mb-2" style="color:#0194F3;"></i>
                            No Image
                        </div>
                    </div>
                    @endif

                    @if($package->destination)
                    <div class="absolute top-3 left-3">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/92 border border-slate-200 px-3 py-1 text-xs font-extrabold text-slate-700 shadow">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                            {{ $package->destination }}
                        </span>
                    </div>
                    @endif

                    @if(!empty($package->label))
                    <div class="absolute top-3 right-3">
                        <span class="inline-flex items-center rounded-full bg-white/90 backdrop-blur border border-white/60 px-3 py-1 text-xs font-extrabold text-slate-900 shadow">
                            {{ $package->label }}
                        </span>
                    </div>
                    @endif
                </div>

                <div class="px-4 pt-4 pb-3">
                    <div class="text-[15px] font-extrabold text-[#0194F3] line-clamp-2">
                        {{ $isEn ? ($package->title_en ?: $package->title) : $package->title }}
                    </div>

                    <div class="mt-2 text-sm">
                        <span class="text-slate-600">{{ $isEn ? 'From ' : 'Mulai ' }}</span>
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

            @empty
            <div class="text-slate-500">
                {{ $isEn ? 'No tour packages yet.' : 'Belum ada paket wisata.' }}
            </div>
            @endforelse
        </div>

        <div class="mt-10 sm:hidden">
            <a href="{{ route('tours.index') }}" class="btn btn-ghost w-full">
                {{ $isEn ? 'View All Packages' : 'Lihat Semua Paket' }}
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
    <div class="max-w-7xl mx-auto px-4 py-14 lg:py-20">
        <div class="text-center max-w-2xl mx-auto" data-aos="fade-up">
            <div class="mx-auto w-fit pill pill-azure">
                <i data-lucide="stars" class="w-4 h-4"></i>
                {{ $siteSettings['home_why_label'] ?? 'Layanan unggulan' }}
            </div>

            <h2 class="mt-4 text-2xl lg:text-3xl font-extrabold text-slate-900">
                {{ $siteSettings['home_why_title'] ?? 'Mengapa Memilih Wisma Indo' }}
            </h2>

            <p class="mt-3 text-slate-600">
                {{ $siteSettings['home_why_desc'] ?? 'Kami berkomitmen memberikan layanan perjalanan yang profesional, transparan, dan berorientasi pada kenyamanan pelanggan.' }}
            </p>
        </div>

        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4" data-aos="fade-up" data-aos-delay="100">
            @php
            $reasons = [
            ['icon'=>'badge-dollar-sign','title'=>($siteSettings['home_why1_title'] ?? 'Harga Transparan'), 'desc'=>($siteSettings['home_why1_desc'] ?? 'Tanpa biaya tersembunyi, semua informasi jelas sejak awal.')],
            ['icon'=>'shield-check','title'=>($siteSettings['home_why2_title'] ?? 'Legal & Terpercaya'), 'desc'=>($siteSettings['home_why2_desc'] ?? 'Dikelola secara profesional dan berpengalaman.')],
            ['icon'=>'zap','title'=>($siteSettings['home_why3_title'] ?? 'Proses Booking Cepat'), 'desc'=>($siteSettings['home_why3_desc'] ?? 'Sistem pemesanan ringkas dan mudah digunakan.')],
            ['icon'=>'headphones','title'=>($siteSettings['home_why4_title'] ?? 'Dukungan Pelanggan'), 'desc'=>($siteSettings['home_why4_desc'] ?? 'Tim siap membantu sebelum dan selama perjalanan.')],
            ];
            @endphp

            @foreach($reasons as $r)
            <div class="card p-6 text-left relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full" style="background: radial-gradient(circle, rgba(1,148,243,0.18), transparent 65%);"></div>

                <div class="flex items-start gap-4 relative">
                    <div class="icon-badge">
                        <i data-lucide="{{ $r['icon'] }}" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="font-extrabold text-slate-900">{{ $r['title'] }}</div>
                        <div class="mt-1.5 text-sm text-slate-600">{{ $r['desc'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</section>


{{-- ================= HOW IT WORKS ================= --}}
<section class="bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 py-14 lg:py-20">
        <div class="text-center max-w-2xl mx-auto" data-aos="fade-up">
            <div class="mx-auto w-fit pill pill-azure">
                <i data-lucide="route" class="w-4 h-4"></i>
                {{ $siteSettings['home_flow_label'] ?? 'Alur mudah' }}
            </div>

            <h2 class="mt-4 text-2xl lg:text-3xl font-extrabold text-slate-900">
                {{ $siteSettings['home_flow_title'] ?? 'Cara Booking yang Rapi & Cepat' }}
            </h2>

            <p class="mt-3 text-slate-600">
                {{ $siteSettings['home_flow_desc'] ?? 'Biar gak buang waktu, alurnya dibuat simple tapi tetap jelas.' }}
            </p>
        </div>

        @php
        $steps = [
        ['no'=>'01','icon'=>'search','title'=>($siteSettings['home_flow1_title'] ?? 'Pilih Paket'),'desc'=>($siteSettings['home_flow1_desc'] ?? 'Cari destinasi, cek detail itinerary, dan sesuaikan kebutuhan.')],
        ['no'=>'02','icon'=>'message-circle','title'=>($siteSettings['home_flow2_title'] ?? 'Konsultasi'),'desc'=>($siteSettings['home_flow2_desc'] ?? 'Tanya jadwal, meeting point, atau request khusus untuk grup.')],
        ['no'=>'03','icon'=>'calendar-check','title'=>($siteSettings['home_flow3_title'] ?? 'Konfirmasi'),'desc'=>($siteSettings['home_flow3_desc'] ?? 'Finalisasi tanggal & data peserta, lalu booking dikunci.')],
        ['no'=>'04','icon'=>'plane','title'=>($siteSettings['home_flow4_title'] ?? 'Berangkat'),'desc'=>($siteSettings['home_flow4_desc'] ?? 'Nikmati perjalanan. Tim support siap bantu selama trip.')],
        ];
        @endphp

        <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-4" data-aos="fade-up" data-aos-delay="120">
            @foreach($steps as $s)
            <div class="card p-6 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full" style="background: radial-gradient(circle, rgba(1,148,243,0.16), transparent 65%);"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="icon-badge">
                            <i data-lucide="{{ $s['icon'] }}" class="w-5 h-5"></i>
                        </div>
                        <div class="text-xs font-extrabold text-slate-500">{{ $s['no'] }}</div>
                    </div>
                    <div class="mt-4 font-extrabold text-slate-900">{{ $s['title'] }}</div>
                    <div class="mt-2 text-sm text-slate-600">{{ $s['desc'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 py-16">

        {{-- Header --}}
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-extrabold mx-auto"
                style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                <i data-lucide="shield-check" class="w-4 h-4" style="color:#0194F3;"></i>
                {{ $siteSettings['home_logos_badge'] ?? 'Kepercayaan pelanggan' }}
            </div>

            <h2 class="mt-4 text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ $siteSettings['home_logos_title'] ?? 'Kepercayaan Pelanggan Wisma Indo' }}
            </h2>

            <p class="mt-2 text-slate-600 max-w-2xl mx-auto">
                {{ $siteSettings['home_logos_desc'] ?? 'Brand dan institusi yang telah mempercayakan perjalanan bersama kami' }}
            </p>

        </div>

        {{-- Logo wall --}}
        <div class="rounded-3xl border border-slate-200 bg-white overflow-hidden">
            <div class="p-6 lg:p-10">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-x-6 gap-y-8">
                    @forelse($clientLogos as $logo)
                    @php
                    $wrapOpen = $logo->url ? '<a href="'.$logo->url.'" target="_blank" rel="noopener" class="group block">' : '<div class="group">';
                            $wrapClose = $logo->url ? '</a>' : '</div>';
                @endphp

                {!! $wrapOpen !!}
                <div class="flex items-center justify-center">
                    <div class="w-full max-w-[170px] h-14 rounded-2xl border border-slate-200 bg-slate-50/60
                            flex items-center justify-center px-5
                            transition group-hover:bg-white group-hover:shadow-sm">
                        <img
                            src="{{ asset('storage/'.$logo->image_path) }}"
                            alt="{{ $logo->name }}"
                            class="h-9 sm:h-10 object-contain opacity-80 transition
                           group-hover:opacity-100"
                            loading="lazy">
                    </div>
                </div>

                <div class="mt-3 text-center text-xs font-semibold text-slate-500 truncate px-2">
                    {{ $logo->name }}
                </div>
                {!! $wrapClose !!}

                @empty
                <div class="col-span-full">
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
                        <div class="mx-auto h-12 w-12 rounded-2xl border border-slate-200 bg-white flex items-center justify-center">
                            <i data-lucide="image" class="w-5 h-5" style="color:#0194F3;"></i>
                        </div>
                        <div class="mt-4 font-extrabold text-slate-900">Belum ada logo pelanggan</div>
                        <p class="mt-1 text-sm text-slate-600">Tambahkan logo dari halaman admin untuk ditampilkan di sini.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        {{-- subtle footer strip --}}
        <div class="h-1 w-full" style="background: linear-gradient(90deg, rgba(1,148,243,.18), rgba(1,148,243,0));"></div>
    </div>

    </div>
</section>


{{-- ================= CTA ================= --}}
<section class="max-w-7xl mx-auto px-4 py-16 text-center">
    <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-8 lg:p-10 shadow-soft">
        <div class="absolute inset-0 travel-dots opacity-60 pointer-events-none"></div>
        <svg class="absolute -top-16 -right-12 w-72 h-72 opacity-60 pointer-events-none" viewBox="0 0 300 300" fill="none" aria-hidden="true">
            <circle cx="150" cy="150" r="120" fill="#0194F3" fill-opacity="0.10" />
            <path d="M70 160c35-45 80-70 130-70 20 0 40 4 60 12" stroke="#0194F3" stroke-opacity="0.35" stroke-width="3" stroke-linecap="round" />
            <path d="M95 205c42-34 78-50 115-50 30 0 55 8 80 19" stroke="#0194F3" stroke-opacity="0.22" stroke-width="3" stroke-linecap="round" />
        </svg>

        <div class="relative">
            <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900">
                {{ $siteSettings['home_final_cta_title'] ?? 'Rencanakan Perjalanan Anda Sekarang' }}
            </h2>
            <p class="mt-3 text-slate-600 max-w-xl mx-auto">
                {{ $siteSettings['home_final_cta_desc'] ?? 'Hubungi tim kami untuk mendapatkan rekomendasi perjalanan terbaik sesuai kebutuhan Anda.' }}
            </p>

            <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ $siteSettings['home_final_cta_primary_url'] ?? route('tours.index') }}" class="btn btn-primary px-8 py-3">
                    <i data-lucide="map" class="w-5 h-5"></i>
                    {{ $siteSettings['home_final_cta_primary_text'] ?? 'Lihat Paket Tour' }}
                </a>
                <a href="{{ $siteSettings['home_final_cta_secondary_url'] ?? '#' }}" class="btn btn-ghost px-8 py-3">
                    <i data-lucide="messages-square" class="w-5 h-5"></i>
                    {{ $siteSettings['home_final_cta_secondary_text'] ?? 'Konsultasi Perjalanan' }}
                </a>
            </div>

        </div>
    </div>
</section>

{{-- ================= PARTNER CTA ================= --}}
<section class="bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 py-12 lg:py-16">
        <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 lg:p-10 shadow-soft" data-aos="fade-up">
            <div class="absolute inset-0 travel-dots opacity-60 pointer-events-none"></div>

            {{-- dekor svg (ngikut pola CTA home yang udah ada) --}}
            <svg class="absolute -top-16 -right-12 w-72 h-72 opacity-70 pointer-events-none" viewBox="0 0 300 300" fill="none" aria-hidden="true">
                <circle cx="150" cy="150" r="120" fill="#0194F3" fill-opacity="0.10" />
                <path d="M70 160c35-45 80-70 130-70 20 0 40 4 60 12" stroke="#0194F3" stroke-opacity="0.35" stroke-width="3" stroke-linecap="round" />
                <path d="M95 205c42-34 78-50 115-50 30 0 55 8 80 19" stroke="#0194F3" stroke-opacity="0.22" stroke-width="3" stroke-linecap="round" />
            </svg>

            <div class="relative grid gap-8 lg:grid-cols-12 lg:items-center">
                {{-- LEFT --}}
                <div class="lg:col-span-5">
                    <div class="pill pill-azure">
                        <i data-lucide="handshake" class="w-4 h-4"></i>
                        {{ $siteSettings['home_partner_badge'] ?? 'Program Partner' }}
                    </div>

                    <h2 class="mt-4 text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ $siteSettings['home_partner_title'] ?? 'Mau jadi Partner Wisma Indo?' }}
                    </h2>

                    <p class="mt-3 text-slate-600 leading-relaxed">
                        {{ $siteSettings['home_partner_desc'] ?? 'Kembangkan jangkauan layanan kamu bersama Wisma Indo. Dapatkan akses dashboard khusus partner untuk kebutuhan operasional.' }}
                    </p>

                    <div class="mt-6">
                        <a href="{{ $siteSettings['home_partner_button_url'] ?? route('partner.register') }}" class="btn btn-primary px-8 py-3">
                            <i data-lucide="user-plus" class="w-5 h-5"></i>
                            {{ $siteSettings['home_partner_button_text'] ?? 'Daftar Partner' }}
                        </a>
                    </div>

                </div>

                {{-- RIGHT --}}
                <div class="lg:col-span-7">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="card p-5">
                            <div class="flex items-center gap-3">
                                <div class="icon-badge">
                                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="text-slate-900 font-extrabold">
                                        {{ $siteSettings['home_partner_card1_title'] ?? 'Dashboard Partner' }}
                                    </div>
                                    <p class="mt-1 text-sm text-slate-600">
                                        {{ $siteSettings['home_partner_card1_desc'] ?? 'Akses halaman khusus partner untuk mengelola kebutuhan operasional.' }}
                                    </p>

                                </div>
                            </div>
                        </div>

                        <div class="card p-5">
                            <div class="flex items-center gap-3">
                                <div class="icon-badge">
                                    <i data-lucide="settings" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="text-slate-900 font-extrabold">
                                        {{ $siteSettings['home_partner_card2_title'] ?? 'Pengaturan Fleksibel' }}
                                    </div>
                                    <p class="mt-1 text-sm text-slate-600">
                                        {{ $siteSettings['home_partner_card2_desc'] ?? ' Data akun partner dan konfigurasi layanan dapat dikelola dengan rapi.' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="card p-5">
                            <div class="flex items-center gap-3">
                                <div class="icon-badge">
                                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="text-slate-900 font-extrabold">
                                        {{ $siteSettings['home_partner_card3_title'] ?? 'Ringkas & Terukur' }}
                                    </div>
                                    <p class="mt-1 text-sm text-slate-600">
                                        {{ $siteSettings['home_partner_card3_desc'] ?? 'Memudahkan pemantauan aktivitas dan pengelolaan kebutuhan harian.' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="card p-5">
                            <div class="flex items-center gap-3">
                                <div class="icon-badge">
                                    <i data-lucide="headphones" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="text-slate-900 font-extrabold">
                                        {{ $siteSettings['home_partner_card4_title'] ?? 'Dukungan Tim' }}
                                    </div>
                                    <p class="mt-1 text-sm text-slate-600">
                                        {{ $siteSettings['home_partner_card4_desc'] ?? 'Tim kami siap membantu untuk kelancaran kerja sama operasional.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 h-1 w-full rounded-full"
                        style="background: linear-gradient(90deg, rgba(1,148,243,.18), rgba(1,148,243,0));"></div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection