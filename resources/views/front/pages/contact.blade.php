@extends('layouts.front')
@section('title', ($title ?? 'Contact') . ' - ' . ($siteSettings['seo_site_title'] ?? 'Bintang Wisata'))

@section('content')
@php
  $addr  = $siteSettings['footer_address'] ?? '-';
  $phone = $siteSettings['footer_phone'] ?? '-';
  $email = $siteSettings['footer_email'] ?? '-';
  $wa    = $siteSettings['footer_whatsapp'] ?? '';
@endphp

<section class="relative overflow-hidden bg-white">
  <div class="absolute inset-0 travel-grid opacity-70"></div>
  <div class="absolute -top-32 -right-32 h-96 w-96 rounded-full blur-3xl opacity-25"
       style="background: radial-gradient(circle, #0194F3 0%, transparent 60%);"></div>

  <div class="relative max-w-7xl mx-auto px-4 pt-10 pb-10 lg:pt-16 lg:pb-16">
    <div class="grid gap-10 lg:grid-cols-12 lg:items-center">
      <div class="lg:col-span-7">
        <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/70 px-4 py-2 text-xs font-semibold text-slate-700">
          <span class="h-2 w-2 rounded-full bg-[#0194F3]"></span>
          Hubungi Kami
        </div>
        <h1 class="mt-4 text-3xl md:text-4xl font-extrabold text-slate-900">{{ $title }}</h1>
        <p class="mt-3 text-slate-600 max-w-xl">
          Untuk bantuan pemesanan, pertanyaan paket, atau kerja sama, silakan hubungi kontak resmi kami di bawah ini.
        </p>
      </div>

      <div class="lg:col-span-5">
        <div class="rounded-3xl border border-slate-200 bg-white/85 shadow-sm p-6">
          <div class="text-sm font-bold text-slate-900">Kontak Resmi</div>

          <div class="mt-4 space-y-3 text-sm text-slate-700">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
              <div class="text-xs text-slate-500">Alamat</div>
              <div class="font-semibold text-slate-900 mt-1">{{ $addr }}</div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Telepon</div>
                <div class="font-semibold text-slate-900 mt-1">{{ $phone }}</div>
              </div>
              <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="text-xs text-slate-500">Email</div>
                <div class="mt-1 font-semibold text-slate-900 break-all">{{ $email }}</div>

              </div>
            </div>

            @if(!empty($wa))
              <a target="_blank"
                 href="https://wa.me/{{ preg_replace('/\D+/', '', $wa) }}"
                 class="inline-flex w-full items-center justify-center rounded-xl bg-[#0194F3] px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">
                Chat WhatsApp
              </a>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="bg-slate-50">
  <div class="max-w-7xl mx-auto px-4 py-10">
    <div class="grid gap-6 lg:grid-cols-12">
      <div class="lg:col-span-7">
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm p-6 md:p-8">
          <h2 class="text-xl font-extrabold text-slate-900">Informasi Tambahan</h2>
          

          <div class="mt-6 prose prose-slate max-w-none">
            {!! $html !!}
          </div>
        </div>
      </div>

      
    </div>
  </div>
</section>
@endsection
