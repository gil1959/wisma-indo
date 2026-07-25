@extends('user.layouts.app')
@section('title', 'Pengaturan WhatsApp')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-slate-800">Pengaturan Pesan WhatsApp</h2>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm max-w-2xl">
        <form action="{{ route('partner.whatsapp.update') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="bg-blue-50 text-blue-800 p-4 rounded-xl text-sm mb-4">
                <p class="font-bold mb-1">Panduan Penggunaan Template</p>
                <p>Anda dapat menyesuaikan teks pesan default yang dikirimkan calon pembeli ketika mereka menekan tombol WhatsApp di iklan Anda.</p>
                <p class="mt-2">Gunakan <strong>{link Iklan}</strong> pada template agar sistem otomatis menyisipkan link iklan yang sedang dilihat pembeli.</p>
            </div>

            <div>
                <label for="whatsapp_template" class="block text-sm font-bold text-slate-700 mb-2">Template Pesan WhatsApp</label>
                <textarea id="whatsapp_template" name="whatsapp_template" rows="4" class="w-full rounded-xl border-slate-200 focus:ring focus:ring-blue-200 focus:border-blue-400" placeholder="Halo kak, saya melihat iklan di {link Iklan} apakah masih tersedia.">{{ old('whatsapp_template', $user->whatsapp_template) }}</textarea>
                <p class="text-xs text-slate-500 mt-2">Biarkan kosong untuk menggunakan template default: <br><i>Halo kak, saya melihat iklan di {link Iklan} apakah masih tersedia.</i></p>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-[#0194F3] text-white font-bold rounded-xl shadow-md hover:bg-blue-600 transition">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
