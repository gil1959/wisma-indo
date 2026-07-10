@extends('layouts.front')

@php
$isEn = app()->getLocale() === 'en';
@endphp

@section('title', $isEn ? 'Checkout' : 'Checkout')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-10">
  <div class="mb-8">
    <h1 class="text-2xl font-extrabold text-slate-900">{{ $isEn ? 'Checkout' : 'Checkout' }}</h1>
    <p class="mt-1 text-sm text-slate-500">
      {{ $isEn ? 'Complete billing details and choose a payment method.' : 'Lengkapi detail billing dan pilih metode pembayaran.' }}
    </p>
  </div>


  <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

    {{-- LEFT FORM --}}
    <form method="POST"
      action="{{ route('checkout.process', $order->id) }}"
      class="lg:col-span-2 space-y-6">
      @csrf

      {{-- ERROR GLOBAL --}}
      @if ($errors->any())
      <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800">
        <div class="font-bold">{{ $isEn ? 'Please fix the following:' : '
Harap perbaiki hal berikut:' }}</div>
        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
          @foreach ($errors->all() as $err)
          <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      {{-- BILLING CARD --}}
      <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="mb-5 flex items-start justify-between gap-4">
          <div>
            <h2 class="text-lg font-extrabold text-slate-900">{{ $isEn ? 'Billing Address' : 'Billing Address' }}</h2>
            <p class="mt-1 text-sm text-slate-500">
              {{ $isEn ? 'This information is used for invoices and payment confirmation.' : 'Data ini dipakai untuk invoice dan konfirmasi pembayaran.' }}
            </p>

          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label class="text-sm font-bold text-slate-700">{{ $isEn ? 'First Name' : 'Nama Depan' }}</label>
            <input type="text" name="billing_first_name"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
              value="{{ old('billing_first_name', $order->billing_first_name) }}">
          </div>

          <div>
            <label class="text-sm font-bold text-slate-700">{{ $isEn ? 'Last Name' : 'Nama Belakang' }}</label>
            <input type="text" name="billing_last_name"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
              value="{{ old('billing_last_name', $order->billing_last_name) }}">
          </div>

          <div class="sm:col-span-2">
            <label class="text-sm font-bold text-slate-700">{{ $isEn ? 'Country' : 'Negara' }}</label>
            <input type="text" name="billing_country"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
              value="{{ old('billing_country', $order->billing_country ?? 'Indonesia') }}">
          </div>

          <div class="sm:col-span-2">
            <label class="text-sm font-bold text-slate-700">{{ $isEn ? 'Full Address' : 'Alamat Lengkap' }}</label>
            <textarea name="billing_address" rows="2"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30">{{ old('billing_address', $order->billing_address) }}</textarea>
          </div>

          <div>
            <label class="text-sm font-bold text-slate-700">{{ $isEn ? 'City' : 'Kota' }}</label>
            <input type="text" name="billing_city"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
              value="{{ old('billing_city', $order->billing_city) }}">
          </div>

          <div>
            <label class="text-sm font-bold text-slate-700">{{ $isEn ? 'State' : 'Provinsi' }}</label>
            <input type="text" name="billing_state"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
              value="{{ old('billing_state', $order->billing_state) }}">
          </div>

          <div>
            <label class="text-sm font-bold text-slate-700">{{ $isEn ? 'Postal' : 'Kode Pos' }}</label>
            <input type="text" name="billing_postal"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
              value="{{ old('billing_postal', $order->billing_postal) }}">
          </div>

          <div class="sm:col-span-2">
            <label class="text-sm font-bold text-slate-700">{{ $isEn ? 'Phone' : 'No HP' }}</label>
            <input type="text" name="billing_phone"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
              value="{{ old('billing_phone', $order->billing_phone ?? $order->customer_phone) }}">
          </div>
        </div>
      </section>

      {{-- PAYMENT CARD --}}
      <section id="payment-method" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

        <div class="mb-5">
          <h2 class="text-lg font-extrabold text-slate-900">Payment Method</h2>
          <p class="mt-1 text-sm text-slate-500">{{ $isEn ? 'Select one of the payment methods below.' : 'Pilih salah satu metode pembayaran di bawah.' }}</p>
        </div>

        @php
        $selected = old('payment_method', $order->payment_method ?? null);

        // normalisasi item payment supaya gampang di-render tile
        $bankItems = [];
        $ewalletItems = [];
        $qrItems = [];
        $otherItems = [];

        // Manual bank -> masuk kategori Bank Transfer
        foreach ($manualMethods as $m) {
        $bankItems[] = [
        'value' => "manual:{$m->id}",
        'title' => $m->method_name,
        'subtitle' => $m->account_number ? $m->account_number : null,

        'icon_url' => null, // manual ga punya icon_url
        'source' => 'manual',
        ];
        }

        // Gateway channels -> kategorikan by group/name/code
        foreach (($gatewayOptions ?? []) as $opt) {
        $group = strtolower((string)($opt['group'] ?? ''));
        $name = strtolower((string)($opt['name'] ?? $opt['label'] ?? ''));
        $code = strtolower((string)($opt['channel_code'] ?? ''));

        $item = [
        'value' => $opt['value'],
        'title' => $opt['name'] ?? $opt['label'],
        'subtitle' => $opt['gateway_label'] ?? null,
        'meta' => null,
        'icon_url' => $opt['icon_url'] ?? null,
        'source' => 'gateway',
        ];

        // QR
        if (str_contains($group, 'qris') || str_contains($name, 'qris') || str_contains($name, 'qr')) {
        $qrItems[] = $item;
        continue;
        }

        // E-Wallet
        if (str_contains($group, 'e-wallet') || str_contains($group, 'wallet') || str_contains($name, 'ovo') || str_contains($name, 'dana') || str_contains($name, 'shopee')) {
        $ewalletItems[] = $item;
        continue;
        }

        // Bank transfer / VA
        if (str_contains($group, 'virtual') || str_contains($group, 'va') || str_contains($group, 'bank') || str_contains($name, 'va') || str_contains($name, 'bank')) {
        $bankItems[] = $item;
        continue;
        }

        // Sisanya (minimarket, credit card, dll)
        $otherItems[] = $item;
        }

        // helper: render tile (biar ga duplikatif)
        @endphp

        @php
        $selected = old('payment_method', $order->payment_method ?? null);

        // ---------------------------
        // 1) Manual bank methods
        // ---------------------------
        $manualTiles = [];
        foreach ($manualMethods as $m) {
        $manualTiles[] = [
        'value' => "manual:{$m->id}",
        'title' => $m->method_name,
        'subtitle' => $m->account_number ?: null,
        'meta' => $m->account_holder ? "A/n: {$m->account_holder}" : null,
        'icon_url' => null,
        'kind' => 'manual',
        ];
        }

        // ---------------------------
        // 2) Gateway: Virtual Account (prefer Xendit invoice)
        // ---------------------------
        $vaTile = null;

        // prefer Xendit invoice (sesuai screenshot)
        foreach (($gatewayOptions ?? []) as $opt) {
        if (($opt['gateway'] ?? null) === 'xendit' && ($opt['channel_code'] ?? null) === 'invoice') {
        $vaTile = [
        'value' => $opt['value'],
        'title' => 'Virtual Account',
        'subtitle' => $opt['gateway_label'] ?? 'Xendit',
        'meta' => null,
        'icon_url' => $opt['icon_url'] ?? null,
        'kind' => 'va',
        ];
        break;
        }
        }

        // fallback: kalau tidak ada xendit invoice, ambil 1 channel gateway yang kelihatan VA/bank
        if (!$vaTile) {
        foreach (($gatewayOptions ?? []) as $opt) {
        $group = strtolower((string)($opt['group'] ?? ''));
        $name = strtolower((string)($opt['name'] ?? $opt['label'] ?? ''));
        if (str_contains($group, 'virtual') || str_contains($group, 'va') || str_contains($group, 'bank') || str_contains($name, 'va') || str_contains($name, 'virtual')) {
        $vaTile = [
        'value' => $opt['value'],
        'title' => 'Virtual Account',
        'subtitle' => $opt['gateway_label'] ?? ($opt['gateway'] ?? 'Gateway'),
        'meta' => null,
        'icon_url' => $opt['icon_url'] ?? null,
        'kind' => 'va',
        ];
        break;
        }
        }
        }

        // ---------------------------
        // 3) Gateway: PayPal Checkout
        // ---------------------------
        $paypalTile = null;
        foreach (($gatewayOptions ?? []) as $opt) {
        if (($opt['gateway'] ?? null) === 'paypal' && ($opt['channel_code'] ?? null) === 'checkout') {
        $paypalTile = [
        'value' => $opt['value'],
        'title' => 'PayPal Checkout',
        'subtitle' => $opt['gateway_label'] ?? 'PayPal',
        'meta' => null,
        'icon_url' => $opt['icon_url'] ?? null,
        'kind' => 'paypal',
        ];
        break;
        }
        }

        // ---------------------------
        // Compose tiles: manual + VA + PayPal dalam 1 container
        // ---------------------------
        $tiles = $manualTiles;

        if ($vaTile) $tiles[] = $vaTile;
        if ($paypalTile) $tiles[] = $paypalTile;

        // UI: biar gak kepanjangan, tampilkan max 6 tile dulu, sisanya bisa dibuka
        $firstTiles = array_slice($tiles, 0, 6);
        $restTiles = array_slice($tiles, 6);
        @endphp

        <div x-data="{ showMore: false }" class="space-y-3">
          <div class="rounded-2xl border border-slate-200 bg-white">
            <div class="px-4 py-4 flex items-center justify-between">
              <div class="text-left">
                <div class="text-sm font-extrabold text-slate-900">Bank Transfer</div>
                <div class="text-xs text-slate-500">Transfer bank / Virtual Account</div>
              </div>
            </div>

            <div class="px-4 pb-4">
              <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                @foreach($firstTiles as $it)
                <label class="cursor-pointer">
                  <input
                    type="radio"
                    name="payment_method"
                    value="{{ $it['value'] }}"
                    class="peer sr-only"
                    @if($selected===$it['value']) checked @endif
                    required />
                  <div class="rounded-2xl border border-slate-200 bg-white p-4 flex items-center justify-center
                        hover:border-[#0194F3]/60 hover:bg-slate-50
                        peer-checked:border-[#0194F3] peer-checked:ring-2 peer-checked:ring-[#0194F3]/20">
                    @if(!empty($it['icon_url']))
                    <img src="{{ $it['icon_url'] }}" alt="{{ $it['title'] }}" class="h-8 object-contain">
                    @else
                    <div class="text-center">
                      <div class="text-sm font-extrabold text-slate-900 leading-tight">{{ $it['title'] }}</div>
                      @if(!empty($it['subtitle']))
                      <div class="mt-1 text-[11px] text-slate-500">{{ $it['subtitle'] }}</div>
                      @endif

                    </div>
                    @endif
                  </div>
                </label>
                @endforeach

                @if(count($restTiles) > 0)
                <button type="button"
                  class="rounded-2xl border border-slate-200 bg-white p-4 text-center
                   hover:border-[#0194F3]/60 hover:bg-slate-50"
                  @click="showMore = !showMore">
                  <div class="text-sm font-extrabold text-slate-900"> {{ $isEn ? 'Other' : 'Lainnya' }}</div>
                  <div class="mt-1 text-[11px] text-slate-500" x-text="showMore ? 'Sembunyikan' : 'Lihat lainnya'"></div>
                </button>
                @endif
              </div>

              @if(count($restTiles) > 0)
              <div x-show="showMore" x-collapse class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                @foreach($restTiles as $it)
                <label class="cursor-pointer">
                  <input
                    type="radio"
                    name="payment_method"
                    value="{{ $it['value'] }}"
                    class="peer sr-only"
                    @if($selected===$it['value']) checked @endif
                    required />
                  <div class="rounded-2xl border border-slate-200 bg-white p-4 flex items-center justify-center
                          hover:border-[#0194F3]/60 hover:bg-slate-50
                          peer-checked:border-[#0194F3] peer-checked:ring-2 peer-checked:ring-[#0194F3]/20">
                    @if(!empty($it['icon_url']))
                    <img src="{{ $it['icon_url'] }}" alt="{{ $it['title'] }}" class="h-8 object-contain">
                    @else
                    <div class="text-center">
                      <div class="text-sm font-extrabold text-slate-900 leading-tight">{{ $it['title'] }}</div>
                      @if(!empty($it['subtitle']))
                      <div class="mt-1 text-[11px] text-slate-500">{{ $it['subtitle'] }}</div>
                      @endif
                      @if(!empty($it['meta']))
                      <div class="mt-1 text-[11px] text-slate-400">{{ $it['meta'] }}</div>
                      @endif
                    </div>
                    @endif
                  </div>
                </label>
                @endforeach
              </div>
              @endif

            </div>
          </div>
        </div>


        {{-- SUBMIT --}}
        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <p class="text-xs text-slate-500">
            {{ $isEn ? 'By continuing, you agree to the payment process according to the method chosen.' : 'Dengan melanjutkan, kamu menyetujui proses pembayaran sesuai metode yang dipilih.' }}
          </p>

          <button type="submit"
            class="w-full sm:w-auto rounded-xl px-6 py-3 text-sm font-extrabold text-white shadow-sm"
            style="background:#0194F3"
            onmouseover="this.style.background='#0186DB'"
            onmouseout="this.style.background='#0194F3'">
            {{ $isEn ? 'Continue to Payment' : 'Lanjut ke Pembayaran' }}
          </button>
        </div>
      </section>

    </form>

    {{-- RIGHT SUMMARY --}}
    <aside class="space-y-4">
      <div class="sticky top-24 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="mb-4">
          <h2 class="text-lg font-extrabold text-slate-900">{{ $isEn ? 'Order Summary' : 'Ringkasan Pesanan' }}</h2>
          <p class="mt-1 text-sm text-slate-500">{{ $isEn ? 'Make sure the order details are correct.' : 'Pastikan detail pesanan sudah benar.' }}</p>
        </div>

        <div class="flex gap-3">
          @if(!empty($package->thumbnail_path))
          <img src="{{ asset('storage/'.$package->thumbnail_path) }}"
            class="h-20 w-20 rounded-xl object-cover ring-1 ring-slate-200"
            alt="Thumbnail">
          @endif

          <div class="min-w-0">
            <p class="truncate font-extrabold text-slate-900">
              {{ $package->title ?? $order->product_name }}
            </p>
            <p class="mt-1 text-sm text-slate-500">
              @php
              $typeLabel =
              $order->type === 'tour' ? 'Paket Tour'
              : ($order->type === 'rent_car' ? 'Paket Rent Car'
              : ($order->type === 'ship' ? 'Sewa Kapal'
              : ($order->type === 'umrah' ? 'Paket Umrah'
              : ($order->type === 'mice' ? 'Paket MICE' : 'Produk'))));
              @endphp
              {{ $typeLabel }}

            </p>
            @if($order->invoice_number ?? false)
            <p class="mt-1 text-xs text-slate-500">
              Invoice: <span class="font-semibold text-slate-700">{{ $order->invoice_number }}</span>
            </p>
            @endif
          </div>
        </div>

        <div class="mt-5 space-y-2 text-sm">
          <div class="flex items-center justify-between text-slate-600">
            <span>Subtotal</span>
            <span class="font-bold text-slate-900">Rp {{ number_format($order->subtotal,0,',','.') }}</span>
          </div>

          <div class="flex items-center justify-between text-slate-600">
            <span>Diskon</span>
            <span class="font-bold text-slate-900">Rp {{ number_format($order->discount,0,',','.') }}</span>
          </div>

          <div class="my-3 border-t border-slate-200"></div>

          <div class="flex items-center justify-between">
            <span class="text-sm font-extrabold text-slate-900">Total</span>
            <span class="text-lg font-extrabold" style="color:#0194F3">
              Rp {{ number_format($order->final_price,0,',','.') }}
            </span>
          </div>
        </div>
      </div>
    </aside>

  </div>
</div>
@endsection