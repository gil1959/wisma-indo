<div class="flex min-h-screen items-center justify-center px-4 py-12">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 border border-slate-100">
        <div class="flex items-center justify-center gap-3 mb-8">
            <img src="{{ isset($siteSettings['site_logo']) && $siteSettings['site_logo'] != '' ? asset($siteSettings['site_logo']) : asset('images/logo.png') }}"
                 alt="{{ $siteSettings['brand_name'] ?? 'Wismaindo' }}"
                 class="h-10 lg:h-12 w-auto object-contain">
            <span class="text-xl font-bold text-slate-800">{{ $siteSettings['brand_name'] ?? 'Wismaindo' }}</span>
        </div>

        {{ $slot }}
    </div>
</div>
