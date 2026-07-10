@extends('layouts.front')

@section('content')
<div class="pt-24 pb-20 min-h-screen bg-slate-50">
    <div class="max-w-3xl mx-auto px-4">
        
        <div class="mb-8">
            <a href="{{ route('iklan.saya') }}" class="text-[#0194F3] font-semibold flex items-center gap-1 hover:underline mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
            <h1 class="text-3xl font-bold text-slate-800">Pasang Iklan Baru</h1>
            <p class="text-slate-600">Kategori Terpilih: <span class="font-bold text-[#0194F3] capitalize">{{ $kategori }}</span></p>
        </div>

        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
            <div class="text-center py-10 text-slate-500">
                <i data-lucide="wrench" class="w-12 h-12 mx-auto mb-4 text-slate-300"></i>
                <h3 class="text-xl font-bold text-slate-700 mb-2">Form Pemasangan Iklan</h3>
                <p>Formulir detail untuk kategori <b>{{ $kategori }}</b> sedang disiapkan.</p>
            </div>
        </div>

    </div>
</div>
@endsection
