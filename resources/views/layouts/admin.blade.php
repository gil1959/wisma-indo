<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <title>@yield('title', 'Admin Panel') - Bintang Wisata</title>

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
                        <div class="h-10 w-10 rounded-2xl grid place-items-center border"
                            style="background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22)">
                            <i data-lucide="shield" class="w-5 h-5" style="color:#0194F3;"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="font-extrabold text-slate-900 leading-tight truncate">Admin Panel</div>
                            <div class="text-xs text-slate-500 -mt-0.5 truncate">Bintang Wisata</div>
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
                ['label'=>'Dashboard','route'=>'admin.dashboard','match'=>'admin.dashboard','icon'=>'layout-dashboard','perm'=>'admin.dashboard.view'],
                ['label'=>'Orders','route'=>'admin.orders.index','match'=>'admin.orders.*','icon'=>'shopping-bag','perm'=>'admin.orders.manage'],
                ['label'=>'Pembayaran','route'=>'admin.payments.index','match'=>'admin.payments.*','icon'=>'credit-card','perm'=>'admin.payments.manage'],

                // GROUP: Paket Wisata
                [
                'label' => 'Paket Wisata',
                'icon' => 'map',
                'children' => [
                ['label'=>'Paket Wisata','route'=>'admin.tour-packages.index','match'=>'admin.tour-packages.*','icon'=>'map','perm'=>'admin.tour-packages.manage'],
                ['label'=>'Kategori Tour','route'=>'admin.categories.index','match'=>'admin.categories.*','icon'=>'tags','perm'=>'admin.categories.manage'],
                ],
                ],

                // GROUP: Rental
                [
                'label' => 'Rental',
                'icon' => 'car',
                'children' => [
                ['label'=>'Rental','route'=>'admin.rent-car-packages.index','match'=>'admin.rent-car-packages.*','icon'=>'car','perm'=>'admin.rent-car-packages.manage'],
                ['label'=>'Kategori Rental','route'=>'admin.rent-car-categories.index','match'=>'admin.rent-car-categories.*','icon'=>'tags','perm'=>'admin.rent-car-categories.manage'],
                ],
                ],
                ['label'=>'Kode Promo','route'=>'admin.promos.index','match'=>'admin.promos.*','icon'=>'ticket-percent','perm'=>'admin.promos.manage'],
                [
                'label' => 'Section Promo',
                'icon' => 'ticket-percent',
                'children' => [
                ['label'=>'Home Promo Tours','route'=>'admin.home-sections.promo-tours.edit','match'=>'admin.home-sections.promo-tours.*','icon'=>'sparkles','perm'=>'admin.home-sections.manage'],
                ['label'=>'Home Banner: Discount','route'=>'admin.promos.home-banners.index','params'=>['section'=>'discount'],'match'=>'admin.promos.home-banners.*','icon'=>'images','perm'=>'admin.promos.manage'],
                ['label'=>'Home Banner: Missions','route'=>'admin.promos.home-banners.index','params'=>['section'=>'missions'],'match'=>'admin.promos.home-banners.*','icon'=>'images','perm'=>'admin.promos.manage'],
                ],
                ],
                ['label'=>'Dokumentasi','route'=>'admin.documentations.index','match'=>'admin.documentations.*','icon'=>'images','perm'=>'admin.documentations.manage'],
                ['label'=>'Inspirasi Destinasi','route'=>'admin.destination-inspirations.index','match'=>'admin.destination-inspirations.*','icon'=>'sparkles','perm'=>'admin.destination-inspirations.manage'],
                ['label'=>'Artikel','route'=>'admin.articles.index','match'=>'admin.articles.*','icon'=>'newspaper','perm'=>'admin.articles.manage'],

                ['label'=>'Halaman Legal','route'=>'admin.legal-pages.edit','match'=>'admin.legal-pages.*','icon'=>'file-text','perm'=>'admin.legal-pages.manage'],

                ['label'=>'Halaman Document','route'=>'admin.travel-documents.edit','match'=>'admin.travel-documents.*','icon'=>'file-text','perm'=>'admin.legal-pages.manage'],

                // GROUP: Sewa Kapal
                [
                'label' => 'Sewa Kapal',
                'icon' => 'ship',
                'children' => [
                ['label'=>'Paket Sewa Kapal','route'=>'admin.ship-packages.index','match'=>'admin.ship-packages.*','icon'=>'ship','perm'=>'admin.ship-packages.manage'],
                ['label'=>'Kategori Sewa Kapal','route'=>'admin.ship-categories.index','match'=>'admin.ship-categories.*','icon'=>'tags','perm'=>'admin.ship-categories.manage'],
                ],
                ],

                // GROUP: Umrah
                [
                'label' => 'Umrah',
                'icon' => 'kaaba',
                'children' => [
                ['label'=>'Paket Umrah','route'=>'admin.umrah-packages.index','match'=>'admin.umrah-packages.*','icon'=>'kaaba','perm'=>'admin.umrah-packages.manage'],
                ['label'=>'Kategori Umrah','route'=>'admin.umrah-categories.index','match'=>'admin.umrah-categories.*','icon'=>'tags','perm'=>'admin.umrah-categories.manage'],
                ],
                ],

                // GROUP: MICE
                [
                'label' => 'MICE',
                'icon' => 'briefcase',
                'children' => [
                ['label'=>'Paket MICE','route'=>'admin.mice-packages.index','match'=>'admin.mice-packages.*','icon'=>'briefcase','perm'=>'admin.mice-packages.manage'],
                ['label'=>'Kategori MICE','route'=>'admin.mice-categories.index','match'=>'admin.mice-categories.*','icon'=>'tags','perm'=>'admin.mice-categories.manage'],
                ],
                ],

                ['label'=>'Client Logos','route'=>'admin.client-logos.index','match'=>'admin.client-logos.*','icon'=>'image','perm'=>'admin.client-logos.manage'],
                ['label'=>'Komentar Paket','route'=>'admin.reviews.index','match'=>'admin.reviews.*','icon'=>'message-square','perm'=>'admin.reviews.manage'],
                ['label'=>'SEO','route'=>'admin.seo.edit','match'=>'admin.seo.*','icon'=>'search','perm'=>'admin.seo.manage'],


                // GROUP: Users & Affiliate
                [
                'label' => 'Users & Affiliate',
                'icon' => 'users',
                'children' => [
                ['label'=>'All Users','route'=>'admin.users.index','match'=>'admin.users.*','icon'=>'users','perm'=>'admin.users.manage'],
                ['label'=>'Affiliate Requests','route'=>'admin.affiliate.requests.index','match'=>'admin.affiliate.requests.*','icon'=>'user-check','perm'=>'admin.affiliate.requests.manage'],
                ['label'=>'Affiliate Orders','route'=>'admin.affiliate.orders.index','match'=>'admin.affiliate.orders.*','icon'=>'shopping-bag','perm'=>'admin.affiliate.orders.manage'],
                ['label'=>'Affiliate Withdrawals','route'=>'admin.affiliate.withdrawals.index','match'=>'admin.affiliate.withdrawals.*','icon'=>'wallet','perm'=>'admin.affiliate.withdrawals.manage'],
                ['label'=>'Affiliate Users','route'=>'admin.users.affiliate.index','match'=>'admin.users.affiliate.*','icon'=>'users','perm'=>'admin.affiliate.users.manage'],
                ],
                ],

                // GROUP: Partners
                [
                'label' => 'Partners',
                'icon' => 'handshake',
                'children' => [
                ['label'=>'Partner Applications','route'=>'admin.partners.applications.index','match'=>'admin.partners.applications.*','icon'=>'user-check','perm'=>'admin.partners.applications.manage'],
                ['label'=>'Partner Users','route'=>'admin.partners.users.index','match'=>'admin.partners.users.*','icon'=>'users','perm'=>'admin.partners.users.manage'],
                ['label'=>'Produk Partner','route'=>'admin.partners.products.index','match'=>'admin.partners.products.*','icon'=>'package','perm'=>'admin.partners.products.manage'],
                ['label'=>'Partner Withdrawals','route'=>'admin.partner_withdrawals.index','match'=>'admin.partner_withdrawals.*','icon'=>'wallet','perm'=>'admin.partner_withdrawals.manage'],
                ],
                ],

                [
                'label' => 'Tabungan Umrah',
                'icon' => 'wallet',
                'children' => [
                ['label'=>'Verifikasi Akun','route'=>'admin.tabungan-umrah.accounts.pending','match'=>'admin.tabungan-umrah.accounts.*','icon'=>'user-check','perm'=>'admin.dashboard.view'],
                ['label'=>'Akun Terverifikasi','route'=>'admin.tabungan-umrah.accounts.verified','match'=>'admin.tabungan-umrah.accounts.verified','icon'=>'users','perm'=>'admin.dashboard.view'],
                ['label'=>'Setoran/Finance','route'=>'admin.tabungan-umrah.deposits.index','match'=>'admin.tabungan-umrah.deposits.*','icon'=>'credit-card','perm'=>'admin.dashboard.view'],
                ],
                ],
                ['label'=>'Kirim Notifikasi','route'=>'admin.notifications.create','match'=>'admin.notifications.*','icon'=>'bell','perm'=>'admin.notifications.manage'],

                // GROUP: Settings
                [
                'label' => 'Settings',
                'icon' => 'settings',
                'children' => [
                ['label'=>'General','route'=>'admin.settings.general','match'=>'admin.settings.general*','icon'=>'sliders','perm'=>'admin.settings.manage'],
                ['label'=>'Home Setting','route'=>'admin.settings.home','match'=>'admin.settings.home*','icon'=>'layout-grid','perm'=>'admin.settings.manage'],
                ['label'=>'Popup Widget','route'=>'admin.settings.popup.edit','match'=>'admin.settings.popup.*','icon'=>'message-square','perm'=>'admin.settings.manage'],
                ],
                ],

                ['label'=>'Profil','route'=>'admin.profile.edit','match'=>'admin.profile.*','icon'=>'user','perm'=>'admin.profile.manage'],
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
                        <div class="hidden sm:block text-right">
                            <div class="font-bold text-slate-900">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-slate-500">{{ auth()->user()->email }}</div>
                        </div>

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