@extends('layouts.front')

@section('content')
<div class="pt-24 pb-20 min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-slate-800 mb-8">Riwayat Transaksi</h1>
        
        <div class="bg-white rounded-3xl p-12 border border-slate-200 text-center flex flex-col items-center justify-center">
            <div class="w-20 h-20 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="receipt" class="w-10 h-10"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Belum Ada Transaksi</h3>
            <p class="text-slate-500 mb-6">Anda belum pernah melakukan top up atau transaksi berbayar lainnya.</p>
            <a href="{{ route('topup') }}" class="px-6 py-2 bg-[#0194F3] text-white font-bold rounded-xl hover:bg-blue-600 transition">
                Top Up Sekarang
            </a>
        </div>
    </div>
</div>
@endsection
