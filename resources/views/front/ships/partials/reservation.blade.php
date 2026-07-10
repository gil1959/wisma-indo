@php
$isEn = app()->getLocale() === 'en';
$highSeasonNotice = $isEn
? 'For high season, you must chat admin before booking.'
: 'Untuk high season Wajib Chat admin sebelum Booking';
$i18n = [
'reservation' => $isEn ? 'Ship Reservation' : 'Reservasi Sewa Kapal',
'no_price' => $isEn ? 'No price available for this category yet.' : 'Belum ada harga untuk kategori ini.',
'continue' => $isEn ? 'Continue Booking' : 'Lanjut Booking',
'per_pax' => $isEn ? '/ pax' : '/ pax',

];
@endphp

<div>
  <div class="md:sticky md:top-24 bg-white shadow-lg rounded-2xl p-6 border border-gray-100">

    <h2 class="font-bold text-lg mb-4 text-gray-900">{{ $i18n['reservation'] }}</h2>

    <div class="flex mb-4 bg-gray-100 rounded-full p-1 text-sm font-semibold">
      <button type="button"
        @click="active = 'weekday'"
        :class="active === 'weekday' ? 'bg-[#0194F3] text-white shadow-sm' : 'text-gray-600'"
        class="flex-1 py-2 rounded-full transition">
        Weekday
      </button>

      <button type="button"
        @click="active = 'weekend'"
        :class="active === 'weekend' ? 'bg-[#0194F3] text-white shadow-sm' : 'text-gray-600'"
        class="flex-1 py-2 rounded-full transition">
        Weekend
      </button>
    </div>

    <div class="space-y-3">
      <template x-for="tier in tiers[active]" :key="tier.id">
        <div
          class="p-4 border rounded-xl cursor-pointer hover:border-[#0194F3] transition
       flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2"

          @click="selectTier(tier)"
          :class="selectedTier && selectedTier.id === tier.id ? 'border-[#0194F3] bg-[#0194F3]/5' : ''">
          <div class="text-sm">
            <p class="font-semibold text-gray-800" x-text="tier.label_text"></p>
            <p class="text-xs text-gray-500 mt-1" x-text="active === 'weekday' ? 'Weekday' : 'Weekend'"></p>
          </div>

          <div class="text-left sm:text-right">
            <p class="text-[#0194F3] font-bold text-base sm:text-lg leading-tight">
              Rp <span x-text="Number(tier.price || 0).toLocaleString('id-ID')"></span>
            </p>
            <p class="text-[11px] text-gray-500">{{ $i18n['per_pax'] }}</p>
          </div>


        </div>

      </template>

      <template x-if="!tiers[active] || tiers[active].length === 0">
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

    <button
      type="button"
      class="w-full mt-6 bg-[#0194F3] text-white py-3 rounded-xl font-semibold text-sm flex items-center justify-center gap-2 disabled:bg-gray-400 disabled:cursor-not-allowed transition"
      :disabled="!selectedTier"
      @click="$dispatch('open-ship-booking', { tier: selectedTier })">
      <i data-lucide="shopping-cart" class="w-4 h-4"></i>
      <span>{{ $i18n['continue'] }}</span>
    </button>

  </div>
</div>