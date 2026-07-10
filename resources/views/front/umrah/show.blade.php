@extends('layouts.front')

@php
$isEn = app()->getLocale() === 'en';

$title = $isEn ? (($package->title_en ?? null) ?: ($package->title ?? '')) : ($package->title ?? '');

$descHtml = $isEn
? (($package->long_description_en ?? null) ?: ($package->long_description ?? null))
: ($package->long_description ?? null);

$itineraryHtml = $isEn
? (($package->itinerary_en ?? null) ?: ($package->itinerary ?? null))
: ($package->itinerary ?? null);

$includeHtml = $isEn
? (($package->include_text_en ?? null) ?: ($package->include_text ?? null))
: ($package->include_text ?? null);

$excludeHtml = $isEn
? (($package->exclude_text_en ?? null) ?: ($package->exclude_text ?? null))
: ($package->exclude_text ?? null);

$seoTitle = $isEn
? (($package->seo_title_en ?? null) ?: ($package->seo_title ?? null) ?: $title)
: (($package->seo_title ?? null) ?: $title);

$metaDesc = $isEn
? (($package->seo_description_en ?? null) ?: ($package->seo_description ?? null) ?: \Illuminate\Support\Str::limit(trim(strip_tags($descHtml ?? '')), 160))
: (($package->seo_description ?? null) ?: \Illuminate\Support\Str::limit(trim(strip_tags($descHtml ?? '')), 160));

$metaKeys = $isEn
? (($package->seo_keywords_en ?? null) ?: ($package->seo_keywords ?? ''))
: ($package->seo_keywords ?? '');

$i18n = [
'about' => $isEn ? 'About this package' : 'Tentang Paket',
'itinerary' => $isEn ? 'Trip Itinerary' : 'Itinerary Perjalanan',
'include' => $isEn ? 'Included' : 'Termasuk (Include)',
'exclude' => $isEn ? 'Excluded' : 'Tidak Termasuk (Exclude)',

// sidebar reservation
'reservation_title' => $isEn ? 'Reservation' : 'Reservasi Paket',
'continue_booking' => $isEn ? 'Continue Booking' : 'Lanjut Booking',
'no_price' => $isEn ? 'No price available for this package.' : 'Belum ada harga untuk paket ini.',
'per_pax' => $isEn ? '/ pax' : '/ pax', // boleh tetep, mata uang tetap IDR
'domestic' => $isEn ? 'Domestic' : 'Domestik',
];

$title = $package->title; // (ngikut Umrah show sekarang, belum multi-bahasa)
$galleryImages = [];

if ($package->thumbnail_path) {
$galleryImages[] = [
'src' => asset('storage/' . $package->thumbnail_path),
'alt' => $title,
'is_thumb' => true,
];
}

foreach ($package->photos as $photo) {
$galleryImages[] = [
'src' => asset('storage/' . $photo->file_path),
'alt' => 'Gallery photo',
'is_thumb' => false,
];
}
@endphp

@section('title', $seoTitle)
@section('meta_description', $metaDesc)
@section('meta_keywords', $metaKeys)


@section('content')

<div
    x-data='{
        tiers: @json($package->tiers->values()),
        selectedTier: null
    }'
    class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-3 gap-8">

    {{-- =============== LEFT CONTENT =============== --}}
    <div class="md:col-span-2 space-y-8">

        {{-- THUMBNAIL --}}
        @if($package->thumbnail_path)
        <div class="-mt-6 -mx-4 md:mx-0">
            <button type="button" class="block w-full" data-lb-open="0">
                <img
                    src="{{ asset('storage/' . $package->thumbnail_path) }}"
                    class="w-full h-64 md:h-80 object-cover rounded-2xl shadow-md hover:opacity-95 transition"
                    alt="{{ $title }}">
            </button>
        </div>
        @endif


        {{-- GALLERY / FOTO TAMBAHAN --}}
        @if($package->photos->count())
        <section class="mt-2">
            <div class="grid grid-cols-3 gap-3">
                @foreach($package->photos as $idx => $photo)
                @php $openIndex = ($package->thumbnail_path ? 1 : 0) + $idx; @endphp
                <button type="button" class="block" data-lb-open="{{ $openIndex }}">
                    <img
                        src="{{ asset('storage/' . $photo->file_path) }}"
                        class="w-full h-28 md:h-32 object-cover rounded-xl shadow hover:opacity-90 transition"
                        alt="Gallery photo"
                        loading="lazy">
                </button>
                @endforeach
            </div>
        </section>
        @endif


        {{-- TITLE + META --}}
        <div class="mt-2">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900">{{ $title }}</h1>


            <div class="mt-2 flex items-center gap-2 text-sm text-slate-700">
                @php
                $avg = (float) ($package->rating_value ?? 5);
                $count = (int) ($package->rating_count ?? 0);
                $rounded = (int) round($avg);
                @endphp

                <div class="flex gap-1">
                    @for($i=1;$i<=5;$i++)
                        <svg class="w-4 h-4 {{ $i <= $rounded ? 'text-amber-400' : 'text-slate-300' }}"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.156c.969 0 1.371 1.24.588 1.81l-3.363 2.444a1 1 0 00-.364 1.118l1.286 3.955c.3.921-.755 1.688-1.54 1.118l-3.363-2.444a1 1 0 00-1.175 0L6.98 18.007c-.784.57-1.838-.197-1.539-1.118l1.286-3.955a1 1 0 00-.364-1.118L3 9.382c-.783-.57-.38-1.81.588-1.81h4.156a1 1 0 00.95-.69l1.286-3.955z" />
                        </svg>
                        @endfor
                </div>

                <span class="font-semibold">{{ number_format($avg, 1) }}/5</span>
                <span class="text-slate-500">· {{ $count }} ulasan</span>
            </div>

            <div class="mt-2 text-sm text-gray-500 flex flex-wrap items-center gap-4">
                @if($package->duration_text)
                <span class="flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4 text-gray-500"></i>
                    <span>{{ $package->duration_text }}</span>
                </span>
                @endif

                @if($package->destination)
                <span class="flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4 text-gray-500"></i>
                    <span>{{ $package->destination }}</span>
                </span>
                @endif
            </div>
        </div>

        {{-- DESCRIPTION --}}
        @if($descHtml)
        <section class="bg-white rounded-xl shadow-sm p-5">
            <h2 class="text-lg font-semibold mb-3 text-[#0194F3]">{{ $i18n['about'] }}</h2>
            <div class="text-sm leading-relaxed text-gray-700">
                {!! $descHtml !!}
            </div>
        </section>
        @endif

        {{-- ITINERARY (UMRAH: WYSIWYG HTML) --}}
        @if($itineraryHtml)
        <section class="bg-white rounded-xl shadow-sm p-5">
            <h2 class="text-lg font-semibold mb-4 text-[#0194F3] flex items-center gap-2">
                <i data-lucide="map" class="w-5 h-5 text-[#0194F3]"></i>
                <span>{{ $i18n['itinerary'] }}</span>
            </h2>

            <div class="text-sm leading-relaxed text-gray-700">
                {!! $itineraryHtml !!}
            </div>
        </section>
        @endif


        {{-- INCLUDE / EXCLUDE (UMRAH: WYSIWYG HTML) --}}
        <div class="grid md:grid-cols-2 gap-4">
            @if($includeHtml)
            <section class="bg-green-50 border border-green-200 rounded-xl p-5">
                <h2 class="text-lg font-semibold text-green-700 mb-3 flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                    {{ $i18n['include'] }}

                </h2>
                <div class="text-sm text-gray-700 leading-relaxed">
                    {!! $includeHtml !!}
                </div>
            </section>
            @endif

            @if($excludeHtml)
            <section class="bg-red-50 border border-red-200 rounded-xl p-5">
                <h2 class="text-lg font-semibold text-red-700 mb-3 flex items-center gap-2">
                    <i data-lucide="x-circle" class="w-5 h-5 text-red-600"></i>
                    {{ $i18n['exclude'] }}
                </h2>
                <div class="text-sm text-gray-700 leading-relaxed">
                    {!! $excludeHtml !!}
                </div>
            </section>
            @endif
        </div>

    </div>

    {{-- =============== SIDEBAR RESERVATION ONLY =============== --}}
    <aside class="md:col-span-1 space-y-6">
        @include('front.umrah.partials.reservation')
    </aside>

    {{-- =============== REVIEWS =============== --}}
    <section class="md:col-span-2 bg-white rounded-xl shadow-sm p-5">
        @include('front.partials.reviews', ['item' => $package, 'type' => 'umrah'])
    </section>

    {{-- =============== POPUP BOOKING =============== --}}
    @include('front.umrah.partials.booking-popup')

</div>

{{-- PRO LIGHTBOX (Vanilla JS, same as Tours) --}}
<div id="proLb" class="fixed inset-0 z-[9999] hidden" aria-modal="true" role="dialog">
    <div id="proLbBackdrop" class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>

    <div class="absolute top-0 left-0 right-0 z-10">
        <div class="mx-auto max-w-7xl px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-white/10 border border-white/15 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white/85" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                        <path d="M3 16l5-5 4 4 3-3 6 6"></path>
                        <path d="M14 7h.01"></path>
                    </svg>
                </div>
                <div class="text-white/90 text-sm">
                    <span id="proLbCounter" class="font-semibold">1 / 1</span>
                    <span id="proLbCaption" class="ml-2 text-white/70 hidden sm:inline"></span>
                </div>
            </div>

            <button id="proLbClose" type="button"
                class="group inline-flex items-center gap-2 rounded-full bg-white/10 hover:bg-white/15 border border-white/15 px-4 py-2 text-white/90 text-sm transition">
                <span class="hidden sm:inline">Close</span>
                <svg class="w-4 h-4 text-white/90 group-hover:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6L6 18"></path>
                    <path d="M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <div class="absolute inset-0 flex items-center justify-center px-4">
        <div class="relative w-full max-w-6xl">
            <button id="proLbPrev" type="button"
                class="absolute -left-2 md:-left-10 top-1/2 -translate-y-1/2 z-10
                       w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 border border-white/15
                       flex items-center justify-center text-white/90 transition">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 18l-6-6 6-6"></path>
                </svg>
            </button>

            <button id="proLbNext" type="button"
                class="absolute -right-2 md:-right-10 top-1/2 -translate-y-1/2 z-10
                       w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 border border-white/15
                       flex items-center justify-center text-white/90 transition">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 18l6-6-6-6"></path>
                </svg>
            </button>

            <div class="relative overflow-hidden rounded-2xl border border-white/10 bg-black/40 shadow-2xl">
                <div id="proLbLoading" class="absolute inset-0 flex items-center justify-center">
                    <div class="w-10 h-10 rounded-full border-2 border-white/30 border-t-white/80 animate-spin"></div>
                </div>

                <img id="proLbMain"
                    src=""
                    alt="Preview"
                    class="w-full max-h-[78vh] object-contain bg-black select-none"
                    draggable="false" />
            </div>

            <div class="mt-3">
                <div id="proLbThumbs"
                    class="flex gap-2 overflow-x-auto pb-2 [-ms-overflow-style:none] [scrollbar-width:none]">
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    (function() {
        const gallery = @json($galleryImages);

        const modal = document.getElementById('proLb');
        const backdrop = document.getElementById('proLbBackdrop');
        const btnClose = document.getElementById('proLbClose');
        const btnPrev = document.getElementById('proLbPrev');
        const btnNext = document.getElementById('proLbNext');
        const mainImg = document.getElementById('proLbMain');
        const thumbs = document.getElementById('proLbThumbs');
        const counter = document.getElementById('proLbCounter');
        const caption = document.getElementById('proLbCaption');
        const loading = document.getElementById('proLbLoading');

        if (!modal || !mainImg || !Array.isArray(gallery)) return;

        let index = 0;

        function setCounter() {
            counter.textContent = `${index + 1} / ${gallery.length}`;
            const cap = gallery[index]?.alt || '';
            caption.textContent = cap;
            caption.classList.toggle('hidden', !cap);
        }

        function setActiveThumb() {
            thumbs.querySelectorAll('img[data-thumb]').forEach((img) => {
                const i = parseInt(img.getAttribute('data-thumb'), 10);
                img.classList.toggle('ring-2', i === index);
                img.classList.toggle('ring-white/80', i === index);
                img.classList.toggle('opacity-100', i === index);
                img.classList.toggle('opacity-70', i !== index);
            });
        }

        function renderThumbs() {
            thumbs.innerHTML = '';
            gallery.forEach((g, i) => {
                const b = document.createElement('button');
                b.type = 'button';
                b.className = 'shrink-0 rounded-lg overflow-hidden border border-white/10 hover:border-white/25 transition';
                b.addEventListener('click', () => open(i));

                const im = document.createElement('img');
                im.src = g.src;
                im.alt = 'thumb';
                im.setAttribute('data-thumb', String(i));
                im.className = 'w-16 h-12 object-cover opacity-70 hover:opacity-100 transition';
                im.loading = 'lazy';

                b.appendChild(im);
                thumbs.appendChild(b);
            });
        }

        function show() {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function hide() {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function open(i) {
            if (!gallery.length) return;
            index = (i + gallery.length) % gallery.length;

            show();
            loading.classList.remove('hidden');

            const src = gallery[index].src;
            const img = new Image();
            img.onload = () => {
                mainImg.src = src;
                mainImg.alt = gallery[index]?.alt || 'Preview';
                loading.classList.add('hidden');
                setCounter();
                setActiveThumb();
            };
            img.onerror = () => {
                mainImg.src = src;
                loading.classList.add('hidden');
                setCounter();
                setActiveThumb();
            };
            img.src = src;
        }

        function next() {
            open(index + 1);
        }

        function prev() {
            open(index - 1);
        }

        renderThumbs();

        document.querySelectorAll('[data-lb-open]').forEach(el => {
            el.addEventListener('click', () => {
                const i = parseInt(el.getAttribute('data-lb-open'), 10);
                open(Number.isFinite(i) ? i : 0);
            });
        });

        btnClose?.addEventListener('click', hide);
        backdrop?.addEventListener('click', hide);
        btnNext?.addEventListener('click', next);
        btnPrev?.addEventListener('click', prev);

        window.addEventListener('keydown', (e) => {
            if (modal.classList.contains('hidden')) return;
            if (e.key === 'Escape') hide();
            if (e.key === 'ArrowRight') next();
            if (e.key === 'ArrowLeft') prev();
        });
    })();
</script>
@endsection