@extends('layouts.front')
@php $isEn = app()->getLocale() === 'en'; @endphp
@section('title', ($title ?? 'Terms & Conditions') . ' - ' . ($siteSettings['seo_site_title'] ?? 'Bintang Wisata'))

@section('content')
<section class="relative overflow-hidden bg-white">
  <div class="absolute inset-0 travel-grid opacity-70"></div>
  <div class="absolute -top-32 -left-32 h-96 w-96 rounded-full blur-3xl opacity-30"
    style="background: radial-gradient(circle, #0F172A 0%, transparent 60%);"></div>

  <div class="relative max-w-7xl mx-auto px-4 pt-10 pb-10 lg:pt-16 lg:pb-16">
    <div class="grid gap-10 lg:grid-cols-12 lg:items-center">
      <div class="lg:col-span-8">
        <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/70 px-4 py-2 text-xs font-semibold text-slate-700">
          <span class="h-2 w-2 rounded-full bg-slate-900"></span>
          {{ $isEn ? 'Terms & Conditions' : 'Syarat & Ketentuan' }}
        </div>
        <h1 class="mt-4 text-3xl md:text-4xl font-extrabold text-slate-900">{{ $title }}</h1>
        <p class="mt-3 text-slate-600 max-w-2xl">
          {{ $isEn
    ? 'Rules for using our services, bookings, payments, schedule changes, and other conditions.'
    : 'Aturan penggunaan layanan, pemesanan, pembayaran, perubahan jadwal, dan ketentuan lainnya.'
}}
        </p>
      </div>

      <div class="lg:col-span-4">
        <div class="rounded-3xl border border-slate-200 bg-white/80 shadow-sm p-6">
          <div class="text-sm font-bold text-slate-900">{{ $isEn ? 'Need Help?' : 'Butuh Bantuan?' }}</div>
          <p class="mt-2 text-sm text-slate-600">
            {{ $isEn
      ? 'If you have questions about service terms, please contact our team.'
      : 'Jika ada pertanyaan mengenai ketentuan layanan, silakan hubungi tim kami.'
  }}
          </p>
          <a href="{{ route('contact') }}"
            class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800">
            {{ $isEn ? 'Go to Contact Page' : 'Ke Halaman Contact' }}
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