<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('akun') }}">
                        <x-application-logo class="block h-10 w-auto fill-current text-gray-600" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('akun')" :active="request()->routeIs('akun')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Notifications + Settings -->
<div class="hidden sm:flex sm:items-center sm:ml-6 space-x-3">
    @php
        $unread = Auth::user()->unreadNotifications()->count();
        $latest = Auth::user()->notifications()->latest()->limit(5)->get();
    @endphp

    <x-dropdown align="right" width="96">
        <x-slot name="trigger">
            <button class="relative inline-flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 bg-white hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0h6z"/>
                </svg>

                @if($unread > 0)
                    <span class="absolute -top-1 -right-1 inline-flex items-center justify-center text-[10px] font-bold text-white bg-blue-600 rounded-full min-w-[18px] h-[18px] px-1">
                        {{ $unread }}
                    </span>
                @endif
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="px-4 py-3 border-b border-gray-100">
                <div class="font-bold text-gray-800">Notifikasi</div>
                <div class="text-xs text-gray-500">Terbaru</div>
            </div>

            <div class="max-h-80 overflow-auto">
                @forelse($latest as $n)
                    <div class="px-4 py-3 border-b border-gray-100">
                        <div class="text-sm font-semibold text-gray-800">
                            {{ data_get($n->data,'title','Notifikasi') }}
                        </div>
                        <div class="text-xs text-gray-600 mt-1">
                            {{ data_get($n->data,'message','') }}
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <a href="{{ data_get($n->data,'url', route('user.notifications.index')) }}"
                               class="text-xs font-bold text-blue-600 hover:underline">
                                Buka
                            </a>
                            @if(is_null($n->read_at))
                                <form method="POST" action="{{ route('notifications.markRead',$n->id) }}">
                                    @csrf
                                    <button class="text-xs font-bold text-gray-700 hover:underline">Tandai dibaca</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-6 text-sm text-gray-500">Belum ada notifikasi.</div>
                @endforelse
            </div>

            <div class="px-4 py-3">
                <a href="{{ route('user.notifications.index') }}" class="text-sm font-bold text-blue-600 hover:underline">
                    Lihat semua
                </a>
            </div>
        </x-slot>
    </x-dropdown>

    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                <div>{{ Auth::user()->name }}</div>
                <div class="ml-1">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </button>
        </x-slot>

        <x-slot name="content">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                    {{ __('Log Out') }}
                </x-dropdown-link>
            </form>
        </x-slot>
    </x-dropdown>
</div>


            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('akun')" :active="request()->routeIs('akun')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
