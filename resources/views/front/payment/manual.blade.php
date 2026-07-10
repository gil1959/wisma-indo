@extends('layouts.front')

@php
$isEn = app()->getLocale() === 'en';
@endphp

@section('title', ($isEn ? 'Payment ' : 'Pembayaran ') . $order->invoice_number)


@section('content')
<div class="mx-auto max-w-4xl px-4 py-10">
  {{-- Flash --}}
  <div class="space-y-3">
    @if(session('success'))
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
      {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
      {{ session('error') }}
    </div>
    @endif
  </div>

  {{-- Header --}}
  <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
    <div>
      <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">{{ $isEn ? 'Manual Payment' : 'Pembayaran Manual' }}</h1>
      <p class="mt-1 text-sm text-slate-600">
        Invoice: <span class="font-semibold text-slate-900">{{ $order->invoice_number }}</span>
      </p>
    </div>

    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-extrabold text-slate-700">
      {{ strtoupper($order->type) }}
    </span>
  </div>

  <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">

    {{-- Left: Summary + Instructions --}}
    <div class="space-y-6 lg:col-span-2">

      {{-- Ringkasan Pesanan --}}
      <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
          <div class="min-w-0">
            <div class="text-xs font-extrabold uppercase tracking-wide text-slate-500">{{ $isEn ? 'Product' : 'Produk' }}</div>
            <div class="mt-1 text-base font-extrabold text-slate-900">
              {{ $order->product_name }}
            </div>

            <div class="mt-2 text-sm text-slate-600">
              {{ $isEn ? 'On behalf of' : 'Atas nama' }}: <span class="font-semibold text-slate-900">{{ $order->customer_name }}</span>
              <span class="mx-2 text-slate-300"></span>
              <span class="break-all">{{ $order->customer_email }}</span>
            </div>
          </div>

          <div class="text-left sm:text-right">
            <div class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Total</div>
            @php
            $uniqueCode = (int)($order->unique_code ?? 0);
            $payable = (int)($order->payable_amount ?? ((int)$order->final_price + $uniqueCode));
            @endphp
            <div class="mt-1 text-2xl font-extrabold" style="color:#0194F3">

              Rp {{ number_format($payable,0,',','.') }}

            </div>
          </div>
        </div>

        <div class="mt-5 border-t border-slate-200 pt-4">
          <div class="grid grid-cols-1 gap-2 text-sm sm:grid-cols-2">
            <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
              <span class="text-slate-600">Subtotal</span>
              <span class="font-extrabold text-slate-900">Rp {{ number_format($order->subtotal,0,',','.') }}</span>
            </div>

            <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
              <span class="text-slate-600">Diskon</span>
              <span class="font-extrabold text-slate-900">Rp {{ number_format($order->discount,0,',','.') }}</span>
            </div>
          </div>
        </div>
      </section>

      {{-- Instruksi Transfer --}}
      <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="flex items-start gap-3">
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl"
            style="background:rgba(1,148,243,.12); color:#0194F3;">
            <span class="font-extrabold">i</span>
          </div>

          <div class="min-w-0">
            <h2 class="text-lg font-extrabold text-slate-900">{{ $isEn ? 'Transfer Instructions' : 'Instruksi Transfer' }}</h2>
            <p class="mt-1 text-sm text-slate-600">
              {{ $isEn ? 'Transfer according to nominal' : 'Transfer sesuai nominal' }} <span class="font-extrabold text-slate-900">{{ $isEn ? 'exactly' : 'persis' }}</span> {{ $isEn ? 'for easy verification.' : 'agar mudah diverifikasi.' }}
              {{ $isEn ? 'After that, upload proof of transfer below.' : 'Setelah itu upload bukti transfer di bawah..' }}
            </p>
            <div class="mt-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
              <div class="text-xs font-extrabold uppercase tracking-wide text-slate-500">{{ $isEn ? 'unique code' : 'Kode Unik' }}</div>
              <div class="mt-1 text-lg font-extrabold text-slate-900">
                {{ str_pad((string)$uniqueCode, 3, '0', STR_PAD_LEFT) }}
              </div>
              <div class="mt-1 text-sm text-slate-600">
                {{ $isEn ? 'Total transfer to be paid' : 'Total transfer yang harus dibayar' }} : <span class="font-extrabold text-slate-900">Rp {{ number_format($payable,0,',','.') }}</span>
              </div>
            </div>

          </div>
        </div>

        {{-- Detail Rekening --}}
        <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
          <dl class="grid grid-cols-1 divide-y divide-slate-200 sm:grid-cols-4 sm:divide-y-0 sm:divide-x">

            <div class="p-4">
              <dt class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Bank</dt>
              <dd class="mt-1 text-sm font-extrabold text-slate-900 break-words">
                {{ $manualMethod->bank_name }}
              </dd>
            </div>

            <div class="p-4">
              <dt class="text-xs font-extrabold uppercase tracking-wide text-slate-500"> {{ $isEn ? 'account number' : 'No. Rekening' }}</dt>
              <dd class="mt-1 text-sm font-extrabold text-slate-900 font-mono tracking-wide break-all">
                {{ $manualMethod->account_number }}
              </dd>
            </div>

            <div class="p-4 sm:col-span-2">
              <dt class="text-xs font-extrabold uppercase tracking-wide text-slate-500"> {{ $isEn ? 'On behalf of' : 'Atas nama' }}</dt>
              <dd class="mt-1 text-sm font-extrabold text-slate-900 break-words">
                {{ $manualMethod->account_holder }}
              </dd>
            </div>

            <div class="p-4">
              <dt class="text-xs font-extrabold uppercase tracking-wide text-slate-500">SWIFT</dt>
              <dd class="mt-1 text-sm font-extrabold text-slate-900 font-mono tracking-wide break-all">
                {{ $manualMethod->swift_code ?: '-' }}
              </dd>
            </div>

          </dl>
        </div>

      </section>

    </div>

    {{-- Right: Upload --}}
    <aside class="lg:col-span-1">
      <div class="sticky top-24 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <h3 class="text-base font-extrabold text-slate-900">{{ $isEn ? 'Upload Proof of Transfer' : 'Upload Bukti Transfer' }}</h3>
        <p class="mt-1 text-sm text-slate-600">
          {{ $isEn ? 'After uploading, the status will be ready' : 'Setelah upload, status akan menjadi' }} <span class="font-extrabold text-slate-900">waiting_verification</span>.
        </p>

        <form method="POST"
          action="{{ route('payment.manual.submit', $order) }}"
          enctype="multipart/form-data"
          class="mt-5 space-y-4">
          @csrf

          <div>
            <label class="block text-sm font-extrabold text-slate-700">{{ $isEn ? 'Proof of Transfer' : 'Bukti Transfer' }}</label>

            <div class="mt-2 rounded-2xl border border-slate-200 bg-white p-3">
              <input type="file"
                name="proof"
                required
                class="block w-full text-sm text-slate-700
                            file:mr-3 file:rounded-xl file:border-0 file:px-4 file:py-2 file:text-sm file:font-extrabold
                            file:text-white file:shadow-sm
                            file:[background:#0194F3] hover:file:opacity-90
                            focus:outline-none" />

              <p class="mt-2 text-xs text-slate-500">
                {{ $isEn ? 'Format: JPG/JPEG/PNG Max: 2MB' : 'Format: JPG/JPEG/PNG  Maks: 2MB' }}
              </p>
            </div>

            @error('proof')
            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
            @enderror
          </div>

          <button type="submit"
            class="w-full rounded-2xl px-4 py-3 text-sm font-extrabold text-white shadow-sm"
            style="background:#0194F3"
            onmouseover="this.style.background='#0186DB'"
            onmouseout="this.style.background='#0194F3'">
            {{ $isEn ? 'Upload Proof & Complete' : 'Upload Bukti & Selesaikan' }}
          </button>

          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-xs text-slate-600">
            <div class="font-extrabold text-slate-800">{{ $isEn ? 'Note' : 'Catatan' }}</div>
            <ul class="mt-2 list-disc space-y-1 pl-5">
              <li>{{ $isEn ? 'Make sure the transfer amount matches the total invoice.' : 'Pastikan nominal transfer sesuai total invoice.' }}</li>
              <li>{{ $isEn ? 'Upload clear images (not blurry).' : 'Upload gambar yang jelas (tidak blur).' }}</li>
              <li>{{ $isEn ? 'Verification is carried out by the admin.' : 'Verifikasi dilakukan oleh admin.' }}</li>
            </ul>
          </div>
        </form>
      </div>
    </aside>

  </div>
</div>
@endsection