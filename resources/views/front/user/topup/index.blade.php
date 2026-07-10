@extends('layouts.front')

@section('content')
<div class="pt-24 pb-20 min-h-screen bg-slate-50">
    <div class="max-w-4xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-slate-800 mb-8">Top Up Poin & Kuota</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-[#0194F3]/5 rounded-bl-full -z-10"></div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Poin Properti</h3>
                <p class="text-slate-500 mb-6 text-sm">Gunakan poin ini untuk memasang iklan properti (jual/sewa) atau menaikkan posisi iklan Anda.</p>
                <div class="text-3xl font-black text-[#0194F3] mb-6">0 <span class="text-base font-semibold text-slate-500">Poin</span></div>
                <button class="w-full py-3 bg-[#0194F3] text-white font-bold rounded-xl hover:bg-blue-600 transition">Top Up Sekarang</button>
            </div>

            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500/5 rounded-bl-full -z-10"></div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Poin Barang & Jasa</h3>
                <p class="text-slate-500 mb-6 text-sm">Gunakan poin ini khusus untuk mengiklankan produk barang atau menawarkan layanan jasa Anda.</p>
                <div class="text-3xl font-black text-purple-600 mb-6">0 <span class="text-base font-semibold text-slate-500">Poin</span></div>
                <button class="w-full py-3 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 transition">Top Up Sekarang</button>
            </div>
        </div>

    </div>
</div>
@endsection
