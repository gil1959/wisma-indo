<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <link rel="icon" type="image/x-icon" href="{{ isset($siteSettings['site_favicon']) && $siteSettings['site_favicon'] != '' ? asset($siteSettings['site_favicon']) : asset('favicon.ico') }}?v=4">

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>@hasSection('title') @yield('title') @else {{ $siteSettings['seo_meta_title'] ?? ($siteSettings['brand_name'] ?? 'Rumaindo') }} | Portal Properti Terpercaya @endif</title>
  <meta name="description" content="@hasSection('meta_desc') @yield('meta_desc') @else {{ $siteSettings['seo_meta_desc'] ?? 'Temukan properti impian Anda di ' . ($siteSettings['brand_name'] ?? 'Rumaindo') . '. Jual beli dan sewa rumah, apartemen, ruko, tanah, serta temukan kebutuhan barang dan jasa terkait properti.' }} @endif">
  <meta name="keywords" content="@hasSection('meta_keywords') @yield('meta_keywords') @else {{ $siteSettings['seo_meta_keywords'] ?? 'properti, jual rumah, sewa apartemen, ruko, tanah kavling, barang jasa properti, rumaindo' }} @endif">
  <meta name="author" content="{{ $siteSettings['brand_name'] ?? 'Rumaindo' }}">
  <meta name="robots" content="index, follow">

  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:title" content="@hasSection('social_title') @yield('social_title') @elseif(View::hasSection('title')) @yield('title') @else {{ $siteSettings['seo_meta_title'] ?? ($siteSettings['brand_name'] ?? 'Rumaindo') }} | Portal Properti Terpercaya @endif">
  <meta property="og:description" content="@hasSection('social_desc') @yield('social_desc') @elseif(View::hasSection('meta_desc')) @yield('meta_desc') @else {{ $siteSettings['seo_meta_desc'] ?? 'Temukan properti impian Anda di ' . ($siteSettings['brand_name'] ?? 'Rumaindo') . '.' }} @endif">
  @hasSection('seo_image')
  <meta property="og:image" content="@yield('seo_image')">
  <meta name="twitter:image" content="@yield('seo_image')">
  @endif
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="@hasSection('social_title') @yield('social_title') @elseif(View::hasSection('title')) @yield('title') @else {{ $siteSettings['seo_meta_title'] ?? ($siteSettings['brand_name'] ?? 'Rumaindo') }} | Portal Properti Terpercaya @endif">
  <meta name="twitter:description" content="@hasSection('social_desc') @yield('social_desc') @elseif(View::hasSection('meta_desc')) @yield('meta_desc') @else {{ $siteSettings['seo_meta_desc'] ?? 'Temukan properti impian Anda di ' . ($siteSettings['brand_name'] ?? 'Rumaindo') . '.' }} @endif">
  @hasSection('canonical')
  <link rel="canonical" href="@yield('canonical')">
  @else
  <link rel="canonical" href="{{ url()->current() }}">
  @endif
  <script type="application/ld+json">
    {
      "@context": "https://schema.org/",
      "@type": "WebSite",
      "name": "{{ $siteSettings['brand_name'] ?? 'Rumaindo' }}",
      "url": "{{ url('/') }}"
    }
  </script>

  {{-- FONT --}}
  <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap">

  {{-- APP CSS via MIX --}}
  <link rel="preload" as="style" href="{{ mix('css/app.css') }}">
  <link rel="stylesheet" href="{{ mix('css/app.css') }}">

  <script defer src="{{ mix('js/app.js') }}"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
  <meta name="theme-color" content="#0194F3">
  <style>
    [x-cloak] { display: none !important; }
  </style>

  @if(!empty($siteSettings['tracking_script_head']))
  {!! $siteSettings['tracking_script_head'] !!}
  @endif
</head>

<body class="bg-slate-50 font-[Poppins] text-slate-800 antialiased">
  @if(!empty($siteSettings['tracking_script_body']))
  {!! $siteSettings['tracking_script_body'] !!}
  @endif

  <div class="min-h-screen flex flex-col">
    @if(session()->has('impersonator_id'))
    <div class="bg-amber-100 border-b border-amber-200 text-amber-800 px-4 py-2 text-center text-sm font-bold shadow-sm z-[9999] relative flex items-center justify-center gap-4">
        Anda sedang login sebagai {{ auth()->user()->name }}
        <a href="{{ route('leave-impersonate') }}" class="px-3 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs transition">Kembali ke Admin</a>
    </div>
    @endif
    
    @include('front.partials.navbar')

    <main class="flex-1 pb-24 lg:pb-0">
      @yield('content')
    </main>

    @include('front.partials.footer')

    @include('front.partials.mobile-bottom-nav')

    @include('shared.popup-widget')
  </div>

  @yield('scripts')

  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init({
      once: true,
      duration: 700,
      offset: 80
    });
  </script>

  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    lucide.createIcons();

    @if(session('status'))
      Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: "{{ session('status') }}",
          confirmButtonColor: '#0194F3',
          timer: 5000
      });
    @endif

    @if(request()->has('verified') && request('verified') == 1)
      Swal.fire({
          icon: 'success',
          title: 'Email Terverifikasi!',
          text: 'Alamat email Anda telah berhasil diverifikasi.',
          confirmButtonColor: '#0194F3',
          timer: 5000
      });
    @endif

    @if($errors->any())
      Swal.fire({
          icon: 'error',
          title: 'Oops...',
          html: '<ul style="text-align:left; list-style:disc; padding-left:20px; font-size:14px;">' +
                @foreach($errors->all() as $error)
                  '<li>{{ $error }}</li>' +
                @endforeach
                '</ul>',
          confirmButtonColor: '#0194F3'
      });
    @endif
  </script>

  {{-- Page-specific scripts (pushed AFTER lucide is ready) --}}
  @stack('scripts')

</body>
</html>