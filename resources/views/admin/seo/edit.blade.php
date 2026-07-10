@extends('layouts.admin')

@section('title', 'SEO')

@section('content')
<div class="max-w-5xl mx-auto">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">SEO</h1>
    <a href="{{ url('/') }}" target="_blank" class="text-sm font-semibold" style="color:#0194F3;">
      Lihat Website →
    </a>
  </div>

  @if(session('success'))
    <div class="mb-4 p-3 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200">
      {{ session('success') }}
    </div>
  @endif

  <form method="POST" action="{{ route('admin.seo.update') }}" class="bg-white border rounded-2xl p-6">
    @csrf

    <div class="flex items-center gap-2 mb-5">
      <div class="h-9 w-9 rounded-xl grid place-items-center border"
           style="background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22)">
        <i data-lucide="globe" class="w-5 h-5" style="color:#0194F3;"></i>
      </div>
      <div class="font-extrabold text-slate-900">Pengaturan SEO Global</div>
    </div>

    <div class="mb-4">
      <label class="block text-sm font-semibold mb-2">Judul Website (Title Tag)</label>
      <input name="seo_site_title" class="w-full rounded-xl border-slate-200"
             value="{{ old('seo_site_title', $settings['seo_site_title'] ?? 'Bintang Wisata - Tour & Travel Terpercaya') }}" />
      @error('seo_site_title')
        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-4">
      <label class="block text-sm font-semibold mb-2">Deskripsi (Meta Description)</label>
      <textarea name="seo_meta_description" rows="4" class="w-full rounded-xl border-slate-200">{{ old('seo_meta_description', $settings['seo_meta_description'] ?? '') }}</textarea>
      @error('seo_meta_description')
        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-6">
      <label class="block text-sm font-semibold mb-2">Keywords (Pisahkan koma)</label>
      <input name="seo_keywords" class="w-full rounded-xl border-slate-200"
             value="{{ old('seo_keywords', $settings['seo_keywords'] ?? '') }}" />
      @error('seo_keywords')
        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
      @enderror
    </div>

    <button type="submit" class="btn btn-primary">
      <i data-lucide="save" class="w-4 h-4"></i>
      Simpan SEO
    </button>
  </form>
</div>
@endsection
