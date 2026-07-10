<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <title>@yield('title', 'User Panel') - {{ config('app.name') }}</title>

    {{-- Fonts --}}
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700;800&display=swap">

    {{-- App CSS (Tailwind + design system Azure) --}}
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

<body class="bg-slate-50 antialiased">

    <div x-data="{ sidebarOpen:false }" x-init="if (window.lucide) lucide.createIcons()" class="min-h-screen">

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
                {{-- BRAND (logo ikut navbar/front) --}}
                <div class="h-16 px-5 flex items-center justify-between border-b border-slate-200">
                    <div class="flex items-center gap-3 min-w-0">
                        <img
                            src="{{ $siteSettings['site_logo'] ?? asset('images/logo.png') }}"
                            alt="{{ $siteSettings['seo_site_title'] ?? 'Bintang Wisata' }}"
                            class="h-9 w-auto object-contain" />

                        <div class="min-w-0">
                            <div class="font-extrabold text-slate-900 leading-tight truncate">User Panel</div>
                            <div class="text-xs text-slate-500 -mt-0.5 truncate">{{ $siteSettings['seo_site_title'] ?? 'Bintang Wisata' }}</div>
                        </div>
                    </div>

                    <button
                        @click="sidebarOpen=false"
                        class="lg:hidden h-9 w-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 grid place-items-center"
                        aria-label="Tutup menu">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                {{-- NAV (ngikut admin: font-bold, active bg azure translucent) --}}
                <nav class="px-3 py-3 space-y-1 overflow-y-auto" style="max-height:calc(100vh - 4rem)">
                    @php
                    $isEn = app()->getLocale() === 'en';

                    $nav = [
                    ['label'=> ($isEn ? 'Dashboard' : 'Dashboard'),'route'=>'user.dashboard','match'=>'user.dashboard','icon'=>'layout-dashboard'],
                    ['label'=> ($isEn ? 'Orders' : 'Pesanan'),'route'=>'user.orders','match'=>'user.orders.*','icon'=>'receipt'],
                    ['label'=> ($isEn ? 'Umrah Savings' : 'Tabungan Umrah'),'route'=>'user.tabungan-umrah.index','match'=>'user.tabungan-umrah.*','icon'=>'wallet'],

                    [
                    'label' => ($isEn ? 'Affiliate' : 'Afiliasi'),
                    'icon' => 'badge-percent',
                    'match' => 'user.affiliate.*',
                    'children' => [
                    ['label'=> ($isEn ? 'Commission' : 'Komisi'),'route'=>'user.affiliate.commission','match'=>'user.affiliate.commission','icon'=>'percent'],
                    ['label'=> ($isEn ? 'Links' : 'Tautan'),'route'=>'user.affiliate.links','match'=>'user.affiliate.links*','icon'=>'link'],
                    ['label'=> ($isEn ? 'Coupons' : 'Kupon'),'route'=>'user.affiliate.coupons','match'=>'user.affiliate.coupons*','icon'=>'ticket'],
                    ['label'=> ($isEn ? 'Orders' : 'Pesanan'),'route'=>'user.affiliate.orders','match'=>'user.affiliate.orders','icon'=>'shopping-bag'],
                    ['label'=> ($isEn ? 'Withdraw' : 'Tarik Dana'),'route'=>'user.withdrawals','match'=>'user.withdrawals*','icon'=>'wallet'],
                    ],
                    ],

                    ['label'=> ($isEn ? 'Profile' : 'Profil'),'route'=>'user.profile.edit','match'=>'user.profile.*','icon'=>'user'],
                    ];
                    @endphp


                    @foreach($nav as $n)
                    @php
                    $hasChildren = isset($n['children']) && is_array($n['children']) && count($n['children']) > 0;
                    $active = request()->routeIs($n['match'] ?? ($n['route'] ?? ''));
                    $childActive = false;

                    if ($hasChildren) {
                    foreach ($n['children'] as $c) {
                    if (request()->routeIs($c['match'] ?? ($c['route'] ?? ''))) {
                    $childActive = true;
                    break;
                    }
                    }
                    }
                    @endphp

                    @if($hasChildren)
                    <div x-data="{ open: {{ $childActive ? 'true' : 'false' }} }" class="space-y-1">
                        <button type="button"
                            @click="open = !open"
                            class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl border transition
                       {{ $childActive ? 'text-slate-900' : 'text-slate-700 hover:bg-slate-50' }}"
                            style="{{ $childActive ? 'background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22);' : 'border-color:transparent;' }}">
                            <span class="flex items-center gap-3 min-w-0">
                                <span class="h-9 w-9 rounded-xl grid place-items-center border shrink-0"
                                    style="{{ $childActive ? 'background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22);' : 'background:rgba(148,163,184,0.10);border-color:rgba(148,163,184,0.20);' }}">

                                    <i data-lucide="{{ $n['icon'] }}" class="w-5 h-5"
                                        style="{{ $childActive ? 'color:#0194F3;' : 'color:#64748b;' }}"></i>
                                </span>

                                <span class="font-bold text-sm truncate">{{ $n['label'] }}</span>
                            </span>

                            <span class="text-xs font-extrabold shrink-0" style="{{ $childActive ? 'color:#0194F3;' : 'color:#94a3b8;' }}">
                                <span x-show="!open">+</span>
                                <span x-show="open">−</span>
                            </span>
                        </button>

                        <div x-show="open" x-collapse class="pl-3 space-y-1">
                            @foreach($n['children'] as $c)
                            @php
                            $cActive = request()->routeIs($c['match'] ?? ($c['route'] ?? ''));
                            @endphp

                            <a href="{{ route($c['route']) }}"
                                @click="sidebarOpen=false"
                                class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl border transition
                              {{ $cActive ? 'text-slate-900' : 'text-slate-700 hover:bg-slate-50' }}"
                                style="{{ $cActive ? 'background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22);' : 'border-color:transparent;' }}">
                                <span class="flex items-center gap-3 min-w-0">
                                    <span class="h-9 w-9 rounded-xl grid place-items-center border shrink-0"
                                        style="{{ $cActive ? 'background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22);' : 'background:rgba(148,163,184,0.10);border-color:rgba(148,163,184,0.20);' }}">
                                        <i data-lucide="{{ $c['icon'] }}" class="w-5 h-5"
                                            style="{{ $cActive ? 'color:#0194F3;' : 'color:#64748b;' }}"></i>
                                    </span>

                                    <span class="font-bold text-sm truncate">{{ $c['label'] }}</span>
                                </span>

                                <span class="text-xs font-extrabold shrink-0" style="{{ $cActive ? 'color:#0194F3;' : 'color:#94a3b8;' }}">→</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <a href="{{ route($n['route']) }}"
                        @click="sidebarOpen=false"
                        class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl border transition
                  {{ $active ? 'text-slate-900' : 'text-slate-700 hover:bg-slate-50' }}"
                        style="{{ $active ? 'background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22);' : 'border-color:transparent;' }}">
                        <span class="flex items-center gap-3 min-w-0">
                            <span class="h-9 w-9 rounded-xl grid place-items-center border shrink-0"
                                style="{{ $active ? 'background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22);' : 'background:rgba(148,163,184,0.10);border-color:rgba(148,163,184,0.20);' }}">
                                <i data-lucide="{{ $n['icon'] }}" class="w-5 h-5"
                                    style="{{ $active ? 'color:#0194F3;' : 'color:#64748b;' }}"></i>
                            </span>

                            <span class="font-bold text-sm truncate">{{ $n['label'] }}</span>
                        </span>

                        <span class="text-xs font-extrabold shrink-0" style="{{ $active ? 'color:#0194F3;' : 'color:#94a3b8;' }}">→</span>
                    </a>
                    @endif
                    @endforeach


                    <div class="pt-3">
                        <div class="h-px bg-slate-200"></div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="pt-2">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl border transition text-red-600 hover:bg-red-50"
                            style="border-color:transparent;">
                            <span class="flex items-center gap-3">
                                <span class="h-9 w-9 rounded-xl grid place-items-center border"
                                    style="background:rgba(239,68,68,0.08);border-color:rgba(239,68,68,0.18);">
                                    <i data-lucide="log-out" class="w-5 h-5" style="color:#ef4444;"></i>
                                </span>
                                <span class="font-bold text-sm">Logout</span>
                            </span>
                            <span class="text-xs font-extrabold">→</span>
                        </button>
                    </form>
                </nav>
            </aside>

            {{-- MAIN --}}
            <div class="flex-1 flex flex-col min-w-0">

                {{-- TOPBAR --}}
                <header class="relative z-50 h-16 bg-white/70 backdrop-blur border-b border-slate-200">
                    <div class="h-full px-4 lg:px-5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <button
                                class="lg:hidden h-9 w-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 grid place-items-center"
                                @click="sidebarOpen=true"
                                aria-label="Buka menu">
                                <i data-lucide="menu" class="w-5 h-5"></i>
                            </button>

                            <div class="leading-tight">
                                <div class="text-xs text-slate-500">@yield('page-subtitle', 'Welcome')</div>
                                <div class="text-sm font-extrabold text-slate-900">@yield('page-title', 'User Dashboard')</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('home') }}"
                                class="hidden sm:inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-bold bg-white border border-slate-200 hover:bg-slate-50 transition">
                                <i data-lucide="home" class="w-4 h-4" style="color:#0194F3;"></i>
                                Back to Site
                            </a>

                            @php
                            $unreadNotifCount = auth()->user()->unreadNotifications()->count();
                            $latestNotifs = auth()->user()->notifications()->latest()->limit(5)->get();
                            @endphp

                            {{-- NOTIF DROPDOWN (theme: slate + azure #0194F3) --}}
                            <div x-data="{ open:false }" class="relative">
                                <button
                                    type="button"
                                    @click="open=!open"
                                    class="relative h-10 w-10 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 grid place-items-center"
                                    aria-label="Notifikasi">
                                    <i data-lucide="bell" class="w-5 h-5" style="color:#0f172a;"></i>

                                    @if($unreadNotifCount > 0)
                                    <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full text-[10px] grid place-items-center font-extrabold text-white"
                                        style="background:#0194F3;">
                                        {{ $unreadNotifCount }}
                                    </span>
                                    @endif
                                </button>

                                <div
                                    x-show="open"
                                    x-cloak
                                    @click.outside="open=false"
                                    class="absolute right-0 mt-2 w-96 rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden z-[60]">

                                    <div class="px-4 py-3 border-b border-slate-200">
                                        <div class="font-extrabold text-slate-900">Notifikasi</div>
                                        <div class="text-xs text-slate-500">Terbaru</div>
                                    </div>

                                    <div class="max-h-80 overflow-auto">
                                        @forelse($latestNotifs as $n)
                                        <div class="px-4 py-3 border-b border-slate-100">
                                            <div class="text-sm font-extrabold text-slate-900">
                                                {{ data_get($n->data,'title','Notifikasi') }}
                                            </div>
                                            <div class="text-xs font-semibold text-slate-600 mt-1">
                                                {{ data_get($n->data,'message','') }}
                                            </div>

                                            <div class="mt-2 flex items-center gap-3">
                                                <a href="{{ data_get($n->data,'url', route('user.notifications.index')) }}"
                                                    class="text-xs font-extrabold hover:underline"
                                                    style="color:#0194F3;">
                                                    Buka
                                                </a>

                                                @if(is_null($n->read_at))
                                                <form method="POST" action="{{ route('notifications.markRead',$n->id) }}">
                                                    @csrf
                                                    <button class="text-xs font-extrabold text-slate-700 hover:underline">
                                                        Tandai dibaca
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                        @empty
                                        <div class="px-4 py-6 text-sm font-semibold text-slate-500">Belum ada notifikasi.</div>
                                        @endforelse
                                    </div>

                                    <div class="px-4 py-3">
                                        <a href="{{ route('user.notifications.index') }}"
                                            class="text-sm font-extrabold hover:underline"
                                            style="color:#0194F3;">
                                            Lihat semua
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="text-right hidden sm:block">
                                    <div class="text-xs text-slate-500">Signed in as</div>
                                    <div class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</div>
                                </div>

                                <div class="h-10 w-10 rounded-2xl grid place-items-center border"
                                    style="background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22)">
                                    <span class="font-extrabold" style="color:#0194F3;">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                </header>

                {{-- CONTENT --}}
                <main class="flex-1 p-4 lg:p-5">
                    <div class="mx-auto w-full max-w-[1160px]">
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

    <script>
        (async function() {
            if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;

            try {
                const reg = await navigator.serviceWorker.register('/sw.js');

                const permission = await Notification.requestPermission();
                if (permission !== 'granted') return;

                const vapidPublicKey = @json(env('VAPID_PUBLIC_KEY'));
                if (!vapidPublicKey) return;

                function urlBase64ToUint8Array(base64String) {
                    const padding = '='.repeat((4 - base64String.length % 4) % 4);
                    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
                    const rawData = window.atob(base64);
                    const outputArray = new Uint8Array(rawData.length);
                    for (let i = 0; i < rawData.length; ++i) outputArray[i] = rawData.charCodeAt(i);
                    return outputArray;
                }

                const sub = await reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
                });

                await fetch(@json(route('push.subscribe')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token())
                    },
                    body: JSON.stringify(sub)
                });
            } catch (e) {
                // gagal push subscribe = biarin, notif in-app tetap jalan
            }
        })();
    </script>

</body>

</html>