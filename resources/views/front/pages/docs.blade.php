@extends('layouts.front')
@php $isEn = app()->getLocale() === 'en'; @endphp

@section('title', (($pageTitle ?? 'Dokumentasi') . ' - Wisma Indo'))

@section('content')

{{-- HEADER --}}
<section class="relative overflow-hidden bg-white">
  <div class="absolute inset-0 travel-grid opacity-70"></div>

  {{-- Decorative travel route --}}
  <svg class="absolute -top-20 -right-20 w-[560px] h-[560px] opacity-80" viewBox="0 0 600 600" fill="none" aria-hidden="true">
    <defs>
      <radialGradient id="docsGlow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(320 280) rotate(90) scale(290)">
        <stop stop-color="#0194F3" stop-opacity="0.20" />
        <stop offset="1" stop-color="#0194F3" stop-opacity="0" />
      </radialGradient>
    </defs>
    <circle cx="320" cy="280" r="290" fill="url(#docsGlow)" />
    <path d="M135 330c70-95 170-155 285-155 45 0 88 8 130 22" stroke="#0194F3" stroke-opacity="0.22" stroke-width="2" stroke-linecap="round" />
    <path d="M165 390c85-70 162-105 248-105 70 0 128 18 175 44" stroke="#0194F3" stroke-opacity="0.16" stroke-width="2" stroke-linecap="round" />
    <circle cx="155" cy="402" r="9" fill="#0194F3" fill-opacity="0.30" />
    <circle cx="535" cy="204" r="9" fill="#0194F3" fill-opacity="0.30" />
  </svg>

  <div class="relative max-w-7xl mx-auto px-4 pt-10 pb-10 lg:pt-14 lg:pb-12">
    <div class="grid gap-8 lg:grid-cols-12 lg:items-end" data-aos="fade-up">

      <div class="lg:col-span-8 max-w-3xl">
        <div class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-extrabold"
          style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
          <span class="h-2 w-2 rounded-full" style="background:#0194F3;"></span>
          {{ $heroBadge ?? ($siteSettings['docs_hero_badge'] ?? 'Dokumentasi Perjalanan') }}
        </div>

        <h1 class="mt-4 text-3xl lg:text-4xl font-extrabold text-slate-900">
          {{ $heroTitle ?? ($siteSettings['docs_hero_title'] ?? 'Dokumentasi') }}
        </h1>

        <p class="mt-3 text-slate-600">
          {{ $heroDesc ?? ($siteSettings['docs_hero_desc'] ?? 'Galeri dokumentasi perjalanan dan aktivitas layanan kami, terdiri dari foto dan video.') }}
        </p>
      </div>

      {{-- Quick stats --}}
      <div class="lg:col-span-4">
        <div class="grid grid-cols-2 gap-3 lg:justify-end">
          <div class="card p-4">
            <div class="flex items-center gap-3">
              <div class="icon-badge"><i data-lucide="image" class="w-5 h-5"></i></div>
              <div>
                <div class="text-xs text-slate-500 font-semibold">
                  {{ $siteSettings['docs_stat_photos'] ?? ($isEn ? 'Total Photos' : 'Total Foto') }}
                </div>
                <div class="text-lg font-extrabold text-slate-900">{{ $photos->count() }}</div>
              </div>
            </div>
          </div>
          <div class="card p-4">
            <div class="flex items-center gap-3">
              <div class="icon-badge"><i data-lucide="video" class="w-5 h-5"></i></div>
              <div>
                <div class="text-xs text-slate-500 font-semibold">
                  {{ $siteSettings['docs_stat_videos'] ?? ($isEn ? 'Total Videos' : 'Total Video') }}
                </div>
                <div class="text-lg font-extrabold text-slate-900">{{ $videos->count() }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    {{-- IMPORTANT: Alpine wrapper harus meliputi Tabs + Gallery --}}
    <div
      x-data="{
        ...lightboxGallery(),
        tab: (window.location.hash === '#video' ? 'videos' : 'photos')
      }"
      x-init="
        init();
        window.addEventListener('hashchange', () => {
          tab = (window.location.hash === '#video' ? 'videos' : 'photos');
        });
      ">

      {{-- Tabs --}}
      <div class="mt-8" data-aos="fade-up" data-aos-delay="120">
        <div class="inline-flex rounded-2xl border border-slate-200 bg-white p-1 shadow-sm">
          <button type="button"
            class="px-4 py-2 rounded-xl text-sm font-extrabold transition inline-flex items-center gap-2"
            :class="tab==='photos' ? 'text-slate-900' : 'text-slate-600 hover:text-slate-900'"
            @click="tab='photos'; window.location.hash='photos'"
            :style="tab==='photos' ? 'background:rgba(1,148,243,0.10); border:1px solid rgba(1,148,243,0.20);' : ''">
            <i data-lucide="image" class="w-4 h-4" :style="tab==='photos' ? 'color:#0194F3;' : ''"></i>
            {{ $siteSettings['docs_tab_photos'] ?? ($isEn ? 'Photos' : 'Foto') }}
          </button>

          <button type="button"
            class="px-4 py-2 rounded-xl text-sm font-extrabold transition inline-flex items-center gap-2"
            :class="tab==='videos' ? 'text-slate-900' : 'text-slate-600 hover:text-slate-900'"
            @click="tab='videos'; window.location.hash='video'"
            :style="tab==='videos' ? 'background:rgba(1,148,243,0.10); border:1px solid rgba(1,148,243,0.20);' : ''">
            <i data-lucide="video" class="w-4 h-4" :style="tab==='videos' ? 'color:#0194F3;' : ''"></i>
            {{ $siteSettings['docs_tab_videos'] ?? ($isEn ? 'Videos' : 'Video') }}
          </button>
        </div>

        <p class="mt-3 text-sm text-slate-500">
          {{ $siteSettings['docs_hint'] ?? ($isEn
    ? 'Use the tabs to navigate the documentation. Content remains fully loaded.'
    : 'Gunakan tab untuk menavigasi dokumentasi. Konten tetap dimuat lengkap.') }}

        </p>
      </div>

      {{-- divider --}}
      <svg class="block w-full" viewBox="0 0 1440 90" fill="none" aria-hidden="true">
        <path d="M0 35C180 80 360 80 540 50C720 20 900 20 1080 50C1260 80 1350 76 1440 56V90H0V35Z" fill="#F8FAFC" />
      </svg>

      {{-- GALLERY --}}
      <section class="max-w-7xl mx-auto px-4 py-10 space-y-14">

        {{-- ================= PHOTOS ================= --}}
        <div data-aos="fade-up" x-show="tab==='photos'" x-cloak>
          <div class="flex items-end justify-between gap-4">
            <div>
              <div class="inline-flex items-center gap-2 text-xs font-extrabold" style="color:#055a93;">
                <i data-lucide="camera" class="w-4 h-4" style="color:#0194F3;"></i>
                {{ $isEn ? 'PHOTO GALLERY' : 'GALERI FOTO' }}
              </div>
              <h2 class="mt-2 text-xl lg:text-2xl font-extrabold text-slate-900">
                {{ $isEn ? 'Photos' : 'Foto' }}
              </h2>
              <p class="mt-1 text-slate-600 text-sm">
                {{ $isEn ? 'A curated collection of documentation photos.' : 'Kumpulan foto dokumentasi pilihan.' }}
              </p>

            </div>

            <div class="hidden sm:flex items-center gap-2">
              <span class="pill pill-azure">
                <i data-lucide="images" class="w-4 h-4"></i>
                {{ $photos->count() }} item
              </span>
            </div>
          </div>

          @if($photos->count())
          <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($photos as $p)
            <button
              type="button"
              @click="openLightbox('{{ $p->url }}')"
              class="group rounded-2xl overflow-hidden border border-slate-200 bg-white shadow-sm hover:shadow-md transition text-left w-full">
              <div class="relative h-44 bg-slate-100 overflow-hidden">
                <img
                  src="{{ $p->url }}"
                  data-lightbox="{{ $p->url }}"
                  loading="lazy"
                  @php
                  $pTitle=$isEn ? ($p->title_en ?: $p->title) : $p->title;
                $pTitle = $pTitle ?: ($isEn ? 'Photo Documentation' : 'Dokumentasi Foto');
                @endphp

                alt="{{ $pTitle }}"
                class="h-full w-full object-cover group-hover:scale-105 transition duration-500">

                {{-- hover overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/55 via-slate-950/15 to-transparent opacity-0 group-hover:opacity-100 transition"></div>

                {{-- top badge --}}
                <div class="absolute top-3 left-3 inline-flex items-center gap-2 rounded-full bg-white/92 border border-slate-200 px-3 py-1 text-xs font-extrabold text-slate-700 shadow">
                  <i data-lucide="map-pin" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                  Dokumentasi
                </div>

                {{-- zoom icon --}}
                <div class="absolute bottom-3 right-3 h-10 w-10 rounded-2xl border border-white/20 bg-white/10 backdrop-blur grid place-items-center opacity-0 group-hover:opacity-100 transition">
                  <i data-lucide="zoom-in" class="w-5 h-5 text-white"></i>
                </div>
              </div>

              <div class="p-4">
                <div class="text-sm font-extrabold text-slate-900 line-clamp-1">
                  {{ $pTitle }}
                </div>
              </div>
            </button>
            @endforeach
          </div>
          @else
          <div class="mt-6 card p-10 text-center text-slate-600">
            <div class="mx-auto h-14 w-14 rounded-2xl border grid place-items-center"
              style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22);">
              <i data-lucide="image-off" class="w-7 h-7" style="color:#0194F3;"></i>
            </div>
            <div class="mt-4 font-extrabold text-slate-900">Belum ada foto</div>
            <p class="mt-2 text-sm text-slate-600">Dokumentasi foto belum tersedia saat ini.</p>
          </div>
          @endif
        </div>

        {{-- ================= VIDEOS ================= --}}
        <div data-aos="fade-up" x-show="tab==='videos'" x-cloak>
          <div class="flex items-end justify-between gap-4">
            <div>
              <div class="inline-flex items-center gap-2 text-xs font-extrabold" style="color:#055a93;">
                <i data-lucide="clapperboard" class="w-4 h-4" style="color:#0194F3;"></i>
                {{ $isEn ? 'VIDEO GALLERY' : 'GALERI VIDEO' }}
              </div>
              <h2 class="mt-2 text-xl lg:text-2xl font-extrabold text-slate-900">
                {{ $isEn ? 'Videos' : 'Video' }}
              </h2>
              <p class="mt-1 text-slate-600 text-sm">
                {{ $isEn ? 'Video highlights of activities and trips.' : 'Cuplikan video kegiatan dan perjalanan.' }}
              </p>

            </div>

            <div class="hidden sm:flex items-center gap-2">
              <span class="pill pill-azure">
                <i data-lucide="film" class="w-4 h-4"></i>
                {{ $videos->count() }} item
              </span>
            </div>
          </div>

          @if($videos->count())
          <div class="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($videos as $v)
            <div class="rounded-2xl overflow-hidden border border-slate-200 bg-white shadow-sm hover:shadow-md transition">
              <div class="relative bg-black">
                @php
                $u = $v->url;

                // direct file video?
                $isDirect = preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $u);

                // tentuin mime kalau direct file
                $path = parse_url($u, PHP_URL_PATH) ?? '';
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $mime = $ext === 'webm' ? 'video/webm' : ($ext === 'ogg' ? 'video/ogg' : 'video/mp4');

                // embed common
                $isEmbed =
                str_contains($u, 'youtube.com/embed') ||
                str_contains($u, 'player.vimeo.com') ||
                (str_contains($u, 'cloudinary.com') && (str_contains($u, 'player') || str_contains($u, '/video/')));
                @endphp

                @if($isDirect)
                <video controls preload="metadata" class="w-full h-56 object-cover">
                  <source src="{{ $u }}" type="{{ $mime }}">
                  Browser Anda tidak mendukung pemutaran video.
                </video>
                @elseif($isEmbed)
                <iframe
                  src="{{ $u }}"
                  class="w-full h-56"
                  frameborder="0"
                  allow="autoplay; fullscreen; picture-in-picture"
                  allowfullscreen></iframe>
                @else
                <div class="w-full h-56 grid place-items-center bg-slate-900">
                  <a href="{{ $u }}" target="_blank"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs font-extrabold bg-white text-slate-900 hover:bg-slate-100 transition">
                    Buka Video (Link)
                  </a>
                </div>
                @endif

                {{-- top badge --}}
                <div class="absolute top-3 left-3 inline-flex items-center gap-2 rounded-full bg-white/92 border border-slate-200 px-3 py-1 text-xs font-extrabold text-slate-700 shadow">
                  <i data-lucide="video" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                  Dokumentasi
                </div>
              </div>

              <div class="p-4">
                <div class="text-sm font-extrabold text-slate-900 line-clamp-1">
                  @php
                  $vTitle = $isEn ? ($v->title_en ?: $v->title) : $v->title;
                  $vTitle = $vTitle ?: ($isEn ? 'Video Documentation' : 'Dokumentasi Video');
                  @endphp

                  {{ $vTitle }}
                </div>
              </div>
            </div>
            @endforeach
          </div>
          @else
          <div class="mt-6 card p-10 text-center text-slate-600">
            <div class="mx-auto h-14 w-14 rounded-2xl border grid place-items-center"
              style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22);">
              <i data-lucide="video-off" class="w-7 h-7" style="color:#0194F3;"></i>
            </div>
            <div class="mt-4 font-extrabold text-slate-900">Belum ada video</div>
            <p class="mt-2 text-sm text-slate-600">Dokumentasi video belum tersedia saat ini.</p>
          </div>
          @endif
        </div>

        {{-- ================= LIGHTBOX OVERLAY ================= --}}
        <div
          x-show="open"
          x-transition.opacity
          class="fixed inset-0 z-[999] bg-black/80 backdrop-blur-sm flex items-center justify-center"
          style="display:none"
          @click.self="close()"
          @keydown.escape.window="close()">
          {{-- close --}}
          <button
            class="absolute top-5 right-5 h-12 w-12 rounded-2xl border border-white/15 bg-white/10 hover:bg-white/15 grid place-items-center text-white transition"
            type="button"
            @click="close()"
            aria-label="Tutup">
            <i data-lucide="x" class="w-6 h-6"></i>
          </button>

          {{-- prev --}}
          <button
            class="absolute left-4 h-12 w-12 rounded-2xl border border-white/15 bg-white/10 hover:bg-white/15 grid place-items-center text-white transition"
            type="button"
            @click="prev()"
            aria-label="Sebelumnya">
            <i data-lucide="chevron-left" class="w-7 h-7"></i>
          </button>

          {{-- next --}}
          <button
            class="absolute right-4 h-12 w-12 rounded-2xl border border-white/15 bg-white/10 hover:bg-white/15 grid place-items-center text-white transition"
            type="button"
            @click="next()"
            aria-label="Berikutnya">
            <i data-lucide="chevron-right" class="w-7 h-7"></i>
          </button>

          <div class="relative">
            <img
              :src="current"
              class="max-h-[90vh] max-w-[90vw] rounded-2xl shadow-2xl border border-white/10"
              alt="Preview Dokumentasi">

            {{-- hint --}}
            <div class="absolute -bottom-10 left-1/2 -translate-x-1/2 text-white/80 text-xs font-semibold hidden sm:flex items-center gap-2">
              <span class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/10 px-3 py-1">
                <i data-lucide="keyboard" class="w-4 h-4"></i>
                {{ $isEn ? 'ESC to close Use arrow buttons to navigate' : 'ESC untuk menutup Tombol panah untuk navigasi' }}
              </span>
            </div>

          </div>
        </div>

      </section>
    </div>

  </div>
</section>

{{-- SCRIPT lightbox --}}
<script>
  function lightboxGallery() {
    return {
      open: false,
      current: null,
      images: [],
      index: 0,

      init() {
        this.images = Array.from(document.querySelectorAll('[data-lightbox]'))
          .map(el => el.dataset.lightbox);
      },

      openLightbox(src) {
        if (!this.images.length) this.init();
        this.current = src;
        this.index = this.images.indexOf(src);
        if (this.index < 0) this.index = 0;
        this.open = true;
        document.body.style.overflow = 'hidden';
      },

      close() {
        this.open = false;
        this.current = null;
        document.body.style.overflow = '';
      },

      next() {
        if (!this.images.length) return;
        this.index = (this.index + 1) % this.images.length;
        this.current = this.images[this.index];
      },

      prev() {
        if (!this.images.length) return;
        this.index = (this.index - 1 + this.images.length) % this.images.length;
        this.current = this.images[this.index];
      }
    }
  }
</script>

@endsection