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

$features = $isEn ? ($package->features_en ?: $package->features) : $package->features;

$galleryImages = [];

if ($package->thumbnail_path) {
$galleryImages[] = [
'src' => asset('storage/' . $package->thumbnail_path),
'alt' => $title,
'is_thumb' => true,
];
}

$i18n = [
'per_day' => $isEn ? '/ Hour' : '/ Jam',
'package_features' => $isEn ? 'Package Features' : 'Fitur Paket',
'no_features' => $isEn ? 'No features added yet.' : 'Belum ada fitur yang ditambahkan.',
'book_car' => $isEn ? 'Car Booking' : 'Booking Mobil',
'pickup_date' => $isEn ? 'Pickup Date' : 'Tanggal Pickup',
'return_date' => $isEn ? 'Return Date' : 'Tanggal Return',
'total_days' => $isEn ? 'Total Hours' : 'Total Jam',
'total_price' => $isEn ? 'Total Price' : 'Total Harga',
'book_now' => $isEn ? 'Book Now' : 'Booking Sekarang',
'description' => $isEn ? 'Description' : 'Deskripsi',
'high_season_notice' => $isEn
? 'For high season, you must chat admin before booking.'
: 'Untuk high season Wajib Chat admin sebelum Booking',
];
@endphp

@section('title', $seoTitle)
@section('meta_description', $metaDesc)
@section('meta_keywords', $metaKeys)



@section('content')
<section class="max-w-7xl mx-auto px-4 py-10">

  <div class="grid gap-10 lg:grid-cols-3">

    {{-- LEFT CONTENT --}}
    <div class="lg:col-span-2 space-y-8">

      {{-- IMAGE --}}
      <div class="rounded-3xl overflow-hidden shadow-sm border border-slate-200 bg-white">
        @if($package->thumbnail_path)
        <button type="button" class="block w-full" data-lb-open="0">
          <img
            src="{{ asset('storage/' . $package->thumbnail_path) }}"
            alt="{{ $title }}"
            class="w-full h-[360px] object-cover cursor-zoom-in">
        </button>
        @else
        <img
          src="https://via.placeholder.com/1200x600?text=Rent+Car"
          alt="{{ $title }}"
          class="w-full h-[360px] object-cover">
        @endif
      </div>

      {{-- TITLE + PRICE --}}
      <div>
        <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-900">
          {{ $title }}
        </h1>

        <div class="mt-3 flex items-end gap-2">
          <div class="text-3xl font-extrabold text-brand-600">
            Rp{{ number_format($package->price_per_hour, 0, ',', '.') }}
          </div>
          <span class="text-slate-500 text-sm mb-1">{{ $i18n['per_day'] }}</span>
        </div>
      </div>

      {{-- FEATURES --}}
      <section class="bg-white border border-slate-200 rounded-2xl p-6">
        <h2 class="text-lg font-bold text-slate-900 mb-4">{{ $i18n['package_features'] }}</h2>

        @if(!empty($features))
        <ul class="grid sm:grid-cols-2 gap-3 text-sm">
          @foreach ($features as $feat)
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

    </div>

    {{-- RIGHT SIDEBAR — BOOKING --}}
    <aside class="lg:col-span-1">
      <div
        id="rentcarBookingBox"
        data-price-12="{{ (int) $package->price_per_12_hours }}"
        data-price-24="{{ (int) $package->price_per_24_hours }}"
        class="sticky top-24 bg-white border border-slate-200 rounded-2xl shadow-soft p-6">

        <h3 class="text-lg font-extrabold text-slate-900 mb-4">
          {{ $i18n['book_car'] }}
        </h3>

        <form id="bookingForm" onsubmit="return false;" class="space-y-4">
          @csrf

          {{-- PILIHAN DURASI --}}
          <div class="space-y-3">
            <label class="cursor-pointer flex items-center justify-between rounded-xl border border-slate-200 p-4 hover:bg-slate-50 transition duration-option" data-type="12_hours">
              <div class="flex items-center gap-3">
                <input type="radio" name="duration_type" value="12_hours" class="w-4 h-4 text-brand-500 focus:ring-brand-500">
                <div>
                  <div class="font-extrabold text-slate-900">Rental 12 Jam</div>
                </div>
              </div>
              <div class="text-right">
                <div class="font-extrabold text-brand-600 text-lg">Rp {{ number_format($package->price_per_12_hours, 0, ',', '.') }}</div>
                <div class="text-xs text-slate-500">Durasi 12 Jam</div>
              </div>
            </label>

            <label class="cursor-pointer flex items-center justify-between rounded-xl border border-slate-200 p-4 hover:bg-slate-50 transition duration-option" data-type="24_hours">
              <div class="flex items-center gap-3">
                <input type="radio" name="duration_type" value="24_hours" class="w-4 h-4 text-brand-500 focus:ring-brand-500">
                <div>
                  <div class="font-extrabold text-slate-900">Rental 24 Jam</div>
                </div>
              </div>
              <div class="text-right">
                <div class="font-extrabold text-brand-600 text-lg">Rp {{ number_format($package->price_per_24_hours, 0, ',', '.') }}</div>
                <div class="text-xs text-slate-500">Durasi 24 Jam</div>
              </div>
            </label>

            <label class="cursor-pointer flex items-center justify-between rounded-xl border border-slate-200 p-4 hover:bg-slate-50 transition duration-option" data-type="custom">
              <div class="flex items-center gap-3">
                <input type="radio" name="duration_type" value="custom" class="w-4 h-4 text-brand-500 focus:ring-brand-500">
                <div class="font-extrabold text-brand-600">Pilih DURASI Sendiri</div>
              </div>
            </label>
          </div>

          {{-- CUSTOM DATES (Hidden by default) --}}
          <div id="customDatesBox" class="hidden space-y-4 pt-3 border-t border-slate-100">
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1">{{ $i18n['pickup_date'] }}</label>
              <input type="datetime-local" id="pickup"
                class="w-full rounded-xl border-slate-200 focus:ring-brand-500 focus:border-brand-500">
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1">{{ $i18n['return_date'] }}</label>
              <input type="datetime-local" id="return"
                class="w-full rounded-xl border-slate-200 focus:ring-brand-500 focus:border-brand-500">
            </div>

            {{-- SUMMARY FOR CUSTOM --}}
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 text-sm">
              <div class="flex justify-between">
                <span class="text-slate-600">{{ $i18n['total_days'] }}</span>
                <strong id="days">0 Jam</strong>
              </div>
              <div class="flex justify-between mt-2">
                <span class="text-slate-600">{{ $i18n['total_price'] }}</span>
                <strong id="total">Rp0</strong>
              </div>
            </div>
          </div>

          {{-- HIGH SEASON WARNING --}}
          <div class="mt-3 p-4 bg-red-50 border-l-4 border-red-400 rounded text-xs text-red-800 space-y-1">
            <p class="font-semibold">
              {!! $isEn
              ? 'For <span class="uppercase font-extrabold">high season</span>, you must chat admin before booking.'
              : 'Untuk <span class="uppercase font-extrabold">high season</span> Wajib Chat admin sebelum Booking.' !!}
            </p>
          </div>

          <button type="button"
            id="btnBook"
            disabled
            class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 py-3 text-sm font-bold text-white hover:bg-brand-600 disabled:opacity-60 disabled:cursor-not-allowed transition">
            <i data-lucide="shopping-cart" class="w-4 h-4"></i>
            Lanjut Booking
          </button>
        </form>

      </div>
    </aside>

  </div>
  {{-- DESKRIPSI --}}
  @if(!empty($package->long_description))
  <div class="mt-10 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="text-lg font-extrabold text-slate-900 mb-4">{{ $i18n['description'] }}</div>
    <div class="prose max-w-none break-all overflow-hidden">
      @php
      $descToRender = html_entity_decode((string)$descHtml, ENT_QUOTES | ENT_HTML5, 'UTF-8');
      @endphp
      {!! $descToRender !!}
    </div>

  </div>
  @endif

  {{-- REVIEWS --}}
  <div class="mt-14">
    @include('front.partials.reviews', ['item' => $package, 'type' => 'rent'])
  </div>

</section>

{{-- Popup booking modern --}}
@include('front.rentcar.partials.booking-popup', ['package' => $package])

{{-- RENTCAR LIGHTBOX (same pattern as tour) --}}
<div id="rentLb" class="fixed inset-0 z-[9999] hidden" aria-modal="true" role="dialog">
  <div id="rentLbBackdrop" class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>

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
          <span id="rentLbCounter" class="font-semibold"></span>
          <span id="rentLbCaption" class="ml-2 text-white/70"></span>
        </div>
      </div>

      <button id="rentLbClose" type="button"
        class="w-10 h-10 rounded-full bg-white/10 border border-white/15 text-white/90 hover:bg-white/15 flex items-center justify-center">
        <span class="text-2xl leading-none">×</span>
      </button>
    </div>
  </div>

  <div class="absolute inset-0 flex items-center justify-center px-4">
    <div class="relative w-full max-w-5xl">
      <div id="rentLbLoading" class="absolute inset-0 hidden items-center justify-center">
        <div class="w-12 h-12 rounded-full border-4 border-white/30 border-t-white/90 animate-spin"></div>
      </div>

      <img id="rentLbMain"
        class="w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl select-none"
        alt="Preview">

      <button id="rentLbPrev" type="button"
        class="absolute left-2 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 border border-white/15 text-white/90 hover:bg-white/15 flex items-center justify-center">
        ‹
      </button>

      <button id="rentLbNext" type="button"
        class="absolute right-2 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 border border-white/15 text-white/90 hover:bg-white/15 flex items-center justify-center">
        ›
      </button>

      <div id="rentLbThumbs" class="mt-4 flex gap-2 overflow-x-auto"></div>
    </div>
  </div>
</div>

<script>
  (function() {
    const gallery = @json($galleryImages);

    const modal = document.getElementById('rentLb');
    const backdrop = document.getElementById('rentLbBackdrop');
    const btnClose = document.getElementById('rentLbClose');
    const btnPrev = document.getElementById('rentLbPrev');
    const btnNext = document.getElementById('rentLbNext');
    const mainImg = document.getElementById('rentLbMain');
    const thumbs = document.getElementById('rentLbThumbs');
    const counter = document.getElementById('rentLbCounter');
    const caption = document.getElementById('rentLbCaption');
    const loading = document.getElementById('rentLbLoading');

    if (!modal || !mainImg || !Array.isArray(gallery) || gallery.length === 0) return;

    let index = 0;

    function show() {
      modal.classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
    }

    function hide() {
      modal.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }

    function setCounter() {
      if (!counter) return;
      counter.textContent = `${index + 1} / ${gallery.length}`;
      const cap = gallery[index]?.alt || '';
      if (caption) {
        caption.textContent = cap;
        caption.classList.toggle('hidden', !cap);
      }
    }

    function setActiveThumb() {
      if (!thumbs) return;
      thumbs.querySelectorAll('img[data-thumb]').forEach((img) => {
        const i = parseInt(img.getAttribute('data-thumb'), 10);
        img.classList.toggle('ring-2', i === index);
        img.classList.toggle('ring-white/70', i === index);
        img.classList.toggle('opacity-70', i !== index);
      });
    }

    function renderThumbs() {
      if (!thumbs) return;
      thumbs.innerHTML = '';

      gallery.forEach((g, i) => {
        const img = document.createElement('img');
        img.src = g.src;
        img.alt = g.alt || 'thumb';
        img.setAttribute('data-thumb', String(i));
        img.className = 'h-16 w-24 object-cover rounded-lg cursor-pointer ring-offset-2 ring-offset-black/70';
        img.addEventListener('click', () => open(i));
        thumbs.appendChild(img);
      });
    }

    function open(i) {
      if (!gallery.length) return;

      index = Math.max(0, Math.min(i, gallery.length - 1));

      if (loading) {
        loading.classList.remove('hidden');
        loading.classList.add('flex');
      }

      const src = gallery[index]?.src;
      const alt = gallery[index]?.alt || '';

      const img = new Image();
      img.onload = () => {
        mainImg.src = src;
        mainImg.alt = alt;
        if (loading) {
          loading.classList.add('hidden');
          loading.classList.remove('flex');
        }
        setCounter();
        setActiveThumb();
        show();
      };
      img.onerror = () => {
        if (loading) {
          loading.classList.add('hidden');
          loading.classList.remove('flex');
        }
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


<script>
  document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('input[name="duration_type"]');
    const customDatesBox = document.getElementById('customDatesBox');
    const pickup = document.getElementById('pickup');
    const ret = document.getElementById('return');
    const daysEl = document.getElementById('days');
    const totalEl = document.getElementById('total');
    const btnBook = document.getElementById('btnBook');
    const box = document.getElementById('rentcarBookingBox');
    
    const price12 = box ? parseInt(box.dataset.price12 || '0', 10) : 0;
    const price24 = box ? parseInt(box.dataset.price24 || '0', 10) : 0;
    
    // Perhitungan per jam berdasar harga 12 jam
    const pricePerHour = Math.round(price12 / 12);

    let currentDurationType = null;
    let finalCustomHours = 0;
    let finalCustomPrice = 0;

    function resetCustomStyles() {
      document.querySelectorAll('.duration-option').forEach(el => {
        el.classList.remove('bg-blue-50', 'border-blue-500');
        el.classList.add('bg-white', 'border-slate-200');
      });
    }

    radios.forEach(radio => {
      radio.addEventListener('change', function() {
        currentDurationType = this.value;
        resetCustomStyles();
        this.closest('.duration-option').classList.remove('bg-white', 'border-slate-200');
        this.closest('.duration-option').classList.add('bg-blue-50', 'border-blue-500');

        if (this.value === 'custom') {
          customDatesBox.classList.remove('hidden');
          recalcCustom();
        } else {
          customDatesBox.classList.add('hidden');
          btnBook.disabled = false;
        }
      });
    });

    function recalcCustom() {
      if (currentDurationType !== 'custom') return;

      if (!pickup.value || !ret.value) {
        daysEl.textContent = '0 Jam';
        totalEl.textContent = 'Rp0';
        btnBook.disabled = true;
        return;
      }

      const start = new Date(pickup.value);
      const end = new Date(ret.value);

      if (end <= start) {
        daysEl.textContent = '0 Jam';
        totalEl.textContent = 'Rp0';
        btnBook.disabled = true;
        return;
      }

      let diffHours = Math.ceil((end - start) / (1000 * 60 * 60));
      // Minimal 12 jam
      if (diffHours < 12) {
        diffHours = 12;
      }

      finalCustomHours = diffHours;
      finalCustomPrice = diffHours * pricePerHour;

      daysEl.textContent = diffHours + ' Jam';
      totalEl.textContent = 'Rp' + finalCustomPrice.toLocaleString('id-ID');
      btnBook.disabled = false;
    }

    if(pickup) pickup.addEventListener('change', recalcCustom);
    if(ret) ret.addEventListener('change', recalcCustom);

    if(btnBook) {
      btnBook.addEventListener('click', function() {
        window.dispatchEvent(
          new CustomEvent('open-rentcar-booking', {
            detail: {
              durationType: currentDurationType,
              pickup: pickup ? pickup.value : '',
              ret: ret ? ret.value : '',
              customHours: finalCustomHours,
              customPrice: finalCustomPrice,
              price12: price12,
              price24: price24
            },
          })
        );
      });
    }
  });
</script>

@endsection