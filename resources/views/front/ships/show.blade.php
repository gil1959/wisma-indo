@extends('layouts.front')

@php
$isEn = app()->getLocale() === 'en';

$title = $isEn ? ($package->title_en ?: $package->title) : $package->title;

$seoTitle = $isEn
? ($package->seo_title_en ?: $package->seo_title ?: $title)
: ($package->seo_title ?: $title);

$descHtml = $isEn
? ($package->long_description_en ?: $package->long_description)
: $package->long_description;

$metaDesc = $isEn
? ($package->seo_description_en ?: $package->seo_description ?: \Illuminate\Support\Str::limit(trim(strip_tags($descHtml ?? '')), 160))
: ($package->seo_description ?: \Illuminate\Support\Str::limit(trim(strip_tags($descHtml ?? '')), 160));

$metaKeys = $isEn
? ($package->seo_keywords_en ?: ($package->seo_keywords ?? ''))
: ($package->seo_keywords ?? '');

$i18n = [
'reviews' => $isEn ? 'reviews' : 'ulasan',
'package_features' => $isEn ? 'Package Features' : 'Fitur Paket',
'no_features' => $isEn ? 'No features added yet.' : 'Belum ada fitur yang ditambahkan.',
'description' => $isEn ? 'Description' : 'Deskripsi',
];

// ==== Gallery data for lightbox ====
$galleryImages = [];

$thumbSrc = $package->thumbnail_path
? asset('storage/' . $package->thumbnail_path)
: 'https://via.placeholder.com/1200x600?text=Sewa+Kapal';

$galleryImages[] = [
'src' => $thumbSrc,
'alt' => $title,
'is_thumb' => true,
];

@endphp

@section('title', $seoTitle)
@section('meta_description', $metaDesc)
@section('meta_keywords', $metaKeys)


@section('content')
<div
  x-data='{
    active: "weekday",
    tiers: {
      weekday: @json($package->tiers->where("type","weekday")->values()),
      weekend: @json($package->tiers->where("type","weekend")->values())
    },

    // selection per tab
    selected: { weekday: null, weekend: null },

    // always reflect selection for active tab
    get selectedTier() { return this.selected[this.active]; },

    selectTier(tier) { this.selected[this.active] = tier; }
  }'
  class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-3 gap-8">

  <div class="md:col-span-2 space-y-8">
    <div class="rounded-3xl overflow-hidden shadow-sm border border-slate-200 bg-white">
      <button type="button" class="block w-full" data-lb-open="0">
        <img
          src="{{ $package->thumbnail_path ? asset('storage/'.$package->thumbnail_path) : 'https://via.placeholder.com/1200x600?text=Sewa+Kapal' }}"
          alt="{{ $title }}"
          class="w-full h-[360px] object-cover">
      </button>
    </div>

    <div>
      <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-900">{{ $title }}</h1>

      <div class="mt-2 flex items-center gap-2 text-sm text-slate-700">
        @php
        $avg = (float)($package->rating_value ?? 5);
        $count = (int)($package->rating_count ?? 0);
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
        <span class="text-slate-500"> {{ $count }} {{ $i18n['reviews'] }}</span>
      </div>
    </div>

    <section class="bg-white border border-slate-200 rounded-2xl p-6">
      <h2 class="text-lg font-bold text-slate-900 mb-4">{{ $i18n['package_features'] }}</h2>

      @if(!empty($package->features))
      <ul class="grid sm:grid-cols-2 gap-3 text-sm">
        @foreach($package->features as $feat)
        <li class="flex items-center gap-2">
          @if(!empty($feat['available']))
          <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
          @else
          <i data-lucide="x-circle" class="w-4 h-4 text-red-400"></i>
          @endif
          <span class="text-slate-700">{{ $feat['name'] ?? '-' }}</span>
        </li>
        @endforeach
      </ul>
      @else
      <div class="text-sm text-slate-500">{{ $i18n['no_features'] }}</div>
      @endif
    </section>

    @if(!empty($descHtml))
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="text-lg font-extrabold text-slate-900 mb-4">{{ $i18n['description'] }}</div>
      <div class="prose max-w-none break-words">{!! $descHtml !!}</div>

    </div>
    @endif




  </div>

  {{-- RESERVASI (DESKTOP: sidebar kanan) --}}

  <aside class="md:col-span-1 space-y-6">
    @include('front.ships.partials.reservation')
  </aside>
  <section class="md:col-span-2 bg-white rounded-xl shadow-sm p-5">
    @include('front.partials.reviews', ['item' => $package, 'type' => 'ship'])
  </section>


  @include('front.ships.partials.booking-popup', ['package' => $package])

</div>

<div id="proLb" class="fixed inset-0 z-[9999] hidden" aria-modal="true" role="dialog">
  {{-- Backdrop --}}
  <div id="proLbBackdrop" class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>

  {{-- Top Bar --}}
  <div class="absolute top-0 left-0 right-0 z-10">
    <div class="mx-auto max-w-7xl px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-2 text-white/90 text-sm">
        <span id="proLbCounter" class="font-semibold">1 / 1</span>
        <span id="proLbCaption" class="hidden text-white/70"></span>
      </div>

      <button id="proLbClose" type="button"
        class="rounded-full bg-white/10 hover:bg-white/20 text-white px-3 py-2 text-sm transition">
        ✕
      </button>
    </div>
  </div>

  {{-- Controls --}}
  <button id="proLbPrev" type="button"
    class="absolute left-3 top-1/2 -translate-y-1/2 z-10 rounded-full bg-white/10 hover:bg-white/20 text-white w-11 h-11 grid place-items-center transition">
    ‹
  </button>
  <button id="proLbNext" type="button"
    class="absolute right-3 top-1/2 -translate-y-1/2 z-10 rounded-full bg-white/10 hover:bg-white/20 text-white w-11 h-11 grid place-items-center transition">
    ›
  </button>

  {{-- Main --}}
  <div class="relative z-0 w-full h-full flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-6xl">
      {{-- Loading --}}
      <div id="proLbLoading" class="hidden absolute inset-0 grid place-items-center">
        <div class="rounded-xl bg-black/40 px-4 py-3 text-white text-sm">Loading…</div>
      </div>

      <div class="rounded-2xl overflow-hidden bg-black/40 border border-white/10 shadow-2xl">
        <img id="proLbMain"
          src=""
          alt="Preview"
          class="w-full max-h-[78vh] object-contain bg-black select-none"
          draggable="false" />
      </div>

      {{-- Thumbnails --}}
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