<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset('favicon.ico') }}">

    <title>{{ config('app.name', 'Bintang Wisata') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
<script src="{{ mix('js/app.js') }}" defer></script>


    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 antialiased">

    {{-- subtle brand background --}}
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -left-40 h-[520px] w-[520px] rounded-full blur-3xl opacity-30"
             style="background: radial-gradient(circle, #0194F3 0%, transparent 60%);">
        </div>
        <div class="absolute -bottom-40 -right-40 h-[520px] w-[520px] rounded-full blur-3xl opacity-20"
             style="background: radial-gradient(circle, #0194F3 0%, transparent 60%);">
        </div>
    </div>

    <div class="relative flex min-h-screen items-center justify-center px-4">
        {{ $slot }}
    </div>

</body>
</html>
