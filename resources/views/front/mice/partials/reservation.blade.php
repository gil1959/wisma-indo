@php
$isEn = app()->getLocale() === 'en';
$numberLocale = $isEn ? 'en-US' : 'id-ID';

$i18n = [
'reservation_title' => $isEn ? 'Package Reservation' : 'Reservasi Paket',
'domestic' => $isEn ? 'Domestic' : 'Domestik',
'foreign_tourists' => $isEn ? 'Foreign Tourists' : 'Foreign Tourists',
'wna' => $isEn ? 'Foreign' : 'WNA',
'price' => $isEn ? 'Price' : 'Harga',
'per_pax' => $isEn ? '/ pax' : '/ pax',
'no_price' => $isEn ? 'No price available for this category yet.' : 'Belum ada harga untuk kategori ini.',
'continue' => $isEn ? 'Continue Booking' : 'Lanjut Booking',
'default_tier_label' => $isEn ? 'Price' : 'Harga',
];

$highSeasonNotice = $isEn
? 'For high season, you must chat admin before booking.'
: 'Untuk high season Wajib Chat admin sebelum Booking';
// tiers -> inject ke alpine (format yang dipakai UI: id, label, price)
$domesticJs = ($package->tiers ?? collect())
->where('type', 'domestic')
->values()
->map(fn ($t) => [
'id' => $t->id,
'label' => ($t->label_text ?: $i18n['default_tier_label']),
'price' => (int) $t->price,
])
->all();

$foreignJs = ($package->tiers ?? collect())
->where('type', 'foreign')
->values()
->map(fn ($t) => [
'id' => $t->id,
'label' => ($t->label_text ?: $i18n['default_tier_label']),
'price' => (int) $t->price,
])
->all();

@endphp

<div class="md:col-span-1">
    <div
        class="sticky top-24 bg-white shadow-lg rounded-2xl p-6 border border-gray-100"
        x-data="{
        active: 'domestic',
        selectedTier: null,
        tiers: {
            domestic: @js($domesticJs),
            international: @js($foreignJs)
        }
    }"
        x-init="
        // kalau domestic kosong tapi foreign ada, auto pindah tab
        if ((!tiers.domestic || tiers.domestic.length === 0) && (tiers.international && tiers.international.length)) {
            active = 'international';
        }
    ">


        <h2 class="font-bold text-lg mb-4 text-gray-900">{{ $i18n['reservation_title'] }}</h2>

        {{-- TAB DOMESTIK / FOREIGN TOURISTS (SAMA PERSIS TOUR) --}}
        <div class="flex mb-4 bg-gray-100 rounded-full p-1 text-sm font-semibold">
            <button
                type="button"
                @click="active = 'domestic'"
                :class="active === 'domestic' ? 'bg-[#0194F3] text-white shadow-sm' : 'text-gray-600'"
                class="flex-1 py-2 rounded-full transition">
                {{ $i18n['domestic'] }}
            </button>
            <button
                type="button"
                @click="active = 'international'"
                :class="active === 'international' ? 'bg-[#0194F3] text-white shadow-sm' : 'text-gray-600'"
                class="flex-1 py-2 rounded-full transition">
                Foreign Tourists
            </button>
        </div>

        {{-- DAFTAR TIERS (HOVER EFFECT SAMA TOUR) --}}
        <div class="space-y-3">
            <template x-for="tier in tiers[active]" :key="tier.id">
                <div
                    class="p-4 border rounded-xl cursor-pointer hover:border-[#0194F3] transition flex justify-between items-center"
                    @click="selectedTier = tier"
                    :class="selectedTier && selectedTier.id === tier.id ? 'border-[#0194F3] bg-[#0194F3]/5' : ''">
                    <div class="text-sm">
                        <p class="font-semibold text-gray-800" x-text="tier.label"></p>
                        <p class="text-xs text-gray-500 mt-1" x-text="active === 'domestic' ? 'Domestik' : 'WNA'"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[#0194F3] font-bold text-lg">
                            Rp <span x-text="tier.price.toLocaleString(@js($numberLocale))"></span>
                        </p>
                        <p class="text-[11px] text-gray-500">{{ $i18n['per_pax'] }}</p>
                    </div>
                </div>
            </template>

            {{-- DOMESTIK: kalau kosong tampilkan info. FOREIGN: harus kosong tanpa text apa pun --}}
            <template x-if="active === 'domestic' && (!tiers[active] || tiers[active].length === 0)">
                <p class="text-sm text-gray-500">{{ $i18n['no_price'] }}</p>
            </template>
        </div>
        {{-- HIGH SEASON WARNING --}}
        <div class="mt-3 p-4 bg-red-50 border-l-4 border-red-400 rounded text-xs text-red-800 space-y-1">
            <p class="font-semibold">
                {!! $isEn
                ? 'For <span class="uppercase font-extrabold">high season</span>, you must chat admin before booking.'
                : 'Untuk <span class="uppercase font-extrabold">high season</span> Wajib Chat admin sebelum Booking.' !!}
            </p>
        </div>
        {{-- BUTTON (ICON SAMA TOUR, bukan emoji) --}}
        <button
            type="button"
            class="w-full mt-6 bg-[#0194F3] text-white py-3 rounded-xl font-semibold text-sm flex items-center justify-center gap-2 disabled:bg-gray-400 disabled:cursor-not-allowed transition"
            :disabled="!selectedTier"
            @click="$dispatch('open-booking', { tier: selectedTier })">
            <i data-lucide="shopping-cart" class="w-4 h-4"></i>
            <span>{{ $i18n['continue'] }}</span>
        </button>

    </div>
</div>