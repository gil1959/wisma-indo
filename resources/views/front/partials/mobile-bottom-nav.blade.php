<div class="lg:hidden fixed bottom-0 left-0 right-0 z-50 pb-[max(0.75rem,env(safe-area-inset-bottom))]">
  <div class="relative z-50 max-w-7xl mx-auto px-4">
    <div class="rounded-3xl border border-slate-200 bg-white/85 backdrop-blur shadow-[0_10px_30px_rgba(15,23,42,0.10)] px-2 py-2">
      <div class="grid grid-cols-5 items-center">
        @php
        $mobileNav = [
            ['route' => 'home', 'icon' => 'home', 'label' => 'Home'],
            ['route' => 'dijual', 'icon' => 'shopping-bag', 'label' => 'Dijual'],
            ['route' => 'pasang.iklan', 'icon' => 'plus-circle', 'label' => 'Pasang'],
            ['route' => 'disewakan', 'icon' => 'key', 'label' => 'Disewakan'],
            ['route' => 'akun', 'icon' => 'user', 'label' => 'Akun'],
        ];
        @endphp

        @foreach($mobileNav as $item)
        @php
            $active = false;
            try { $active = request()->routeIs($item['route']); } catch(\Exception $e) {}
        @endphp
        <a href="{{ \Route::has($item['route']) ? route($item['route']) : '#' }}" class="flex flex-col items-center justify-center py-2 w-full">
          <div class="h-9 w-9 rounded-2xl grid place-items-center border {{ $active ? 'bg-[#0194F3]/10 border-[#0194F3]/20' : 'bg-slate-100/50 border-slate-200' }}">
            <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 {{ $active ? 'text-[#0194F3]' : 'text-slate-700' }}"></i>
          </div>
          <span class="mt-1 text-[11px] leading-none font-semibold {{ $active ? 'text-slate-900' : 'text-slate-700' }}">{{ $item['label'] }}</span>
        </a>
        @endforeach
      </div>
    </div>
  </div>
</div>