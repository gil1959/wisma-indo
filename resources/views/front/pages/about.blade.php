@extends('layouts.front')
@php $isEn = app()->getLocale() === 'en'; @endphp
@section('title', $siteSettings['about_meta_title'] ?? 'About - Wisma Indo')

@section('content')

{{-- HERO ABOUT --}}
<section class="relative overflow-hidden bg-white">
  <div class="absolute inset-0 travel-grid opacity-70"></div>
  <svg class="absolute -top-20 -right-20 w-[520px] h-[520px] opacity-80" viewBox="0 0 600 600" fill="none" aria-hidden="true">
    <defs>
      <radialGradient id="aboutGlow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(320 280) rotate(90) scale(280)">
        <stop stop-color="#0194F3" stop-opacity="0.20" />
        <stop offset="1" stop-color="#0194F3" stop-opacity="0" />
      </radialGradient>
    </defs>
    <circle cx="320" cy="280" r="280" fill="url(#aboutGlow)" />
    <path d="M130 330c70-90 170-150 280-150 40 0 80 7 120 20" stroke="#0194F3" stroke-opacity="0.25" stroke-width="2" stroke-linecap="round" />
    <path d="M165 385c85-70 160-105 245-105 70 0 125 18 170 42" stroke="#0194F3" stroke-opacity="0.18" stroke-width="2" stroke-linecap="round" />
  </svg>

  <div class="relative max-w-7xl mx-auto px-4 pt-10 pb-10 lg:pt-16 lg:pb-16">
    <div class="grid gap-12 lg:grid-cols-2 lg:items-center">

      {{-- LEFT --}}
      <div data-aos="fade-up">
        <div class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-extrabold"
          style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
          <span class="h-2 w-2 rounded-full" style="background:#0194F3;"></span>
          {{ $siteSettings['about_hero_badge'] ?? 'Tentang Wisma Indo' }}
        </div>

        <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-900 leading-tight">
          {{ $siteSettings['about_hero_title'] ?? ($isEn
      ? 'A travel partner that is neat, transparent, and focused on your comfort'
      : 'Mitra perjalanan yang rapi, transparan, dan berorientasi pada kenyamanan Anda'
  ) }}
        </h1>

        <p class="mt-4 text-base lg:text-lg text-slate-700 leading-relaxed max-w-2xl">
          {!! nl2br(e($siteSettings['about_hero_desc'] ?? ($isEn
          ? 'Wisma Indo provides travel and transportation services designed to make everything easier—from choosing packages and scheduling, to on-trip support. Transparency and service accuracy are our baseline standards.'
          : 'Wisma Indo menyediakan layanan perjalanan dan transportasi yang dirancang untuk memudahkan Anda: mulai dari pemilihan paket, penjadwalan, hingga dukungan selama perjalanan. Kami menempatkan transparansi dan ketepatan layanan sebagai standar utama.'
          ))) !!}
        </p>


        <div class="mt-8 flex flex-col sm:flex-row gap-3">
          <a href="{{ route('tours.index') }}" class="btn btn-primary">
            <i data-lucide="map" class="w-4 h-4"></i>
            {{ $isEn ? 'View Tour Packages' : 'Lihat Paket Tour' }}
          </a>
          <a href="{{ route('docs') }}" class="btn btn-ghost">
            <i data-lucide="book-open" class="w-4 h-4"></i>
            {{ $isEn ? 'Trip Documentation' : 'Dokumentasi Perjalanan' }}
          </a>

        </div>

        {{-- TRUST STATS --}}
        <div class="mt-10 grid gap-4 sm:grid-cols-3" data-aos="fade-up" data-aos-delay="120">
          <div class="card p-5">
            <div class="flex items-center gap-3">
              <div class="icon-badge"><i data-lucide="badge-check" class="w-5 h-5"></i></div>
              <div>
                <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Service Focus' : 'Fokus Layanan' }}</div>
                <div class="mt-1 text-lg font-extrabold text-slate-900">{{ $isEn ? 'Tours & Rentals' : 'Tour & Rental' }}</div>
                <div class="mt-1 text-sm text-slate-600">{{ $isEn ? 'One standard, consistent quality.' : 'Satu standar, satu kualitas.' }}</div>
              </div>
            </div>
          </div>
          <div class="card p-5">
            <div class="flex items-center gap-3">
              <div class="icon-badge"><i data-lucide="zap" class="w-5 h-5"></i></div>
              <div>
                <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Process' : 'Proses' }}</div>
                <div class="mt-1 text-lg font-extrabold text-slate-900">{{ $isEn ? 'Fast & Clear' : 'Cepat & Jelas' }}</div>
                <div class="mt-1 text-sm text-slate-600">{{ $isEn ? 'No confusing steps.' : 'Tanpa langkah membingungkan.' }}</div>
              </div>
            </div>
          </div>
          <div class="card p-5">
            <div class="flex items-center gap-3">
              <div class="icon-badge"><i data-lucide="shield-check" class="w-5 h-5"></i></div>
              <div>
                <div class="text-xs font-semibold text-slate-500">{{ $isEn ? 'Commitment' : 'Komitmen' }}</div>
                <div class="mt-1 text-lg font-extrabold text-slate-900">{{ $isEn ? 'Transparent' : 'Transparan' }}</div>
                <div class="mt-1 text-sm text-slate-600">{{ $isEn ? 'Clear pricing & inclusions.' : 'Harga & fasilitas jelas.' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- RIGHT (VISUAL) --}}
      <div data-aos="fade-left" class="relative">
        <div class="rounded-3xl overflow-hidden border border-slate-200 bg-white shadow-soft">
          <div class="relative aspect-[4/3]">
            <div class="absolute inset-0 travel-dots opacity-70"></div>

            {{-- Decorative route line --}}
            <svg class="absolute inset-0" viewBox="0 0 800 600" fill="none" aria-hidden="true">
              <path d="M130 420c85-130 210-210 360-210 90 0 175 25 260 70" stroke="#0194F3" stroke-opacity="0.25" stroke-width="4" stroke-linecap="round" />
              <path d="M580 165l18 18-18 18" stroke="#0194F3" stroke-opacity="0.25" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
              <circle cx="150" cy="410" r="10" fill="#0194F3" fill-opacity="0.35" />
              <circle cx="610" cy="190" r="10" fill="#0194F3" fill-opacity="0.35" />
            </svg>

            {{-- Overlay card --}}
            <div class="absolute bottom-5 left-5 right-5 rounded-2xl bg-white/85 backdrop-blur border border-white/70 p-5 shadow-sm">
              <div class="text-sm font-extrabold text-slate-900">
                {{ $isEn ? 'Services designed for a comfortable experience' : 'Layanan yang dirancang untuk pengalaman yang nyaman' }}
              </div>
              <p class="mt-2 text-sm text-slate-600">
                {{ $isEn
      ? 'We prioritize a structured service flow: clear information, responsive communication, and execution as planned.'
      : 'Kami mengutamakan alur layanan yang tertata: informasi jelas, komunikasi responsif, dan eksekusi sesuai rencana.'
  }}
              </p>

            </div>
          </div>
        </div>

        {{-- Floating note --}}
        <div class="absolute -bottom-6 -left-2 sm:-left-6 rounded-2xl bg-white border border-slate-200 p-5 shadow-sm w-[92%] sm:w-[70%]"
          data-aos="fade-up" data-aos-delay="200">
          <div class="text-xs font-semibold" style="color:#055a93;">{{ $isEn ? 'Service Standard' : 'Standar Layanan' }}</div>
          <div class="mt-1 text-sm font-extrabold text-slate-900">
            {{ $isEn ? 'Clear communication, measurable process' : 'Komunikasi jelas, proses terukur' }}
          </div>
          <p class="mt-2 text-sm text-slate-600">
            {{ $isEn
      ? 'Each request is handled with a clear flow so you can plan your trip with peace of mind.'
      : 'Setiap permintaan ditangani dengan alur yang jelas agar Anda dapat merencanakan perjalanan dengan tenang.'
  }}
          </p>

        </div>
      </div>

    </div>
  </div>
</section>

{{-- VALUES --}}
<section class="max-w-7xl mx-auto px-4 pb-14 lg:pb-20">
  <div class="grid gap-8 lg:grid-cols-3 items-start">
    <div data-aos="fade-up">
      <div class="text-xs font-extrabold" style="color:#055a93;">
        {{ $siteSettings['about_values_label'] ?? 'NILAI KAMI' }}
      </div>
      <h2 class="mt-2 text-2xl lg:text-3xl font-extrabold text-slate-900">
        {{ $siteSettings['about_values_title'] ?? 'Prinsip kerja yang kami pegang' }}
      </h2>
      <p class="mt-3 text-slate-600">
        {!! nl2br(e($siteSettings['about_values_desc'] ?? 'Kami membangun layanan yang rapi dan konsisten. Tujuannya sederhana: pengalaman perjalanan yang nyaman dan dapat diandalkan.')) !!}
      </p>
    </div>

    <div class="lg:col-span-2 grid gap-4 sm:grid-cols-2" data-aos="fade-up" data-aos-delay="100">
      @php
      $values = [
      [
      'icon' => 'receipt-text',
      'title' => ($siteSettings['about_value1_title'] ?? 'Transparansi'),
      'desc' => ($siteSettings['about_value1_desc'] ?? 'Harga, fasilitas, dan ketentuan disampaikan dengan jelas sejak awal.'),
      ],
      [
      'icon' => 'clock-4',
      'title' => ($siteSettings['about_value2_title'] ?? 'Ketepatan'),
      'desc' => ($siteSettings['about_value2_desc'] ?? 'Jadwal dan rencana perjalanan disusun realistis sesuai kebutuhan Anda.'),
      ],
      [
      'icon' => 'sparkles',
      'title' => ($siteSettings['about_value3_title'] ?? 'Kenyamanan'),
      'desc' => ($siteSettings['about_value3_desc'] ?? 'Kami menjaga detail layanan agar perjalanan terasa lebih ringan.'),
      ],
      [
      'icon' => 'message-circle',
      'title' => ($siteSettings['about_value4_title'] ?? 'Responsif'),
      'desc' => ($siteSettings['about_value4_desc'] ?? 'Tim kami memberikan bantuan cepat untuk pertanyaan dan penyesuaian.'),
      ],
      ];
      @endphp

      @foreach($values as $v)
      <div class="card p-6">
        <div class="flex items-start gap-4">
          <div class="icon-badge">
            <i data-lucide="{{ $v['icon'] }}" class="w-5 h-5"></i>
          </div>
          <div>
            <div class="text-sm font-extrabold text-slate-900">{{ $v['title'] }}</div>
            <p class="mt-2 text-sm text-slate-600">{{ $v['desc'] }}</p>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- HOW IT WORKS --}}
<section class="max-w-7xl mx-auto px-4 pb-14 lg:pb-20">
  <div class="rounded-3xl bg-white border border-slate-200 shadow-soft p-6 lg:p-10" data-aos="fade-up">
    <div class="grid gap-10 lg:grid-cols-3">
      <div>
        <div class="text-xs font-extrabold" style="color:#055a93;">
          {{ $siteSettings['about_flow_label'] ?? 'ALUR LAYANAN' }}
        </div>
        <h2 class="mt-2 text-2xl font-extrabold text-slate-900">
          {{ $siteSettings['about_flow_title'] ?? 'Langkah sederhana, hasil yang jelas' }}
        </h2>
        <p class="mt-3 text-slate-600 text-sm">
          {!! nl2br(e($siteSettings['about_flow_desc'] ?? 'Kami menyusun alur layanan agar Anda dapat melakukan pemesanan tanpa kebingungan. Setiap tahap terstruktur dan mudah diikuti.')) !!}
        </p>
      </div>

      <div class="lg:col-span-2 grid gap-4 sm:grid-cols-2">
        @php
        $steps = [
        [
        'no' => '01', 'icon' => 'map',
        'title' => ($siteSettings['about_step1_title'] ?? 'Pilih layanan'),
        'desc' => ($siteSettings['about_step1_desc'] ?? 'Tentukan paket tour atau rental sesuai kebutuhan.'),
        ],
        [
        'no' => '02', 'icon' => 'messages-square',
        'title' => ($siteSettings['about_step2_title'] ?? 'Konsultasi singkat'),
        'desc' => ($siteSettings['about_step2_desc'] ?? 'Konfirmasi detail itinerary, durasi, dan ketentuan.'),
        ],
        [
        'no' => '03', 'icon' => 'calendar-check',
        'title' => ($siteSettings['about_step3_title'] ?? 'Pemesanan'),
        'desc' => ($siteSettings['about_step3_desc'] ?? 'Lengkapi data dan lakukan proses sesuai instruksi.'),
        ],
        [
        'no' => '04', 'icon' => 'plane',
        'title' => ($siteSettings['about_step4_title'] ?? 'Perjalanan dimulai'),
        'desc' => ($siteSettings['about_step4_desc'] ?? 'Nikmati perjalanan, tim kami siap membantu bila diperlukan.'),
        ],
        ];
        @endphp

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
            <div class="mt-4 text-sm font-extrabold text-slate-900">{{ $s['title'] }}</div>
            <p class="mt-2 text-sm text-slate-600">{{ $s['desc'] }}</p>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

{{-- CTA --}}
<section class="max-w-7xl mx-auto px-4 pb-16 lg:pb-24">
  <div class="rounded-3xl overflow-hidden p-8 lg:p-12 text-white shadow-soft relative"
    style="background: linear-gradient(90deg, #0194F3 0%, rgba(1,148,243,0.70) 100%);"
    data-aos="fade-up">
    <div class="absolute inset-0 opacity-60 pointer-events-none">
      <svg class="absolute -top-10 -right-10 w-72 h-72" viewBox="0 0 300 300" fill="none" aria-hidden="true">
        <circle cx="150" cy="150" r="120" fill="#FFFFFF" fill-opacity="0.10" />
        <path d="M70 160c35-45 80-70 130-70 20 0 40 4 60 12" stroke="#FFFFFF" stroke-opacity="0.22" stroke-width="3" stroke-linecap="round" />
      </svg>
    </div>

    <div class="grid gap-8 lg:grid-cols-2 lg:items-center relative">
      <div>
        <h2 class="text-2xl lg:text-3xl font-extrabold">
          {{ $isEn ? 'Ready to plan your trip?' : 'Siap merencanakan perjalanan?' }}
        </h2>
        <p class="mt-2 text-white/90">
          {{ $isEn
      ? 'Explore tour packages and trip documentation. We are ready to help you choose the right option.'
      : 'Jelajahi pilihan paket tour dan dokumentasi perjalanan. Kami siap membantu Anda memilih opsi yang sesuai.'
  }}
        </p>

        <a href="{{ route('tours.index') }}" class="btn bg-white text-slate-900 hover:bg-white/90">
          <i data-lucide="map" class="w-4 h-4"></i>
          {{ $isEn ? 'View Tour Packages' : 'Lihat Paket Tour' }}
        </a>
        <a href="{{ route('docs') }}" class="btn bg-white/10 border border-white/20 text-white hover:bg-white/15">
          <i data-lucide="book-open" class="w-4 h-4"></i>
          {{ $isEn ? 'View Documentation' : 'Lihat Dokumentasi' }}
        </a>

      </div>
    </div>
  </div>
</section>

@endsection