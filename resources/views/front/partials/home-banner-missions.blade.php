@php
$items = $homeMissionBanners ?? collect();
$isEn = app()->getLocale() === 'en';
@endphp

@if($items->count() > 0)
<section class="bg-slate-50">
  <div class="max-w-7xl mx-auto px-4 pb-10 lg:pb-12"
    x-data="homeMissionBannerSlider()"
    x-init="init()">

    <div class="flex items-center justify-between gap-4">
      <div class="flex items-center gap-2 text-xl lg:text-2xl font-extrabold text-slate-900">
        {{ $siteSettings['home_mission_banner_title'] ?? ($isEn ? 'Earn up to IDR 850K from missions' : 'Dapatkan hingga Rp 850K dari misi') }}
      </div>


      <div class="hidden md:flex items-center gap-2">
        <button type="button"
          class="btn btn-ghost !px-3 !py-2"
          @click="prev()"
          aria-label="Sebelumnya">
          <i data-lucide="chevron-left" class="w-5 h-5"></i>
        </button>

        <button type="button"
          class="btn btn-ghost !px-3 !py-2"
          @click="next()"
          aria-label="Berikutnya">
          <i data-lucide="chevron-right" class="w-5 h-5"></i>
        </button>
      </div>
    </div>

    <div class="mt-5">
      <div x-ref="track"
        class="flex gap-5 overflow-x-auto scroll-smooth snap-x snap-mandatory no-scrollbar pb-2">
        @foreach($items as $it)
        <a href="{{ $it->link_url }}"
          class="snap-start shrink-0 w-[88%] sm:w-[56%] md:w-[44%] lg:w-[32%]
                    rounded-2xl overflow-hidden border border-slate-200 bg-white shadow-sm
                    hover:shadow-md transition
                    focus:outline-none focus:ring-4 focus:ring-[rgba(1,148,243,0.25)]">
          <div class="relative aspect-[16/7] bg-slate-100">
            <img
              src="{{ $it->thumbnail_path ? asset('storage/'.$it->thumbnail_path) : 'https://via.placeholder.com/1200x500?text=Mission' }}"
              alt="Mission"
              class="h-full w-full object-cover">
            <div class="absolute inset-0 ring-1 ring-inset ring-black/5"></div>
          </div>
        </a>
        @endforeach
      </div>

      <div class="mt-4 flex md:hidden items-center justify-center gap-2">
        <button type="button" class="btn btn-ghost !px-3 !py-2" @click="prev()">
          <i data-lucide="chevron-left" class="w-5 h-5"></i>
        </button>
        <button type="button" class="btn btn-ghost !px-3 !py-2" @click="next()">
          <i data-lucide="chevron-right" class="w-5 h-5"></i>
        </button>
      </div>
    </div>

  </div>
</section>

<script>
  window.homeMissionBannerSlider = window.homeMissionBannerSlider || function() {
    return {
      init() {},
      step() {
        const el = this.$refs.track;
        if (!el) return 360;
        const first = el.querySelector('.snap-start');
        const gap = 20;
        return first ? (first.getBoundingClientRect().width + gap) : 360;
      },
      next() {
        const el = this.$refs.track;
        if (!el) return;
        el.scrollBy({
          left: this.step(),
          behavior: 'smooth'
        });
      },
      prev() {
        const el = this.$refs.track;
        if (!el) return;
        el.scrollBy({
          left: -this.step(),
          behavior: 'smooth'
        });
      }
    }
  };
</script>
@endif