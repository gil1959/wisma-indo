@extends('layouts.front')

@section('content')
<div class="pt-24 pb-20 min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-slate-800 mb-8">Dashboard Akun Saya</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center">
                <i data-lucide="user" class="w-12 h-12 text-[#0194F3] mb-4"></i>
                <h3 class="text-xl font-bold text-slate-800">Profil Saya</h3>
                <p class="text-slate-500 mb-4">Atur informasi pribadi Anda.</p>
                <a href="#" class="px-6 py-2 bg-[#0194F3]/10 text-[#0194F3] font-bold rounded-xl hover:bg-[#0194F3]/20 transition">Edit Profil</a>
            </div>
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center">
                <i data-lucide="wallet" class="w-12 h-12 text-emerald-500 mb-4"></i>
                <h3 class="text-xl font-bold text-slate-800">Saldo Poin</h3>
                <p class="text-slate-500 mb-4">Poin Properti: 0 | Poin B&J: 0</p>
                <a href="{{ route('topup') }}" class="px-6 py-2 bg-emerald-50 text-emerald-600 font-bold rounded-xl hover:bg-emerald-100 transition">Top Up</a>
            </div>
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center">
                <i data-lucide="list" class="w-12 h-12 text-orange-500 mb-4"></i>
                <h3 class="text-xl font-bold text-slate-800">Iklan Aktif</h3>
                <p class="text-slate-500 mb-4">0 Properti | 0 B&J</p>
                <a href="{{ route('iklan.saya') }}" class="px-6 py-2 bg-orange-50 text-orange-600 font-bold rounded-xl hover:bg-orange-100 transition">Kelola Iklan</a>
            </div>
        </div>
    </div>
</div>
@endsection
