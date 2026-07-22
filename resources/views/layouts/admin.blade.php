<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ isset($siteSettings['site_favicon']) ? asset($siteSettings['site_favicon']) : asset('favicon.ico') }}">

    <title>@yield('title', 'Admin Panel') - {{ $siteSettings['brand_name'] ?? 'Brand Anda' }}</title>

    {{-- Fonts --}}
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700;800&display=swap">

    {{-- App CSS (Tailwind) --}}
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    @stack('styles')

    {{-- Alpine --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Lucide --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: Nunito, ui-sans-serif, system-ui;
        }

        a {
            text-decoration: none !important;
        }
    </style>
</head>

<body class="bg-slate-50 antialiased admin-ui">

    <div
        x-data="{ sidebarOpen:false }"
        x-init="if (window.lucide) lucide.createIcons()"
        class="min-h-screen">

        {{-- OVERLAY (MOBILE) --}}
        <div
            x-show="sidebarOpen"
            x-cloak
            x-transition.opacity
            @click="sidebarOpen=false"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 lg:hidden"></div>

        <div class="flex min-h-screen">

            {{-- SIDEBAR --}}
            <aside
                class="fixed lg:static inset-y-0 left-0 z-50
                   w-[18rem] lg:w-64
                   bg-white/90 backdrop-blur
                   border-r border-slate-200
                   transform transition-transform duration-300
                   lg:translate-x-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
                {{-- BRAND --}}
                <div class="h-16 px-5 flex items-center justify-between border-b border-slate-200">
                    <div class="flex items-center gap-3">
                        @if(!empty($siteSettings['site_logo']))
                            <img src="{{ asset($siteSettings['site_logo']) }}" alt="Logo" class="h-10 w-10 object-contain rounded-xl">
                        @else
                            <div class="h-10 w-10 rounded-2xl grid place-items-center border"
                                style="background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22)">
                                <i data-lucide="shield" class="w-5 h-5" style="color:#0194F3;"></i>
                            </div>
                        @endif
                        <div class="min-w-0">
                            <div class="font-extrabold text-slate-900 leading-tight truncate">Admin Panel</div>
                            <div class="text-xs text-slate-500 -mt-0.5 truncate">{{ $siteSettings['brand_name'] ?? 'Brand Anda' }}</div>
                        </div>
                    </div>

                    <button
                        @click="sidebarOpen=false"
                        class="lg:hidden h-9 w-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 grid place-items-center"
                        aria-label="Tutup menu">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                {{-- NAV --}}
                @php
                $nav = [
                    ['label'=>'Dashboard','route'=>'admin.dashboard','match'=>'admin.dashboard','icon'=>'layout-dashboard'],
                    
                    [
                        'label' => 'Data Properti',
                        'icon' => 'home',
                        'children' => [
                            ['label'=>'Semua Iklan','route'=>'admin.listings.index','match'=>'admin.listings.*','icon'=>'list'],
                            ['label'=>'Kategori Iklan','route'=>'admin.listing-categories.index','match'=>'admin.listing-categories.*','icon'=>'tags'],
                        ],
                    ],
                    
                    [
                        'label' => 'Transaksi & Saldo',
                        'icon' => 'credit-card',
                        'children' => [
                            ['label'=>'Paket Top Up','route'=>'admin.topup-packages.index','match'=>'admin.topup-packages.*','icon'=>'package'],
                            ['label'=>'Paket Promosi','route'=>'admin.listing-packages.index','match'=>'admin.listing-packages.*','icon'=>'star'],
                            ['label'=>'Permintaan Top Up','route'=>'admin.topups.index','match'=>'admin.topups.*','icon'=>'wallet'],
                            ['label'=>'Promosi Iklan','route'=>'admin.listing-promotions.index','match'=>'admin.listing-promotions.*','icon'=>'trending-up'],
                        ],
                    ],
                    
                    [
                        'label' => 'Pengguna (Users)',
                        'icon' => 'users',
                        'children' => [
                            ['label'=>'Semua Akun','route'=>'admin.users.index','match'=>'admin.users.*','icon'=>'users'],
                        ],
                    ],
                    
                    ['label'=>'Push Notifikasi','route'=>'admin.notifications.create','match'=>'admin.notifications.*','icon'=>'bell'],
                    ['label'=>'Popup Widget','route'=>'admin.settings.popup.edit','match'=>'admin.settings.popup.*','icon'=>'message-square'],
                    
                    [
                        'label' => 'Artikel & Berita',
                        'icon' => 'newspaper',
                        'children' => [
                            ['label'=>'Daftar Artikel','route'=>'admin.articles.index','match'=>'admin.articles.*','icon'=>'file-text'],
                            ['label'=>'Kategori','route'=>'admin.article-categories.index','match'=>'admin.article-categories.*','icon'=>'layers'],
                        ],
                    ],
                    
                    [
                        'label' => 'Halaman Legal',
                        'icon' => 'book-open',
                        'children' => [
                            ['label'=>'Kebijakan Privasi','route'=>'admin.legal.privacy','match'=>'admin.legal.privacy','icon'=>'file-text'],
                            ['label'=>'Syarat & Ketentuan','route'=>'admin.legal.terms','match'=>'admin.legal.terms','icon'=>'file-text'],
                            ['label'=>'Kontak Kami','route'=>'admin.legal.contact','match'=>'admin.legal.contact','icon'=>'phone'],
                        ],
                    ],
                    
                    [
                        'label' => 'Settings',
                        'icon' => 'settings',
                        'children' => [
                            ['label'=>'General','route'=>'admin.settings.general','match'=>'admin.settings.general','icon'=>'sliders'],
                            ['label'=>'Home Setting','route'=>'admin.settings.home','match'=>'admin.settings.home*','icon'=>'layout-grid'],
                        ],
                    ],

                    ['label'=>'Profil Admin','route'=>'admin.profile.edit','match'=>'admin.profile.*','icon'=>'user'],
                ];
                @endphp


                <nav class="px-3 py-3 space-y-1 overflow-y-auto" style="max-height:calc(100vh - 4rem)">
                    @php
                    $isAdmin = auth()->check() && auth()->user()->hasRole('admin');
                    @endphp

                    @foreach($nav as $n)

                    {{-- CASE 1: GROUP / HAS CHILDREN --}}
                    @if(isset($n['children']))
                    @php
                    $childActive = false;
                    $hasVisibleChild = false;

                    foreach($n['children'] as $c){
                    $needPerm = $c['perm'] ?? null;
                    $allowed = $isAdmin || !$needPerm || auth()->user()->can($needPerm);

                    if ($allowed) {
                    $hasVisibleChild = true;
                    if (request()->routeIs($c['match'])) {
                    $childActive = true;
                    }
                    }
                    }
                    @endphp

                    @if(!$hasVisibleChild)
                    @continue
                    @endif

                    <div x-data="{ open: {{ $childActive ? 'true' : 'false' }} }" class="space-y-1">
                        <button type="button"
                            class="w-full flex items-center gap-3 px-3 py-2 rounded-2xl hover:bg-slate-100 transition"
                            @click="open = !open">
                            <span class="h-9 w-9 rounded-xl grid place-items-center border shrink-0"
                                style="{{ $childActive ? 'background:#0194F3;color:white;border-color:#0194F3;' : 'background:white;color:#64748b;border-color:#e2e8f0;' }}">
                                <i data-lucide="{{ $n['icon'] }}" class="h-5 w-5"></i>
                            </span>
                            <span class="flex-1 text-left text-sm font-extrabold text-slate-900">{{ $n['label'] }}</span>
                            <i data-lucide="chevron-down" class="h-4 w-4 text-slate-500"
                                :style="open ? 'transform: rotate(180deg);' : ''"></i>
                        </button>

                        <div x-show="open" x-cloak class="pl-3 space-y-1">
                            @foreach($n['children'] as $c)
                            @php
                            $needPerm = $c['perm'] ?? null;
                            $allowed = $isAdmin || !$needPerm || auth()->user()->can($needPerm);
                            @endphp

                            @if(!$allowed)
                            @continue
                            @endif

                            @php $active = request()->routeIs($c['match']); @endphp
                            <a href="{{ route($c['route'], $c['params'] ?? []) }}"
                                class="w-full flex items-center gap-3 px-3 py-2 rounded-2xl border transition
                                  {{ $active ? 'bg-slate-100 border-slate-200' : 'bg-white border-transparent hover:bg-slate-50' }}">
                                <span class="h-9 w-9 rounded-xl grid place-items-center border shrink-0"
                                    style="{{ $active ? 'background:#0194F3;color:white;border-color:#0194F3;' : 'background:white;color:#64748b;border-color:#e2e8f0;' }}">
                                    <i data-lucide="{{ $c['icon'] }}" class="h-5 w-5"></i>
                                </span>
                                <span class="text-sm font-extrabold text-slate-900">{{ $c['label'] }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- CASE 2: SINGLE --}}
                    @else
                    @php
                    $needPerm = $n['perm'] ?? null;
                    $allowed = $isAdmin || !$needPerm || auth()->user()->can($needPerm);
                    @endphp

                    @if(!$allowed)
                    @continue
                    @endif

                    @php $active = request()->routeIs($n['match']); @endphp

                    <a href="{{ route($n['route']) }}"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded-2xl border transition
                      {{ $active ? 'bg-slate-100 border-slate-200' : 'bg-white border-transparent hover:bg-slate-50' }}">
                        <span class="h-9 w-9 rounded-xl grid place-items-center border shrink-0"
                            style="{{ $active ? 'background:#0194F3;color:white;border-color:#0194F3;' : 'background:white;color:#64748b;border-color:#e2e8f0;' }}">
                            <i data-lucide="{{ $n['icon'] }}" class="h-5 w-5"></i>
                        </span>
                        <span class="text-sm font-extrabold text-slate-900">{{ $n['label'] }}</span>
                    </a>
                    @endif

                    @endforeach

                </nav>

            </aside>

            {{-- MAIN --}}
            <div class="flex-1 min-w-0 flex flex-col">

                {{-- TOPBAR --}}
                <header class="h-16 bg-white/90 backdrop-blur border-b border-slate-200 px-4 flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <button
                            @click="sidebarOpen=true"
                            class="lg:hidden h-9 w-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 grid place-items-center"
                            aria-label="Buka menu">
                            <i data-lucide="menu" class="w-5 h-5"></i>
                        </button>

                        <div class="hidden sm:flex h-9 w-9 rounded-xl border items-center justify-center"
                            style="background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22)">
                            <i data-lucide="sparkles" class="w-5 h-5" style="color:#0194F3;"></i>
                        </div>

                        <div class="min-w-0">
                            <h1 class="font-extrabold text-slate-900 truncate">
                                @yield('page-title','Dashboard')
                            </h1>
                            <div class="text-xs text-slate-500 hidden sm:block">
                                Kelola konten dan transaksi
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-3 hover:bg-slate-50 p-1.5 rounded-2xl transition">
                            <div class="hidden sm:block text-right">
                                <div class="font-bold text-slate-900">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-slate-500">{{ auth()->user()->email }}</div>
                            </div>
                            <div class="w-10 h-10 rounded-xl overflow-hidden border border-slate-200 bg-slate-100 flex items-center justify-center shrink-0">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset(auth()->user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                                @else
                                    <i data-lucide="user" class="w-5 h-5 text-slate-400"></i>
                                @endif
                            </div>
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-extrabold text-white transition"
                                style="background:#ef4444"
                                onmouseover="this.style.background='#dc2626'"
                                onmouseout="this.style.background='#ef4444'">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                <span class="hidden sm:inline">Log Out</span>
                            </button>
                        </form>
                    </div>
                </header>

                {{-- CONTENT --}}
                <main class="flex-1 p-4 lg:p-5">
                    <div class="mx-auto w-full max-w-[1180px]">
                        @yield('content')
                    </div>
                </main>

            </div>
        </div>
    </div>
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });
    </script>

</body>

</html>