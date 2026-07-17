@extends('layouts.front')

@section('content')
<div class="pt-24 pb-20 min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col lg:flex-row gap-8">
            
            {{-- LEFT SIDEBAR --}}
            <div class="w-full lg:w-1/3 xl:w-1/4 flex flex-col gap-6">
                
                {{-- Profile Card --}}
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex flex-col items-center mb-6">
                        <div class="w-24 h-24 rounded-full bg-slate-200 mb-4 overflow-hidden border-4 border-slate-50">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset(Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                {{-- Avatar Placeholder --}}
                                <svg class="w-full h-full text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            @endif
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">{{ Auth::user()->name }}</h3>
                        <p class="text-xs text-slate-500 uppercase tracking-wide">&#64;{{ strtoupper(str_replace(' ', '', Auth::user()->name)) }}-{{ Auth::id() }}</p>
                    </div>

                    <div class="space-y-4 text-sm mt-6 pt-6 border-t border-slate-100">
                        <div class="flex items-start gap-3">
                            <i data-lucide="mail" class="w-5 h-5 text-slate-400 shrink-0 mt-0.5"></i>
                            <div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Email</div>
                                <div class="text-slate-800 font-medium">{{ Auth::user()->email }}</div>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-3">
                            <i data-lucide="phone" class="w-5 h-5 text-slate-400 shrink-0 mt-0.5"></i>
                            <div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nomor Whatsapp</div>
                                <div class="text-slate-800 font-medium">{{ Auth::user()->phone ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i data-lucide="map-pin" class="w-5 h-5 text-slate-400 shrink-0 mt-0.5"></i>
                            <div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Lokasi</div>
                                <div class="text-slate-800 font-medium">{{ Auth::user()->sub_district ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i data-lucide="home" class="w-5 h-5 text-slate-400 shrink-0 mt-0.5"></i>
                            <div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Alamat</div>
                                <div class="text-slate-800 font-medium">{{ Auth::user()->full_address ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i data-lucide="calendar" class="w-5 h-5 text-slate-400 shrink-0 mt-0.5"></i>
                            <div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Bergabung Sejak</div>
                                <div class="text-slate-800 font-medium">{{ Auth::user()->created_at->format('d M Y') }}</div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <i data-lucide="align-left" class="w-5 h-5 text-slate-400 shrink-0 mt-0.5"></i>
                            <div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Bio</div>
                                <div class="text-slate-800 font-medium">{{ Auth::user()->bio ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Menu Dashboard --}}
                <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3 px-2">Menu Dashboard</div>
                    <div class="space-y-1">
                        <a href="{{ route('iklan.saya') }}" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-sm transition group">
                            <div class="flex items-center gap-3">
                                <i data-lucide="grid" class="w-5 h-5 text-slate-500 group-hover:text-slate-700 transition"></i>
                                List Iklan Saya
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-sm transition group">
                            <div class="flex items-center gap-3">
                                <i data-lucide="edit" class="w-5 h-5 text-slate-500 group-hover:text-slate-700 transition"></i>
                                Edit Profil
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}" class="block w-full">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-between p-3 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 font-bold text-sm transition group">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="log-out" class="w-5 h-5 text-red-500 group-hover:text-red-600 transition"></i>
                                    Keluar
                                </div>
                                <i data-lucide="chevron-right" class="w-4 h-4 text-red-400"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>

            {{-- RIGHT CONTENT --}}
            <div class="w-full lg:w-2/3 xl:w-3/4">
                
                {{-- Header --}}
                <div class="mb-8">
                    <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Akun Saya</h1>
                    <p class="text-slate-500 text-sm sm:text-base">Kelola informasi profil, kuota iklan, dan pantau riwayat top up Anda di sini.</p>
                </div>

                @php
                    $quota = Auth::user()->quota;
                    // Hitung total iklan terpakai dari user
                    $terpakai = Auth::user()->listings()->count();
                    $sisa = $quota->listing_quota ?? 0;
                    $total = $terpakai + $sisa;
                @endphp

                {{-- KUOTA IKLAN --}}
                <div class="mb-10">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Kuota Iklan (Semua Kategori)</h2>
                        <a href="{{ route('transaksi') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition">
                            <i data-lucide="clock" class="w-3.5 h-3.5"></i> Histori
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-4">
                        {{-- Kuota Iklan --}}
                        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm relative overflow-hidden">
                            <div class="flex items-center justify-between gap-3 mb-8">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-[#0194F3]/10 flex items-center justify-center">
                                        <i data-lucide="image" class="w-5 h-5 text-[#0194F3]"></i>
                                    </div>
                                    <span class="font-extrabold text-slate-700 text-base">SISA KUOTA IKLAN ANDA</span>
                                </div>
                                <a href="{{ route('topup') }}" class="px-4 py-2 bg-[#0194F3] hover:bg-blue-600 text-white font-bold text-sm rounded-xl transition shadow-md shadow-[#0194F3]/20">
                                    + Top Up
                                </a>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4 text-center border-t border-slate-100 pt-6">
                                <div>
                                    <div class="text-xs font-bold text-slate-400 mb-1">TOTAL DIBELI</div>
                                    <div class="text-2xl font-black text-slate-800">{{ $total }}</div>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-400 mb-1">TERPAKAI</div>
                                    <div class="text-2xl font-black text-slate-800">{{ $terpakai }}</div>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-[#0194F3] mb-1">SISA KUOTA</div>
                                    <div class="text-3xl font-black text-[#0194F3]">{{ $sisa }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
