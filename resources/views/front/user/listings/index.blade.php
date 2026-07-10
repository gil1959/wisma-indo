@extends('layouts.front')

@section('content')
<div class="pt-24 pb-20 min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-slate-800">Iklan Saya</h1>
            <a href="{{ route('pasang.iklan') }}" class="px-6 py-2 bg-[#0194F3] text-white font-bold rounded-xl hover:bg-blue-600 transition flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i> Pasang Iklan Baru
            </a>
        </div>
        
        <div class="bg-white rounded-3xl p-12 border border-slate-200 text-center flex flex-col items-center justify-center">
            <div class="w-20 h-20 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="inbox" class="w-10 h-10"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Belum Ada Iklan</h3>
            <p class="text-slate-500 mb-6">Anda belum memasang iklan apapun. Mulai pasang iklan pertama Anda sekarang!</p>
        </div>
    </div>
</div>
@endsection
