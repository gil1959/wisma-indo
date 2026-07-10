@extends('layouts.admin')
@section('title', 'Halaman Legal')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">

  <div class="flex items-start justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">Halaman Legal</h1>
      <p class="text-sm text-slate-600 mt-1">
        Kelola konten Privacy Policy, Terms & Conditions, dan Contact dari satu tempat.
      </p>
    </div>

    <a href="{{ route('privacy-policy') }}" target="_blank"
       class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
      Preview Privacy
    </a>
  </div>

  @if(session('success'))
    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900">
      <div class="font-semibold">Berhasil</div>
      <div class="text-sm text-emerald-800">{{ session('success') }}</div>
    </div>
  @endif

  @if($errors->any())
    <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-900">
      <div class="font-semibold">Validasi gagal</div>
      <ul class="mt-2 list-disc pl-5 text-sm text-rose-800">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.legal-pages.update') }}" class="space-y-6">
    @csrf

    {{-- PRIVACY --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
      <div class="px-5 py-4 font-extrabold text-white flex items-center justify-between" style="background:#0194F3;">
        <span>Privacy Policy</span>
        <span class="text-white/90 text-sm">/privacy-policy</span>
      </div>

      <div class="p-5 space-y-4">
        <div>
          <label class="block text-sm font-bold text-slate-800 mb-1">Judul</label>
          <input type="text" name="legal_privacy_title"
                 value="{{ old('legal_privacy_title', $settings['legal_privacy_title'] ?? 'Privacy Policy') }}"
                 class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        </div>

        <div>
          <label class="block text-sm font-bold text-slate-800 mb-1">Konten</label>
          <textarea name="legal_privacy_html"
                    class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                    rows="12">{{ old('legal_privacy_html', $settings['legal_privacy_html'] ?? '') }}</textarea>
          <div class="text-xs text-slate-500 mt-2">
            Bisa isi HTML dari editor. Pastikan ada poin: data yang dikumpulkan, penggunaan data, dan kontak.
          </div>
        </div>
      </div>
    </div>

    {{-- TERMS --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
      <div class="px-5 py-4 font-extrabold text-white flex items-center justify-between" style="background:#0F172A;">
        <span>Terms & Conditions</span>
        <span class="text-white/80 text-sm">/terms-conditions</span>
      </div>

      <div class="p-5 space-y-4">
        <div>
          <label class="block text-sm font-bold text-slate-800 mb-1">Judul</label>
          <input type="text" name="legal_terms_title"
                 value="{{ old('legal_terms_title', $settings['legal_terms_title'] ?? 'Terms & Conditions') }}"
                 class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        </div>

        <div>
          <label class="block text-sm font-bold text-slate-800 mb-1">Konten</label>
          <textarea name="legal_terms_html"
                    class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                    rows="12">{{ old('legal_terms_html', $settings['legal_terms_html'] ?? '') }}</textarea>
          <div class="text-xs text-slate-500 mt-2">
            Pastikan ada: aturan pemesanan, pembayaran, pembatalan/refund, perubahan jadwal, tanggung jawab.
          </div>
        </div>
      </div>
    </div>

    {{-- CONTACT --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
      <div class="px-5 py-4 font-extrabold text-white flex items-center justify-between" style="background:#16A34A;">
        <span>Contact</span>
        <span class="text-white/90 text-sm">/contact</span>
      </div>

      <div class="p-5 space-y-4">
        <div>
          <label class="block text-sm font-bold text-slate-800 mb-1">Judul</label>
          <input type="text" name="legal_contact_title"
                 value="{{ old('legal_contact_title', $settings['legal_contact_title'] ?? 'Contact') }}"
                 class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
        </div>

        <div>
          <label class="block text-sm font-bold text-slate-800 mb-1">Konten</label>
          <textarea name="legal_contact_html"
                    class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                    rows="12">{{ old('legal_contact_html', $settings['legal_contact_html'] ?? '') }}</textarea>
          <div class="text-xs text-slate-500 mt-2">
            Kontak utama (alamat/telp/email/wa) tetap tampil dari footer settings, ini untuk tambahan seperti jam operasional, maps embed, dll.
          </div>
        </div>
      </div>
    </div>

    <div class="flex justify-end">
      <button type="submit"
              class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-bold text-white hover:bg-slate-800">
        Simpan
      </button>
    </div>
  </form>
</div>

@include('admin.partials.wysiwyg')
@endsection
