<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <link rel="icon" type="image/x-icon" href="{{ isset($siteSettings['site_favicon']) && $siteSettings['site_favicon'] != '' ? asset($siteSettings['site_favicon']) : asset('favicon.ico') }}?v=4">

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>{{ $siteSettings['seo_meta_title'] ?? ($siteSettings['brand_name'] ?? 'Rumaindo') }} | Portal Properti Terpercaya</title>
  <meta name="description" content="{{ $siteSettings['seo_meta_desc'] ?? 'Temukan properti impian Anda di ' . ($siteSettings['brand_name'] ?? 'Rumaindo') . '. Jual beli dan sewa rumah, apartemen, ruko, tanah, serta temukan kebutuhan barang dan jasa terkait properti.' }}">
  <meta name="keywords" content="{{ $siteSettings['seo_meta_keywords'] ?? 'properti, jual rumah, sewa apartemen, ruko, tanah kavling, barang jasa properti, rumaindo' }}">
  <meta name="author" content="{{ $siteSettings['brand_name'] ?? 'Rumaindo' }}">
  <meta name="robots" content="index, follow">

  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ url('/') }}">
  <meta property="og:title" content="{{ $siteSettings['seo_meta_title'] ?? ($siteSettings['brand_name'] ?? 'Rumaindo') }} | Portal Properti Terpercaya">
  <meta property="og:description" content="{{ $siteSettings['seo_meta_desc'] ?? 'Temukan properti impian Anda di ' . ($siteSettings['brand_name'] ?? 'Rumaindo') . '.' }}">
  
  <link rel="canonical" href="{{ url('/') }}">

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

  @if(!empty($siteSettings['tracking_head']))
  {!! $siteSettings['tracking_head'] !!}
  @endif
</head>

<body class="bg-slate-50 font-[Poppins] text-slate-800 antialiased">
  @if(!empty($siteSettings['tracking_body']))
  {!! $siteSettings['tracking_body'] !!}
  @endif

  <div class="min-h-screen flex flex-col">
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