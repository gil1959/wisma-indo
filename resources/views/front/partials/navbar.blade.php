<header
  x-data="{ scrolled:false, pasangIklanModal: false }"
  x-init="window.addEventListener('scroll', ()=> scrolled = window.scrollY > 10)"
  @open-pasang-iklan.window="pasangIklanModal = true"
  class="sticky top-0 z-50 transition"
  :class="scrolled ? 'bg-white/80 backdrop-blur border-b border-slate-200 shadow-sm' : 'bg-white/90 backdrop-blur'">
  <div class="max-w-7xl mx-auto px-4 py-2 lg:py-4 flex items-center justify-between gap-3">

    {{-- BRAND --}}
    <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
      <img
        src="{{ isset($siteSettings['site_logo']) && $siteSettings['site_logo'] != '' ? asset($siteSettings['site_logo']) : asset('images/logo.png') }}"
        alt="{{ $siteSettings['brand_name'] ?? 'Rumaindo' }}"
        class="h-8 lg:h-9 object-contain shrink-0" />
      <span class="font-bold text-xl text-slate-800 tracking-tight">{{ $siteSettings['brand_name'] ?? 'Rumaindo' }}</span>
    </a>

    {{-- DESKTOP NAV --}}
    <nav class="hidden lg:flex flex-1 items-center justify-center gap-2 flex-nowrap whitespace-nowrap">
      @php
        $isDashboard = request()->is('akun*') || request()->is('iklan-saya*') || request()->is('top-up*') || request()->is('transaksi*') || request()->is('iklan-favorit*');

        if ($isDashboard) {
            $nav = [
                ['label' => 'Properti Terbaru', 'route' => 'properti'],
                ['label' => 'Iklan Favorit', 'route' => 'iklan.favorit'],
                ['label' => 'Iklan Saya', 'route' => 'iklan.saya'],
                ['label' => 'Top Up', 'route' => 'topup'],
                ['label' => 'Transaksi', 'route' => 'transaksi'],
            ];
        } else {
            $nav = [
                ['label' => 'Dijual', 'route' => 'dijual'],
                ['label' => 'Disewakan', 'route' => 'disewakan'],
                ['label' => 'Properti Terbaru', 'route' => 'properti'],
                ['label' => 'Cari Barang & Jasa', 'route' => 'barangjasa'],
                ['label' => 'Simulasi Nilai Properti', 'route' => 'simulasi'],
            ];
        }
      @endphp

      @foreach($nav as $n)
        @php
            // Simple active check logic
            $active = false;
            try { $active = request()->routeIs($n['route']); } catch(\Exception $e) {}
        @endphp
        <a
          href="{{ \Route::has($n['route']) ? route($n['route']) : '#' }}"
          class="group relative px-3 py-2 rounded-xl text-sm font-semibold transition hover:bg-slate-50 flex items-center gap-2 whitespace-nowrap
                 {{ $active ? 'text-slate-900' : 'text-slate-700 hover:text-slate-900' }}">
          <span>{{ $n['label'] }}</span>
          <span
            class="absolute left-3 right-3 -bottom-0.5 h-[2px] rounded-full transition-all duration-300
                   {{ $active ? 'opacity-100' : 'opacity-0 group-hover:opacity-100' }}"
            style="background:#0194F3;"></span>
        </a>
      @endforeach
    </nav>

    {{-- RIGHT ACTIONS --}}
    <div class="flex items-center gap-4 shrink-0">

      {{-- PASANG IKLAN BUTTON --}}
      @auth
      <button @click="pasangIklanModal = true"
        class="hidden lg:inline-flex items-center gap-2 px-5 py-2 rounded-full text-sm font-bold text-white shadow hover:opacity-90 transition"
        style="background: #0194F3;">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
        Pasang Iklan
      </button>
      @else
      <a href="{{ route('login') }}"
        class="hidden lg:inline-flex items-center gap-2 px-5 py-2 rounded-full text-sm font-bold text-white shadow hover:opacity-90 transition"
        style="background: #0194F3;">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
        Pasang Iklan
      </a>
      @endauth

      {{-- MAIL/NOTIFICATION ICON --}}
      @auth
      @php
          $unreadCount = auth()->user()->unreadNotifications->count();
      @endphp
      <a href="{{ route('user.notifications.index') }}" class="relative p-2 text-slate-600 hover:text-[#0194F3] transition">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white animate-pulse"></span>
        @endif
      </a>
      @endauth

      {{-- PROFILE DROPDOWN --}}
      <div x-data="{ openProfile: false }" class="relative">
        <button @click="openProfile = !openProfile" @click.away="openProfile = false" class="p-2 text-slate-600 hover:text-slate-900 transition focus:outline-none">
          <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center overflow-hidden border border-slate-300">
            @if(auth()->check() && auth()->user()->avatar)
                <img src="{{ asset(auth()->user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
            @else
                <i data-lucide="user" class="w-5 h-5 text-slate-500"></i>
            @endif
          </div>
        </button>

        {{-- Dropdown Menu --}}
        <div x-show="openProfile" x-transition x-cloak
          class="absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden z-50">
          
          @auth
          <div class="py-1">
            <a href="{{ \Route::has('akun') ? route('akun') : '#' }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 font-medium">Akun Saya</a>
            <a href="{{ route('user.notifications.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 font-medium flex justify-between items-center">
                Notifikasi
                @if($unreadCount > 0)
                    <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                @endif
            </a>
            <a href="{{ \Route::has('iklan.saya') ? route('iklan.saya') : '#' }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 font-medium">Iklan Saya</a>
          </div>
          @endauth
          <div class="border-t border-slate-100 py-1">
            <a href="{{ \Route::has('articles') ? route('articles') : '#' }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 font-medium">Artikel Inspirasi</a>
          </div>
          <div class="border-t border-slate-100 py-1">
            @auth
              <form method="POST" action="{{ route('logout') }}" class="m-0">
                  @csrf
                  <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-bold">KELUAR</button>
              </form>
            @else
              <a href="{{ route('login') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 font-bold text-center bg-slate-50 border-t border-slate-100 mt-1">MASUK / DAFTAR</a>
            @endauth
          </div>
        </div>
      </div>

    </div>
  </div>

  {{-- MODAL PASANG IKLAN --}}
  <template x-teleport="body">
    <div x-show="pasangIklanModal" style="display: none;" class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
      
      {{-- OVERLAY --}}
      <div x-show="pasangIklanModal" 
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="opacity-0"
           x-transition:enter-end="opacity-100"
           x-transition:leave="transition ease-in duration-200"
           x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0"
           class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" 
           @click="pasangIklanModal = false"></div>
      
      {{-- MODAL CONTENT --}}
      <div x-show="pasangIklanModal" 
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="opacity-0 translate-y-8 scale-95"
           x-transition:enter-end="opacity-100 translate-y-0 scale-100"
           x-transition:leave="transition ease-in duration-200"
           x-transition:leave-start="opacity-100 translate-y-0 scale-100"
           x-transition:leave-end="opacity-0 translate-y-8 scale-95"
           class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl mx-auto overflow-hidden z-10"
           @click.stop>
        
        <div x-data="{ selectedCategory: 'properti' }">
          <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <h3 class="text-xl font-bold text-slate-800">Mau pasang iklan apa?</h3>
            <button @click="pasangIklanModal = false" class="p-2 text-slate-400 hover:text-slate-600 transition bg-slate-100 rounded-full hover:bg-slate-200 flex items-center justify-center">
              <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
          </div>

          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              
              {{-- Kategori: Properti --}}
              <div @click="selectedCategory = 'properti'"
                   class="border-2 rounded-2xl p-5 cursor-pointer transition-all duration-200 hover:shadow-md"
                   :class="selectedCategory === 'properti' ? 'border-[#0194F3] bg-[#0194F3]/5' : 'border-slate-200 hover:border-[#0194F3]/50 bg-white'">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-[#0194F3] flex items-center justify-center mb-4">
                  <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <h4 class="font-bold text-slate-800 text-lg mb-1">Properti</h4>
                <p class="text-xs font-semibold text-[#0194F3] mb-2 uppercase tracking-wide">Iklan Hunian & Tanah</p>
                <p class="text-sm text-slate-500 leading-relaxed">Jual atau sewa rumah, apartemen, tanah, dll.</p>
              </div>

              {{-- Kategori: Barang --}}
              <div @click="selectedCategory = 'barang'"
                   class="border-2 rounded-2xl p-5 cursor-pointer transition-all duration-200 hover:shadow-md"
                   :class="selectedCategory === 'barang' ? 'border-[#0194F3] bg-[#0194F3]/5' : 'border-slate-200 hover:border-[#0194F3]/50 bg-white'">
                <div class="w-12 h-12 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center mb-4">
                  <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                </div>
                <h4 class="font-bold text-slate-800 text-lg mb-1">Barang</h4>
                <p class="text-xs font-semibold text-orange-600 mb-2 uppercase tracking-wide">Perlengkapan</p>
                <p class="text-sm text-slate-500 leading-relaxed">Jual barang elektronik, otomotif, perabotan.</p>
              </div>

              {{-- Kategori: Jasa --}}
              <div @click="selectedCategory = 'jasa'"
                   class="border-2 rounded-2xl p-5 cursor-pointer transition-all duration-200 hover:shadow-md"
                   :class="selectedCategory === 'jasa' ? 'border-[#0194F3] bg-[#0194F3]/5' : 'border-slate-200 hover:border-[#0194F3]/50 bg-white'">
                <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center mb-4">
                  <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </div>
                <h4 class="font-bold text-slate-800 text-lg mb-1">Jasa</h4>
                <p class="text-xs font-semibold text-purple-600 mb-2 uppercase tracking-wide">Layanan Profesional</p>
                <p class="text-sm text-slate-500 leading-relaxed">Tawarkan jasa profesional atau keahlian Anda.</p>
              </div>

            </div>
          </div>

          <div class="px-6 py-5 border-t border-slate-100 bg-slate-50 flex justify-end">
            <button type="button" @click="window.location.href = '{{ \Route::has('pasang.iklan') ? route('pasang.iklan') : '#' }}?kategori=' + selectedCategory" 
              class="px-6 py-3 bg-[#0194F3] hover:bg-blue-600 text-white font-bold rounded-xl shadow-md transition flex items-center gap-2">
              Lanjutkan
              <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </button>
          </div>
        </div>

      </div>
    </div>
  </template>
</header>