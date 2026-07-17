@extends('layouts.front')

@section('content')
<div class="bg-slate-50 min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-6xl">
        
        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold text-slate-800 mb-3">Pilih Paket Top Up Kuota</h1>
            <p class="text-slate-500 max-w-xl mx-auto">Beli kuota untuk memasang lebih banyak iklan properti, barang, dan jasa dengan proses instan.</p>
        </div>

        @if(session('error'))
            <div class="mb-8 max-w-2xl mx-auto p-4 bg-red-50 text-red-600 rounded-xl border border-red-200 text-center font-semibold shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- VOUCHER PROMO SECTION --}}
        @if($voucherPackages->count() > 0)
        <div class="mb-14">
            <div class="flex items-center gap-3 mb-6">
                <i data-lucide="sparkles" class="w-6 h-6 text-amber-500"></i>
                <h2 class="text-2xl font-extrabold text-slate-800">Voucher Promo Spesial</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($voucherPackages as $pkg)
                    <div class="bg-white rounded-3xl border-2 border-amber-200 shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden relative flex flex-col group">
                        
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-amber-400 to-orange-500"></div>

                        @if($pkg->discount_label)
                            <div class="absolute top-4 right-4 bg-gradient-to-r from-orange-500 to-red-500 text-white text-xs font-bold px-4 py-1.5 rounded-full shadow-md z-10 animate-bounce">
                                {{ $pkg->discount_label }}
                            </div>
                        @endif
                        
                        <div class="p-8 text-center border-b border-amber-50 relative overflow-hidden bg-amber-50/30">
                            <h3 class="text-xl font-extrabold text-slate-800 mb-2 relative z-10">{{ $pkg->amount }} Listing Kuota</h3>
                            
                            @if($pkg->original_price && $pkg->original_price > $pkg->price)
                                <div class="text-slate-400 line-through text-sm font-bold mb-1">
                                    Rp {{ number_format($pkg->original_price, 0, ',', '.') }}
                                </div>
                            @endif
                            
                            <div class="text-orange-600 font-black text-4xl relative z-10 flex items-start justify-center gap-1">
                                <span class="text-lg mt-1">Rp</span>
                                {{ number_format($pkg->price, 0, ',', '.') }}
                            </div>

                            @if($pkg->bonus)
                                <div class="inline-flex mt-4 items-center justify-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full font-bold text-xs relative z-10">
                                    <i data-lucide="gift" class="w-3.5 h-3.5"></i> Ekstra +{{ $pkg->bonus }} Iklan
                                </div>
                            @endif
                        </div>
                        
                        <div class="p-6 flex-grow flex flex-col justify-between">
                            <ul class="space-y-3 mb-8">
                                <li class="flex items-start gap-3 text-sm text-slate-600 font-medium">
                                    <i data-lucide="check" class="w-5 h-5 text-orange-500 shrink-0"></i>
                                    <span>Berlaku untuk semua kategori iklan.</span>
                                </li>
                                @if($pkg->valid_until)
                                    <li class="flex items-start gap-3 text-sm text-slate-600 font-medium">
                                        <i data-lucide="clock" class="w-5 h-5 text-orange-500 shrink-0"></i>
                                        <span>Promo berakhir pada: <br><strong class="text-slate-800">{{ \Carbon\Carbon::parse($pkg->valid_until)->format('d M Y, H:i') }}</strong></span>
                                    </li>
                                @endif
                            </ul>
                            
                            <a href="{{ route('topup.checkout', $pkg->id) }}" class="block w-full py-3.5 px-4 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-bold text-center rounded-xl transition shadow-lg shadow-orange-500/30">
                                Klaim Promo Sekarang
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- REGULAR PACKAGES SECTION --}}
        <div>
            @if($voucherPackages->count() > 0 && $regularPackages->count() > 0)
                <div class="flex items-center gap-3 mb-6 mt-12">
                    <i data-lucide="package" class="w-6 h-6 text-[#0194F3]"></i>
                    <h2 class="text-2xl font-extrabold text-slate-800">Paket Reguler</h2>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($regularPackages as $pkg)
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden relative flex flex-col">
                        @if($pkg->discount_label)
                            <div class="absolute top-4 right-4 bg-emerald-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md z-10">
                                {{ $pkg->discount_label }}
                            </div>
                        @endif
                        
                        <div class="p-8 text-center border-b border-slate-50 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-[#0194F3]/5 rounded-bl-full -z-0"></div>
                            <h3 class="text-xl font-bold text-slate-800 mb-2 relative z-10">{{ $pkg->amount }} Listing</h3>
                            <div class="text-[#0194F3] font-black text-3xl mb-1 relative z-10 flex justify-center items-start gap-1">
                                <span class="text-base mt-1">Rp</span>{{ number_format($pkg->price, 0, ',', '.') }}
                            </div>
                            @if($pkg->bonus)
                                <div class="text-emerald-500 font-bold text-sm mt-2 flex items-center justify-center gap-1.5 relative z-10">
                                    <i data-lucide="gift" class="w-4 h-4"></i> +{{ $pkg->bonus }} Iklan Gratis
                                </div>
                            @endif
                        </div>
                        
                        <div class="p-6 bg-slate-50 flex-grow flex flex-col justify-between">
                            <ul class="space-y-3 mb-8">
                                <li class="flex items-start gap-3 text-sm text-slate-600">
                                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500 shrink-0"></i>
                                    <span>Bisa untuk semua kategori iklan.</span>
                                </li>
                                <li class="flex items-start gap-3 text-sm text-slate-600">
                                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500 shrink-0"></i>
                                    <span>Masa aktif kuota selamanya (Lifetime).</span>
                                </li>
                            </ul>
                            
                            <a href="{{ route('topup.checkout', $pkg->id) }}" class="block w-full py-3.5 px-4 bg-white border-2 border-[#0194F3] text-[#0194F3] hover:bg-[#0194F3] hover:text-white font-bold text-center rounded-xl transition shadow-sm hover:shadow-lg hover:shadow-[#0194F3]/30">
                                Beli Paket Ini
                            </a>
                        </div>
                    </div>
                @empty
                    @if($voucherPackages->count() == 0)
                        <div class="col-span-full py-12 text-center text-slate-500 bg-white rounded-3xl border border-slate-200">
                            <div class="w-20 h-20 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                <i data-lucide="package-x" class="w-10 h-10 text-slate-400"></i>
                            </div>
                            <p class="text-lg font-semibold">Belum ada paket Top Up yang tersedia saat ini.</p>
                        </div>
                    @endif
                @endforelse
            </div>
        </div>
        
    </div>
</div>
@endsection
