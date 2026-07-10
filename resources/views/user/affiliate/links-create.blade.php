@extends('user.layouts.app')
@php $isEn = app()->getLocale() === 'en'; @endphp

@section('content')
<div class="space-y-6">
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">{{ $isEn ? 'Create Affiliate Link' : 'Buat Link Affiliate' }}</h1>
      <p class="mt-1 text-sm text-slate-600">{{ $isEn ? 'Select a product, add campaign parameters, and (optional) use a coupon.' : 'Pilih produk, tambahkan parameter campaign, dan (opsional) pakai coupon.' }}</p>
    </div>
    <a href="{{ route('user.affiliate.links') }}"
      class="px-4 py-2 rounded-2xl border border-slate-200 text-sm font-bold text-slate-700 bg-white hover:bg-slate-50">
      {{ $isEn ? 'Back' : 'Kembali' }}
    </a>
  </div>

  <div class="bg-white border border-slate-200 rounded-2xl p-4">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3">
      <div>
        <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Product Type' : 'Tipe Produk' }}</label>
        <select name="type" class="mt-2 w-full rounded-2xl border border-slate-200 px-3 py-2 text-sm font-semibold">
          <option value="" {{ $type===''?'selected':'' }}>{{ $isEn ? 'All' : 'Semua' }}</option>
          <option value="tour" {{ $type==='tour'?'selected':'' }}>Tour</option>
          <option value="umrah" {{ $type==='umrah'?'selected':'' }}>Umrah</option>
          <option value="rent_car" {{ $type==='rent_car'?'selected':'' }}>Rent Car</option>
          <option value="ship" {{ $type==='ship'?'selected':'' }}>{{ $isEn ? 'Rent Ship' : 'sewa kapal' }}</option>
        </select>
      </div>
      <div class="md:col-span-2">
        <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Search Product' : 'Cari Produk' }}</label>
        <input name="q" value="{{ $q }}" placeholder="{{ $isEn ? 'Type product name / slug...' : 'Ketik nama produk / slug...' }}"
          class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
      </div>
      <div class="md:col-span-3">
        <button class="px-4 py-2.5 rounded-2xl font-extrabold text-white" style="background:#0194F3;">
          {{ $isEn ? 'Apply Filter' : 'Terapkan Filter' }}
        </button>
      </div>
    </form>
  </div>

  <div class="bg-white border border-slate-200 rounded-2xl p-6">
    <form method="POST" action="{{ route('user.affiliate.links.store') }}" class="space-y-5">
      @csrf

      <div>
        <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Link Name' : 'Nama Link' }}</label>
        <input name="name" required
          class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800"
          placeholder="{{ $isEn ? 'Example: IG Reels - Bali Tour - Jan' : 'Contoh: IG Reels - Tour Bali - Jan' }}">
      </div>

      <div>
        <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Select Product' : 'Pilih Produk' }}</label>
        <div class="mt-2 grid grid-cols-1 gap-2 max-h-72 overflow-auto border border-slate-200 rounded-2xl p-3 bg-slate-50">
          @forelse($products as $p)
          <label class="flex items-center gap-3 p-3 rounded-2xl bg-white border border-slate-200 cursor-pointer hover:bg-slate-50">
            <input type="radio" name="product_pick" value="{{ $p['type'] }}:{{ $p['id'] }}" required>
            <div class="min-w-0">
              <div class="text-sm font-extrabold text-slate-900 truncate">{{ $p['name'] }}</div>
              <div class="text-xs text-slate-600">
                {{ $isEn ? 'Type' : 'Tipe' }}:
                <span class="font-bold">{{ $p['type'] }}</span>
              </div>
              <div class="text-xs text-slate-600">
                {{ $isEn ? 'Package Name' : 'Nama Paket' }}: <span class="font-mono">{{ $p['slug'] }}</span>
              </div>
            </div>
          </label>
          @empty
          <div class="text-sm text-slate-600 p-3">{{ $isEn ? 'No products found for this filter.' : 'Tidak ada produk ditemukan untuk filter ini.' }}
            << /div>
              @endforelse
          </div>

          {{-- helper: split product_pick di controller via request --}}
          <input type="hidden" name="product_type" id="product_type">
          <input type="hidden" name="product_id" id="product_id">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Platform (optional)' : 'Platform (opsional)' }}</label>
            <input name="platform" placeholder="tiktok / instagram / youtube / whatsapp"
              class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
          </div>
          <div>
            <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Platform ID (optional)' : 'Platform ID (opsional)' }}</label>
            <input name="platform_id" placeholder="ID campaign/adset/shortlink internal"
              class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
          </div>
        </div>

        {{-- Advanced Tracking (Optional) --}}
        <div class="border border-slate-200 rounded-2xl overflow-hidden">
          <button type="button"
            class="w-full flex items-center justify-between px-4 py-3 bg-slate-50 hover:bg-slate-100"
            onclick="document.getElementById('utmBox').classList.toggle('hidden')">
            <div class="text-left">
              <div class="text-sm font-extrabold text-slate-900">Campaign Tracking (Optional)</div>
              <div class="text-xs text-slate-600 mt-0.5">
                Advanced
              </div>
            </div>
            <div class="text-xs font-extrabold text-slate-700">{{ $isEn ? 'Show / Hide' : 'Tampilkan / Sembunyikan' }}</div>
          </button>

          <div id="utmBox" class="hidden bg-white px-4 py-4 space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
              <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">UTM Source</label>
                <input name="utm_source" value="{{ old('utm_source') }}"
                  placeholder="{{ $isEn ? 'example: instagram / tiktok / whatsapp' : 'contoh: instagram / tiktok / whatsapp' }}"
                  class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
                <div class="mt-1 text-[11px] text-slate-500">{{ $isEn ? 'Traffic source.' : 'Sumber traffic.' }}</div>
              </div>

              <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">UTM Medium</label>
                <input name="utm_medium" value="{{ old('utm_medium') }}"
                  placeholder="contoh: reels / story / ads / bio"
                  class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
                <div class="mt-1 text-[11px] text-slate-500">{{ $isEn ? 'Content/placement type.' : 'Jenis konten/penempatan.' }}</div>
              </div>

              <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">UTM Campaign</label>
                <input name="utm_campaign" value="{{ old('utm_campaign') }}"
                  placeholder="contoh: promo_januari / lebaran_2026"
                  class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
                <div class="mt-1 text-[11px] text-slate-500">{{ $isEn ? 'Campaign name.' : 'Nama campaign.' }}
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div>
                    <label class="text-xs font-extrabold text-slate-600 uppercase">UTM Content (opsional)</label>
                    <input name="utm_content" value="{{ old('utm_content') }}"
                      placeholder="contoh: video1 / influencerA"
                      class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
                  </div>

                  <div>
                    <label class="text-xs font-extrabold text-slate-600 uppercase">UTM Term (opsional)</label>
                    <input name="utm_term" value="{{ old('utm_term') }}"
                      placeholder="contoh: keyword / segment"
                      class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
                  </div>
                </div>
              </div>
            </div>


            <div>
              <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Coupon (optional)' : 'Coupon (opsional)' }}</label>
              <select name="user_coupon_id" class="mt-2 w-full rounded-2xl border border-slate-200 px-3 py-2 text-sm font-semibold">
                <option value="">{{ $isEn ? 'No coupon' : 'Tanpa coupon' }}</option>
                @foreach($userCoupons as $c)
                <option value="{{ $c->id }}">{{ $c->alias_name }} ({{ $c->promo?->code }})</option>
                @endforeach
              </select>

            </div>

            <button class="px-4 py-2.5 rounded-2xl font-extrabold text-white" style="background:#0194F3;">
              {{ $isEn ? 'Create Link' : 'Create Link' }}
            </button>
    </form>
  </div>
</div>

<script>
  // split product_pick -> product_type + product_id sebelum submit
  document.addEventListener('submit', function(e) {
    const pick = document.querySelector('input[name="product_pick"]:checked');
    if (!pick) return;
    const [t, id] = pick.value.split(':');
    document.getElementById('product_type').value = t;
    document.getElementById('product_id').value = id;
  }, true);
</script>
@endsection