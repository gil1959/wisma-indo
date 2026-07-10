@extends('layouts.front')
@php $isEn = app()->getLocale() === 'en'; @endphp
@section('title', ($title ?? 'Privacy Policy') . ' - ' . ($siteSettings['seo_site_title'] ?? 'Bintang Wisata'))

@section('content')
<section class="relative overflow-hidden bg-white">
  <div class="absolute inset-0 travel-grid opacity-70"></div>
  <div class="absolute -top-32 -right-32 h-96 w-96 rounded-full blur-3xl opacity-35"
    style="background: radial-gradient(circle, #0194F3 0%, transparent 60%);"></div>

  <div class="relative max-w-7xl mx-auto px-4 pt-10 pb-10 lg:pt-16 lg:pb-16">
    <div class="grid gap-10 lg:grid-cols-12 lg:items-center">
      <div class="lg:col-span-7">
        <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/70 px-4 py-2 text-xs font-semibold text-slate-700">
          <span class="h-2 w-2 rounded-full" style="background:#0194F3;"></span>
          {{ $isEn ? 'Privacy Policy' : 'Kebijakan Privasi' }}
        </div>
        <h1 class="mt-4 text-3xl md:text-4xl font-extrabold text-slate-900">{{ $title }}</h1>
        <p class="mt-3 text-slate-600 max-w-xl">
          {{ $isEn
    ? 'This document explains how we handle user data for travel services and bookings.'
    : 'Dokumen ini menjelaskan bagaimana kami mengelola data pengguna untuk layanan perjalanan dan pemesanan.'
}}
        </p>
      </div>

      <div class="lg:col-span-5">
        <div class="rounded-3xl border border-slate-200 bg-white/80 shadow-sm p-6">
          <div class="text-sm font-bold text-slate-900">{{ $isEn ? 'Quick Contact' : 'Kontak Cepat' }}</div>
          <div class="mt-3 space-y-2 text-sm text-slate-700">
            <div><span class="font-semibold">{{ $isEn ? 'Email:' : 'Email:' }}</span> {{ $siteSettings['footer_email'] ?? '-' }}</div>
            <div><span class="font-semibold">{{ $isEn ? 'Phone:' : 'Telepon:' }}</span> {{ $siteSettings['footer_phone'] ?? '-' }}</div>
            <div><span class="font-semibold">{{ $isEn ? 'Address:' : 'Alamat:' }}</span> {{ $siteSettings['footer_address'] ?? '-' }}</div>
          </div>
          <a href="{{ route('contact') }}"
            class="mt-5 inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800">
            Hubungi Kami
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="bg-slate-50">
  <div class="max-w-7xl mx-auto px-4 py-10">
    <div class="mx-auto max-w-4xl rounded-3xl border border-slate-200 bg-white shadow-sm p-6 md:p-8">
      <div class="prose prose-slate max-w-none">
        {!! $html !!}
      </div>
    </div>
  </div>
</section>
@endsection