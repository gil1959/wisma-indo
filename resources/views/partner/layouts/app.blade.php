<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <title>@yield('title', 'Partner Panel') - {{ config('app.name') }}</title>

    {{-- Fonts --}}
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700;800&display=swap">

    {{-- App CSS --}}
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    @stack('styles')

    {{-- Alpine (biar sidebar dropdown kayak admin jalan) --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

{{-- Lucide --}}
<script src="https://unpkg.com/lucide@latest"></script>


    <style>
        [x-cloak]{ display:none !important; }
        body{ font-family: Nunito, ui-sans-serif, system-ui; }
        a{ text-decoration:none !important; }
    </style>
</head>

<body class="bg-slate-50 antialiased">
<div x-data="{ sidebarOpen:false }" x-init="lucide.createIcons()" class="min-h-screen">

    {{-- MOBILE OVERLAY --}}
    <div x-show="sidebarOpen" x-cloak
         class="fixed inset-0 bg-black/40 z-40 lg:hidden"
         @click="sidebarOpen=false"></div>

    @php
    $nav = [
        ['label'=>'Dashboard','route'=>'partner.dashboard','match'=>'partner.dashboard','icon'=>'layout-dashboard'],
        ['label'=>'Orders','route'=>'partner.orders.index','match'=>'partner.orders.*','icon'=>'shopping-bag'],
        ['label'=>'Withdraw','route'=>'partner.withdraw.index','match'=>'partner.withdraw.*','icon'=>'wallet'],
    ];

    if (auth()->user()->partner_type === 'agency_paket_tour') {
        // GROUP: Paket Wisata
        $nav[] = [
            'label' => 'Paket Wisata',
            'icon'  => 'map',
            'children' => [
                ['label'=>'Paket Tour Wisata','route'=>'partner.tour-packages.index','match'=>'partner.tour-packages.*','icon'=>'map'],
                ['label'=>'Kategori Tour','route'=>'partner.tour-categories.index','match'=>'partner.tour-categories.*','icon'=>'tags'],
            ],
        ];

    }

   if (auth()->user()->partner_type === 'agency_rental_mobil') {
       
        // GROUP: Rental
        $nav[] = [
            'label' => 'Rental',
            'icon'  => 'car',
            'children' => [
                ['label'=>'Paket Travel (Rent Car)','route'=>'partner.rent-car-packages.index','match'=>'partner.rent-car-packages.*','icon'=>'car'],
                ['label'=>'Kategori Rental','route'=>'partner.rent-car-categories.index','match'=>'partner.rent-car-categories.*','icon'=>'tags'],
            ],
        ];
    }

   if (auth()->user()->partner_type === 'agency_restoran') {
        // GROUP: Restoran
        $nav[] = [
            'label' => 'Restoran',
            'icon'  => 'utensils',
            'children' => [
                ['label'=>'Paket Restoran','route'=>'partner.restoran-packages.index','match'=>'partner.restoran-packages.*','icon'=>'utensils'],
            ],
        ];
    }

   if (auth()->user()->partner_type === 'agency_hotel_vila') {
        // GROUP: Hotel/Vila
        $nav[] = [
            'label' => 'Hotel/Vila',
            'icon'  => 'building',
            'children' => [
                ['label'=>'Paket Hotel/Vila','route'=>'partner.hotel-packages.index','match'=>'partner.hotel-packages.*','icon'=>'building'],
            ],
        ];
    }

    // FIX: route profile partner (punya lu tadi salah: user.profile.edit)
    
    $nav[] = ['label'=>'Profile','route'=>'partner.profile.edit','match'=>'partner.profile.*','icon'=>'user'];
@endphp


    {{-- WRAPPER --}}
    <div class="min-h-screen flex">

        {{-- SIDEBAR --}}
        <aside
            class="fixed z-50 inset-y-0 left-0 w-72 bg-white border-r border-slate-200 transform transition lg:translate-x-0 lg:static lg:inset-auto"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="h-16 px-5 flex items-center justify-between border-b border-slate-200">
                <div class="flex items-center gap-3 min-w-0">
                    <img
                        src="{{ $siteSettings['site_logo'] ?? asset('images/logo.png') }}"
                        alt="{{ $siteSettings['seo_site_title'] ?? 'Bintang Wisata' }}"
                        class="h-9 w-auto object-contain"
                    />

                    <div class="min-w-0">
                        <div class="font-extrabold text-slate-900 truncate">Partner Panel</div>
                        <div class="text-xs text-slate-500 truncate">{{ $siteSettings['seo_site_title'] ?? 'Bintang Wisata' }}</div>
                    </div>
                </div>

                <button class="lg:hidden h-9 w-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 grid place-items-center"
                        @click="sidebarOpen=false" aria-label="Tutup menu">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="p-4 space-y-1">
    @foreach($nav as $n)

        {{-- CASE 1: GROUP / HAS CHILDREN --}}
        @if(isset($n['children']) && is_array($n['children']))
            @php
                $childActive = false;
                foreach($n['children'] as $c){
                    if(request()->routeIs($c['match'])){
                        $childActive = true;
                        break;
                    }
                }
            @endphp

            <div x-data="{ open: {{ $childActive ? 'true' : 'false' }} }" class="space-y-1">

                {{-- Parent button --}}
                <button type="button"
                    @click="open = !open"
                    class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl border transition
                           {{ $childActive ? 'text-slate-900' : 'text-slate-700 hover:bg-slate-50' }}"
                    style="{{ $childActive ? 'background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22);' : 'border-color:transparent;' }}"
                >
                    <span class="flex items-center gap-3 min-w-0">
                        <span class="h-9 w-9 rounded-xl grid place-items-center border shrink-0"
                              style="{{ $childActive ? 'background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22);' : 'background:rgba(148,163,184,0.10);border-color:rgba(148,163,184,0.20);' }}">
                            <i data-lucide="{{ $n['icon'] }}" class="w-5 h-5"
                               style="{{ $childActive ? 'color:#0194F3;' : 'color:#64748b;' }}"></i>
                        </span>

                        <span class="font-extrabold text-sm truncate">{{ $n['label'] }}</span>
                    </span>

                    <span class="shrink-0">
                        <i data-lucide="chevron-down"
                           class="w-4 h-4 transition-transform"
                           :class="open ? 'rotate-180' : ''"
                           style="{{ $childActive ? 'color:#0194F3;' : 'color:#94a3b8;' }}"
                        ></i>
                    </span>
                </button>

                {{-- Children --}}
                <div x-show="open" x-collapse class="pl-3">
                    <div class="space-y-1 border-l border-slate-200 ml-3 pl-3">
                        @foreach($n['children'] as $c)
                            @php $active = request()->routeIs($c['match']); @endphp

                            <a href="{{ route($c['route']) }}"
                               @click="sidebarOpen=false"
                               class="flex items-center justify-between gap-3 px-3 py-2 rounded-xl border transition
                                      {{ $active ? 'text-slate-900' : 'text-slate-700 hover:bg-slate-50' }}"
                               style="{{ $active ? 'background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22);' : 'border-color:transparent;' }}"
                            >
                                <span class="flex items-center gap-3 min-w-0">
                                    <span class="h-8 w-8 rounded-xl grid place-items-center border shrink-0"
                                          style="{{ $active ? 'background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22);' : 'background:rgba(148,163,184,0.08);border-color:rgba(148,163,184,0.16);' }}">
                                        <i data-lucide="{{ $c['icon'] ?? 'dot' }}" class="w-4 h-4"
                                           style="{{ $active ? 'color:#0194F3;' : 'color:#64748b;' }}"></i>
                                    </span>

                                    <span class="font-extrabold text-sm truncate">{{ $c['label'] }}</span>
                                </span>

                                <span class="text-xs font-extrabold shrink-0"
                                      style="{{ $active ? 'color:#0194F3;' : 'color:#94a3b8;' }}">
                                    →
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

        {{-- CASE 2: NORMAL ITEM --}}
        @else
            @php $active = request()->routeIs($n['match'] ?? ''); @endphp

            <a href="{{ route($n['route']) }}"
               @click="sidebarOpen=false"
               class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl border transition
                      {{ $active ? 'text-slate-900' : 'text-slate-700 hover:bg-slate-50' }}"
               style="{{ $active ? 'background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22);' : 'border-color:transparent;' }}"
            >
                <span class="flex items-center gap-3 min-w-0">
                    <i data-lucide="{{ $n['icon'] }}" class="w-5 h-5" style="{{ $active ? 'color:#0194F3;' : 'color:#64748b;' }}"></i>
                    <span class="font-extrabold text-sm truncate">{{ $n['label'] }}</span>
                </span>

                <span class="text-xs font-extrabold" style="{{ $active ? 'color:#0194F3;' : 'color:#94a3b8;' }}">→</span>
            </a>
        @endif

    @endforeach

    <div class="pt-3 mt-3 border-t border-slate-200">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl border border-transparent hover:bg-red-50 transition text-red-600">
                <span class="flex items-center gap-3 min-w-0">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    <span class="font-extrabold text-sm truncate">Logout</span>
                </span>
                <span class="text-xs font-extrabold">→</span>
            </button>
        </form>
    </div>
</div>

        </aside>

        {{-- MAIN --}}
        <div class="flex-1 min-w-0">

            {{-- TOPBAR (ngikut user layout) --}}
            <header class="h-16 bg-white/70 backdrop-blur border-b border-slate-200">
                <div class="h-full px-4 lg:px-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <button
                            class="lg:hidden h-9 w-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 grid place-items-center"
                            @click="sidebarOpen=true"
                            aria-label="Buka menu"
                        >
                            <i data-lucide="menu" class="w-5 h-5"></i>
                        </button>

                        <div class="leading-tight">
                            <div class="text-xs text-slate-500">@yield('page-subtitle', 'Welcome')</div>
                            <div class="text-sm font-extrabold text-slate-900">@yield('page-title', 'Partner Dashboard')</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('home') }}"
                           class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 transition">
                            <i data-lucide="home" class="w-4 h-4" style="color:#0194F3;"></i>
                            <span class="text-sm font-extrabold text-slate-900">Back to Site</span>
                        </a>

                        @php
    $unread = auth()->user()->unreadNotifications()->count();
    $latest = auth()->user()->notifications()->latest()->limit(5)->get();
@endphp

<div x-data="{ open:false }" class="relative">
    <button @click="open=!open"
            class="relative h-10 w-10 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 grid place-items-center">
        <i data-lucide="bell" class="w-5 h-5"></i>
        @if($unread > 0)
            <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full text-[10px] grid place-items-center font-extrabold text-white"
                  style="background:#0194F3;">
                {{ $unread }}
            </span>
        @endif
    </button>

    <div x-show="open" x-cloak @click.outside="open=false"
         class="absolute right-0 mt-2 w-96 rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden z-50">
        <div class="px-4 py-3 border-b border-slate-200">
            <div class="font-extrabold text-slate-900">Notifikasi</div>
            <div class="text-xs text-slate-500">Terbaru</div>
        </div>

        <div class="max-h-80 overflow-auto">
            @forelse($latest as $n)
                <div class="px-4 py-3 border-b border-slate-100">
                    <div class="text-sm font-extrabold text-slate-900">
                        {{ data_get($n->data,'title','Notifikasi') }}
                    </div>
                    <div class="text-xs font-semibold text-slate-600 mt-1">
                        {{ data_get($n->data,'message','') }}
                    </div>

                    <div class="mt-2 flex items-center gap-3">
                        <a href="{{ data_get($n->data,'url', route('partner.notifications.index')) }}"
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
            <a href="{{ route('partner.notifications.index') }}"
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
                                <div class="text-sm font-extrabold text-slate-900">{{ auth()->user()->name }}</div>
                            </div>

                            <div class="h-10 w-10 rounded-xl border border-slate-200 bg-white grid place-items-center font-extrabold text-slate-900">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- CONTENT --}}
            <main class="p-4 lg:p-6">
                @if(session('success'))
                    <div class="mb-4 rounded-2xl border border-green-200 bg-green-50 p-4 text-green-800">
                        <div class="font-extrabold">Sukses</div>
                        <div class="text-sm mt-1">{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800">
                        <div class="font-extrabold">Gagal</div>
                        <div class="text-sm mt-1">{{ session('error') }}</div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</div>

<script src="{{ mix('js/app.js') }}"></script>
<script>
(async function(){
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
        // sengaja diem; kalau gagal push, notif in-app tetap jalan
    }
})();
</script>
@stack('scripts')
</body>
</html>
