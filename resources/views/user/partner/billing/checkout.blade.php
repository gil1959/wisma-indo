@extends('user.layouts.app')

@section('title', 'Checkout Paket')
@section('page-title', 'Checkout Paket')
@section('page-subtitle', 'Pilih metode pembayaran untuk langganan paket Anda.')

@section('content')
<div class="max-w-4xl">
        
        <div class="mb-8">
            <a href="{{ route('partner.billing') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-[#0194F3] transition font-medium">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Pilihan Paket
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- Bagian Kiri: Pilihan Pembayaran --}}
            <div class="lg:col-span-7 space-y-6">
                <h1 class="text-2xl font-extrabold text-slate-800">Pilih Metode Pembayaran</h1>
                
                @if(session('error'))
                    <div class="p-4 bg-red-50 text-red-600 rounded-xl border border-red-200 font-semibold shadow-sm text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('partner.billing.process', $package->id) }}" method="POST" id="checkout-form">
                    @csrf
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6 p-6">
                        <h4 class="font-bold text-slate-700 text-sm mb-4 border-b border-slate-100 pb-2">Silakan Pilih Metode Pembayaran:</h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Offline Methods --}}
                            @forelse($offlineMethods as $method)
                                <label class="flex items-center p-4 border border-slate-200 rounded-xl bg-white cursor-pointer hover:border-[#0194F3] transition shadow-sm hover:shadow-md">
                                    <input type="radio" name="payment_method" value="offline|{{ $method->id }}" class="w-5 h-5 text-[#0194F3] focus:ring-[#0194F3]" required>
                                    <div class="ml-3 flex-grow">
                                        <span class="block font-bold text-slate-800 text-sm">{{ $method->name }}</span>
                                        <span class="block text-xs text-slate-500 mt-0.5">Transfer Manual (Verifikasi Admin)</span>
                                    </div>
                                    <i data-lucide="building" class="w-6 h-6 text-slate-400"></i>
                                </label>
                            @empty
                            @endforelse

                            {{-- PG Methods --}}
                            @foreach($pgChannels as $channel)
                                <label class="flex items-center p-4 border border-slate-200 rounded-xl bg-white cursor-pointer hover:border-[#0194F3] transition shadow-sm hover:shadow-md">
                                    <input type="radio" name="payment_method" value="pg|{{ $channel['code'] }}" class="w-5 h-5 text-[#0194F3] focus:ring-[#0194F3]" required>
                                    <div class="ml-3 flex-grow">
                                        <span class="block font-bold text-slate-800 text-sm">{{ $channel['name'] }}</span>
                                        <span class="block text-xs text-emerald-600 mt-0.5">Pembayaran Otomatis</span>
                                    </div>
                                    @if($channel['logo'])
                                        <img src="{{ $channel['logo'] }}" alt="{{ $channel['name'] }}" class="h-6 max-w-[60px] object-contain">
                                    @else
                                        <i data-lucide="credit-card" class="w-6 h-6 text-slate-400"></i>
                                    @endif
                                </label>
                            @endforeach
                            
                            @if(count($offlineMethods) == 0 && count($pgChannels) == 0)
                                <div class="col-span-full p-4 bg-amber-50 text-amber-700 text-sm rounded-xl border border-amber-200">
                                    Belum ada metode pembayaran yang diaktifkan oleh Admin.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8">
                        <button type="submit" class="w-full py-4 bg-[#0194F3] hover:bg-blue-600 text-white font-bold text-lg rounded-xl transition shadow-lg shadow-[#0194F3]/30">
                            Bayar Sekarang
                        </button>
                    </div>
                </form>
            </div>
            
            {{-- Bagian Kanan: Detail Pesanan --}}
            <div class="lg:col-span-5">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sticky top-6">
                        <h2 class="text-xl font-bold text-slate-800 mb-6">Ringkasan Pembelian</h2>
                        
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-bold text-slate-700">Paket {{ $package->name }}</h3>
                                    <p class="text-sm text-slate-500">Kuota Listing: {{ $package->listing_quota == -1 ? 'Unlimited' : $package->listing_quota }}</p>
                                    <p class="text-sm text-slate-500">Masa Aktif: {{ $package->duration_days }} Hari</p>
                                </div>
                                <div class="font-bold text-[#0194F3]">Rp {{ number_format($package->price, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        
                        <div class="border-t border-slate-100 pt-4 mb-6">
                            <div class="flex justify-between items-center text-lg font-black text-slate-800">
                                <span>Total Pembayaran</span>
                                <span>Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-2 text-right">*Belum termasuk kode unik (jika transfer manual)</p>
                        </div>
                    </div>
            </div>

        </div>
    </div>
</div>
@endsection
