@php
$shipPromoEnabled = ($siteSettings['home_ship_promo_enabled'] ?? '1') === '1';
$isEn = app()->getLocale() === 'en';
@endphp

@if($shipPromoEnabled && isset($promoShips) && $promoShips->count() > 0)
<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 py-10 lg:py-14">

        <div class="flex items-end justify-between gap-4" data-aos="fade-up">
            <div>
                <div class="pill pill-azure">
                    <i data-lucide="ship" class="w-4 h-4"></i>
                    {{ $siteSettings['home_ship_promo_badge'] ?? 'PROMO KAPAL' }}
                </div>

                <h2 class="mt-4 text-2xl lg:text-3xl font-extrabold text-slate-900">
                    {{ $siteSettings['home_ship_promo_title'] ?? 'Paket Sewa Kapal Promo' }}
                </h2>

                @if(!empty($siteSettings['home_ship_promo_desc']))
                <p class="mt-2 text-slate-600">
                    {{ $siteSettings['home_ship_promo_desc'] }}
                </p>
                @endif
            </div>

            {{-- route list kapal yang valid di source lu: ship.index --}}
            <a href="{{ route('ship.index') }}" class="hidden sm:inline-flex btn btn-ghost">
                {{ $isEn ? 'View All' : 'Lihat Semua' }}
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="mt-7">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-5">
                @foreach($promoShips->take(10) as $package)
                @php
                $tiers = $package->tiers ?? collect();
                $minPrice = $tiers->where('price', '>', 0)->min('price');

                $ratingCount = (int)($package->rating_count ?? 0);

                // MULTI BAHASA (DINAMIS) - sama gaya home-promo-tours di repo lu
                $title = $isEn
                ? (($package->title_en ?? null) ?: ($package->title ?? ''))
                : ($package->title ?? '');

                $durationText = $isEn
                ? (($package->duration_text_en ?? null) ?: ($package->duration_text ?? ''))
                : ($package->duration_text ?? '');

                $catBase = $package->category?->name ?? 'Kapal';
                $catName = $isEn
                ? (($package->category?->name_en ?? null) ?: $catBase)
                : $catBase;
                @endphp



                <div>
                    <a href="{{ route('ship.show', $package->slug) }}"
                        class="group block bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition h-full">

                        <div class="relative h-44 overflow-hidden bg-slate-100">
                            <img
                                src="{{ $package->thumbnail_path ? asset('storage/'.$package->thumbnail_path) : 'https://via.placeholder.com/1200x600?text=Sewa+Kapal' }}"
                                alt="{{ $title }}"
                                class="h-full w-full object-cover">

                            @if(!empty($package->label))
                            <div class="absolute top-3 right-3">
                                <span class="inline-flex items-center rounded-full bg-red-600 border border-red-600 px-3 py-1 text-xs font-extrabold text-white shadow">
                                    {{ $package->label }}
                                </span>
                            </div>
                            @endif

                            {{-- BADGE KATEGORI (anti nabrak) --}}
                            <div class="absolute top-3 left-3 max-w-[calc(100%-7.5rem)]">
                                <span class="inline-flex items-center gap-2 rounded-full bg-white/92 border border-slate-200 px-3 py-1 text-xs font-extrabold text-slate-700 shadow max-w-full">
                                    <i data-lucide="tag" class="w-4 h-4 shrink-0" style="color:#0194F3;"></i>
                                    <span class="truncate max-w-full">
                                        {{ $catName }}
                                    </span>
                                </span>
                            </div>
                        </div>

                        <div class="px-4 pt-4 pb-3">
                            <div class="text-[15px] font-extrabold text-[#0194F3] line-clamp-2">
                                {{ $title }}
                            </div>

                            <div class="mt-2 text-sm">
                                <span class="text-slate-600">{{ $isEn ? 'From ' : 'Mulai ' }}</span>
                                <span class="font-extrabold text-rose-600">
                                    @if($minPrice !== null)
                                    Rp {{ number_format((int) $minPrice, 0, ',', '.') }}
                                    @else
                                    -
                                    @endif
                                </span>
                                <span class="text-slate-500">/charter</span>
                            </div>

                            <div class="mt-2 flex items-center gap-2 text-xs text-slate-600">
                                <div class="flex items-center gap-0.5" aria-label="Rating">
                                    @for($i=0; $i<5; $i++)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#FBBF24" class="w-4 h-4">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                        </svg>
                                        @endfor
                                </div>
                                <span>({{ $ratingCount }})</span>
                            </div>
                        </div>

                        <div class="border-t border-slate-200 px-4 pt-3 pb-4">
                            <div class="flex items-center gap-2 text-xs text-slate-600">
                                <i data-lucide="calendar" class="w-4 h-4" style="color:#0194F3;"></i>
                                <span class="line-clamp-1">{{ $durationText }}</span>
                            </div>

                            <div class="mt-3">
                                <div class="btn btn-primary w-full justify-center !rounded-md !py-2">
                                    {{ $isEn ? 'View Details' : 'Lihat Detail' }}
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>

            <div class="mt-10 sm:hidden">
                <a href="{{ route('ship.index') }}" class="btn btn-ghost w-full">
                    {{ $isEn ? 'View All Packages' : 'Lihat Semua Paket' }}
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>

            </div>
        </div>

    </div>
</section>
@endif