@extends('user.layouts.app')
@php $isEn = app()->getLocale() === 'en'; @endphp

@section('content')
<div class="space-y-5">
  <div class="flex items-start justify-between gap-3">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">Affiliate Links</h1>
      <p class="mt-1 text-sm text-slate-600"> {{ $isEn
      ? 'Manage your affiliate links, track clicks and conversions, and copy Sales / Checkout URLs.'
      : 'Kelola link affiliate kamu, pantau klik dan konversi, dan salin link Sales / Checkout.'
  }}</p>
    </div>

    <a href="{{ route('user.affiliate.links.create') }}"
      class="px-4 py-2.5 rounded-2xl font-extrabold text-white"
      style="background:#0194F3;">
      {{ $isEn ? '+ Create Link' : '+ Buat Link' }}
    </a>
  </div>

  {{-- Filter / Search --}}
  <div class="bg-white p-4 rounded-2xl border border-slate-200">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
      <div class="md:col-span-2">
        <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Search' : 'Cari' }}</label>
        <input name="q" value="{{ request('q') }}" placeholder="{{ $isEn ? 'Search link name / code / product name...' : 'Cari nama link / code / nama produk...' }}">
      </div>

      <div>
        <label class="text-xs font-extrabold text-slate-600 uppercase">Product Type</label>
        <select name="type"
          class="mt-2 w-full rounded-2xl border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-800">
          <option value="" {{ request('type')==='' ? 'selected' : '' }}>{{ $isEn ? 'All' : 'Semua' }}</option>
          <option value="tour" {{ request('type')==='tour' ? 'selected' : '' }}>Tour</option>
          <option value="umrah" {{ request('type')==='umrah' ? 'selected' : '' }}>Umrah</option>
          <option value="rent_car" {{ request('type')==='rent_car' ? 'selected' : '' }}>Rent Car</option>
          <option value="ship" {{ request('type')==='ship' ? 'selected' : '' }}>{{ $isEn ? 'Ship Rental' : 'Sewa Kapal' }}</option>
        </select>
      </div>

      <div class="flex items-end gap-2">
        <button class="w-full px-4 py-2.5 rounded-2xl font-extrabold text-white"
          style="background:#0194F3;">
          {{ $isEn ? 'Apply' : 'Terapkan' }}
        </button>
        <a href="{{ route('user.affiliate.links') }}"
          class="px-4 py-2.5 rounded-2xl border border-slate-200 text-sm font-extrabold text-slate-700 bg-white hover:bg-slate-50">
          {{ $isEn ? 'Reset' : 'Reset' }}
        </a>
      </div>
    </form>
  </div>

  {{-- List --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="p-4 border-b border-slate-200">
      <div class="text-sm font-extrabold text-slate-900">{{ $isEn ? 'Links List' : 'Daftar Links' }}</div>
      <div class="text-xs text-slate-600 mt-1">{{ $isEn
      ? 'Use Sales URL for landing page, Checkout URL for direct checkout (if frontend supports auto-open checkout).'
      : 'Gunakan Sales URL untuk landing page, Checkout URL untuk direct checkout (jika frontend mendukung auto-open checkout).'
  }}</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">{{ $isEn ? 'Name' : 'Nama' }}</th>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">{{ $isEn ? 'Product' : 'Produk' }}</th>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Code</th>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Clicks</th>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Conversions</th>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Sales URL</th>
            <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Checkout URL</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-200">
          @forelse($links as $l)
          <tr class="align-top">
            <td class="px-4 py-3">
              <div class="font-extrabold text-slate-900">{{ $l->name }}</div>
              <div class="mt-1 text-xs text-slate-600">
                {{ $isEn ? 'Created' : 'Dibuat' }}: <span class="font-semibold">{{ optional($l->created_at)->format('d M Y, H:i') }}</span>
              </div>
            </td>

            <td class="px-4 py-3">
              <div class="text-xs font-extrabold uppercase text-slate-600">{{ $l->product_type ?? '-' }}</div>
              <div class="mt-1 font-semibold text-slate-900">{{ $l->product_name ?? '-' }}</div>
              @if(!empty($l->promo_code))
              <div class="mt-1 text-xs text-slate-600">Coupon: <span class="font-extrabold text-slate-800">{{ $l->promo_code }}</span></div>
              @endif
              @if(!empty($l->platform))
              <div class="mt-1 text-xs text-slate-600">Platform: <span class="font-semibold">{{ $l->platform }}</span>@if(!empty($l->platform_id)) • <span class="font-mono">{{ $l->platform_id }}</span>@endif</div>
              @endif
            </td>

            <td class="px-4 py-3 font-extrabold text-slate-900">{{ $l->code }}</td>
            <td class="px-4 py-3 font-semibold text-slate-800">{{ number_format((int)$l->clicks) }}</td>
            <td class="px-4 py-3 font-semibold text-slate-800">{{ number_format((int)$l->conversions) }}</td>

            <td class="px-4 py-3 min-w-[360px]">
              <div class="flex items-center gap-2">
                <input id="sales-url-{{ $l->id }}" readonly value="{{ $l->sales_url }}"
                  class="w-full rounded-2xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 bg-slate-50"
                  onclick="this.select();">
                <button type="button"
                  data-copy-btn
                  data-target="sales-url-{{ $l->id }}"
                  class="shrink-0 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-extrabold text-slate-700 hover:bg-slate-100">
                  {{ $isEn ? 'Copy' : 'Salin' }}
                </button>
              </div>
              <div class="mt-2 text-xs text-slate-500" id="copy-hint-sales-{{ $l->id }}"></div>
            </td>


            <td class="px-4 py-3 min-w-[360px]">
              <div class="flex items-center gap-2">
                <input id="checkout-url-{{ $l->id }}" readonly value="{{ $l->checkout_url }}"
                  class="w-full rounded-2xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 bg-slate-50"
                  onclick="this.select();">
                <button type="button"
                  data-copy-btn
                  data-target="checkout-url-{{ $l->id }}"
                  class="shrink-0 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-extrabold text-slate-700 hover:bg-slate-100">
                  Salin
                </button>
              </div>
              <div class="mt-2 text-xs text-slate-500" id="copy-hint-checkout-{{ $l->id }}"></div>
            </td>

          </tr>
          @empty
          <tr>
            <td class="px-4 py-6 text-slate-600" colspan="7">{{ $isEn ? 'No links yet.' : 'Belum ada link.' }}</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-4">
      {{ $links->links() }}
    </div>
  </div>
</div>
<script>
  (function() {
    function setHint(id, msg) {
      const el = document.getElementById(id);
      if (!el) return;
      el.textContent = msg;
      setTimeout(() => {
        el.textContent = '';
      }, 1200);
    }

    async function copyFromInput(inputId, hintId) {
      const input = document.getElementById(inputId);
      if (!input) return;

      const text = input.value || '';
      if (!text) {
        setHint(hintId, 'Link kosong.');
        return;
      }

      try {
        // modern way
        if (navigator.clipboard && window.isSecureContext) {
          await navigator.clipboard.writeText(text);
        } else {
          // fallback
          input.select();
          input.setSelectionRange(0, 99999);
          document.execCommand('copy');
        }
        setHint(hintId, 'Tersalin.');
      } catch (e) {
        setHint(hintId, 'Gagal menyalin: ' + (e?.message || 'unknown'));
      }
    }

    document.addEventListener('click', function(e) {
      const btn = e.target.closest('[data-copy-btn]');
      if (!btn) return;

      const targetId = btn.getAttribute('data-target');
      if (!targetId) return;

      const hintId = targetId.startsWith('sales-url-') ?
        'copy-hint-sales-' + targetId.replace('sales-url-', '') :
        'copy-hint-checkout-' + targetId.replace('checkout-url-', '');

      copyFromInput(targetId, hintId);
    });
  })();
</script>

@endsection