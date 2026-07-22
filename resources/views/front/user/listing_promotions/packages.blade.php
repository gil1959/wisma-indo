@extends('layouts.front')
@section('title', 'Pilih Paket Promosi Iklan')

@section('content')
<div class="min-h-screen bg-slate-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('iklan.saya') }}" class="text-indigo-600 hover:text-indigo-700 font-medium inline-flex items-center gap-2 mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Iklan Saya
            </a>
            <h1 class="text-3xl font-bold text-slate-900">
                {{ request('type') == 'sundul' ? 'Beli Paket Sundulan' : (request('type') == 'premium' ? 'Beli Paket Premium' : 'Promosikan Iklan') }}
            </h1>
            <p class="text-slate-600 mt-2">Pilih paket untuk meningkatkan visibilitas iklan Anda: <span class="font-semibold text-slate-900">{{ $listing->title }}</span></p>

        </div>

        @if(session('error'))
        <div class="bg-red-50 text-red-600 p-4 rounded-2xl mb-6 flex items-start gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 mt-0.5"></i>
            <p>{{ session('error') }}</p>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($packages as $package)
                <div class="bg-white rounded-3xl p-6 border {{ $package->type == 'premium' ? 'border-yellow-400 shadow-md relative overflow-hidden' : 'border-slate-200' }} flex flex-col h-full">
                    @if($package->type == 'premium')
                        <div class="absolute top-0 right-0 bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-bl-xl uppercase tracking-wider">
                            Rekomendasi
                        </div>
                    @endif
                    <div class="flex-grow">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-2xl {{ $package->type == 'premium' ? 'bg-yellow-50 text-yellow-600' : 'bg-indigo-50 text-indigo-600' }} flex items-center justify-center text-xl shrink-0">
                                <i data-lucide="{{ $package->type == 'premium' ? 'star' : 'arrow-up' }}" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 leading-tight">{{ $package->name }}</h3>
                                @if($package->discount_label)
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-red-100 text-red-600 text-[10px] font-bold rounded uppercase tracking-wide">{{ $package->discount_label }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-6">
                            @if($package->original_price)
                                <div class="text-sm text-slate-400 line-through font-medium mb-1">
                                    Rp {{ number_format($package->original_price, 0, ',', '.') }}
                                </div>
                            @endif
                            <div class="text-3xl font-black text-slate-900">
                                Rp {{ number_format($package->price, 0, ',', '.') }}
                            </div>
                        </div>
                        <ul class="space-y-3 mb-8">
                            @if(is_array($package->benefits) && count($package->benefits) > 0)
                                @foreach($package->benefits as $benefit)
                                    <li class="flex items-start gap-3 text-sm text-slate-600">
                                        <i data-lucide="check" class="w-4 h-4 text-green-500 mt-0.5 shrink-0"></i>
                                        <span>{!! preg_replace('/(\d+x|Premium)/i', '<strong class="text-slate-900">$1</strong>', e($benefit)) !!}</span>
                                    </li>
                                @endforeach
                            @else
                                <!-- Fallback if no benefits are set in DB -->
                                @if($package->type == 'premium')
                                    <li class="flex items-start gap-3 text-sm text-slate-600">
                                        <i data-lucide="check" class="w-4 h-4 text-green-500 mt-0.5 shrink-0"></i>
                                        <span>Label <strong class="text-slate-900">Premium</strong> di semua halaman</span>
                                    </li>
                                    <li class="flex items-start gap-3 text-sm text-slate-600">
                                        <i data-lucide="check" class="w-4 h-4 text-green-500 mt-0.5 shrink-0"></i>
                                        <span>Tampil di urutan teratas</span>
                                    </li>
                                @else
                                    <li class="flex items-start gap-3 text-sm text-slate-600">
                                        <i data-lucide="check" class="w-4 h-4 text-green-500 mt-0.5 shrink-0"></i>
                                        <span>Disundul sebanyak <strong class="text-slate-900">{{ $package->amount }}x</strong></span>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    </div>
                    <a href="{{ route('listing_promotions.checkout', ['listing' => $listing->id, 'package' => $package->id]) }}" class="block w-full text-center py-3 px-4 rounded-2xl font-bold transition-all {{ $package->type == 'premium' ? 'bg-yellow-400 text-yellow-900 hover:bg-yellow-500' : 'bg-indigo-600 text-white hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-200' }}">
                        {{ $package->button_text ?? 'Pilih Paket' }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
