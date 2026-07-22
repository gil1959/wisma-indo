@extends('layouts.front')

@section('content')

<div class="bg-slate-50 min-h-screen py-10 pt-24">
    <div class="max-w-7xl mx-auto px-4">
        
        {{-- Breadcrumb & Title --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                <a href="{{ route('home') }}" class="hover:text-[#0194F3]">WismaIndo</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span class="text-slate-800 font-semibold capitalize">Iklan Pengguna</span>
            </div>
            
            <div class="flex items-center gap-4 mt-4">
                <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-slate-200 shadow-sm shrink-0 bg-white">
                    @if($user->avatar)
                        <img src="{{ asset($user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-400"><i data-lucide="user" class="w-8 h-8"></i></div>
                    @endif
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800">
                        Iklan oleh {{ $user->name }}
                    </h1>
                    <p class="text-slate-500 text-sm mt-1">Bergabung sejak {{ optional($user->created_at)->format('M Y') }}</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-8 mt-8">
            {{-- GRID HASIL LISTING --}}
            <div class="w-full">
                
                {{-- Toolbar --}}
                <div class="flex items-center justify-between mb-6">
                    <p class="text-slate-600 font-medium">Menampilkan <span class="font-bold text-slate-800">{{ $listings->total() }}</span> iklan</p>
                </div>

                @if($listings->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($listings as $item)
                    <a href="{{ route('listing.show', $item->slug) }}" class="group bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-xl hover:border-[#0194F3]/30 transition-all duration-300 flex flex-col h-full">
                        <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                            @if($item->cover_image)
                                <img src="{{ asset($item->cover_image) }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                    <i data-lucide="image" class="w-12 h-12"></i>
                                </div>
                            @endif
                            <div class="absolute top-3 left-3 z-10 flex flex-col gap-1.5">
                                <span class="px-2.5 py-1 bg-white/90 backdrop-blur-sm text-slate-700 text-xs font-bold rounded-lg shadow-sm capitalize">{{ $item->listingCategory->name ?? '' }}</span>
                                @if($item->transaction_type)
                                <span class="px-2.5 py-1 bg-[#0194F3] text-white text-xs font-bold rounded-lg shadow-sm capitalize">{{ $item->transaction_type }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="p-4 flex flex-col flex-1">
                            <h3 class="font-bold text-slate-800 text-sm mb-1.5 line-clamp-2 group-hover:text-[#0194F3] transition">{{ $item->title }}</h3>
                            <div class="flex justify-between items-center mb-3">
                                <div class="text-slate-400 text-xs flex items-center gap-1">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 shrink-0"></i>
                                    <span class="line-clamp-1">{{ $item->location ?? $item->address ?? 'Lokasi tidak diketahui' }}</span>
                                </div>
                                <div class="text-slate-400 text-xs flex items-center gap-1">
                                    <i data-lucide="eye" class="w-3.5 h-3.5 shrink-0"></i>
                                    <span>{{ $item->views ?? 0 }}</span>
                                </div>
                            </div>

                            {{-- Detail berdasarkan tipe --}}
                            @if($item->type == 'property')
                            <div class="flex items-center gap-3 text-slate-500 text-xs mb-3 flex-wrap">
                                @if($item->bedrooms)
                                <span class="flex items-center gap-1"><i data-lucide="bed" class="w-3.5 h-3.5"></i> {{ $item->bedrooms }} KT</span>
                                @endif
                                @if($item->bathrooms)
                                <span class="flex items-center gap-1"><i data-lucide="bath" class="w-3.5 h-3.5"></i> {{ $item->bathrooms }} KM</span>
                                @endif
                                @if($item->building_area)
                                <span class="flex items-center gap-1"><i data-lucide="maximize-2" class="w-3.5 h-3.5"></i> {{ $item->building_area }} m²</span>
                                @endif
                                @if($item->land_area && !$item->building_area)
                                <span class="flex items-center gap-1"><i data-lucide="map" class="w-3.5 h-3.5"></i> {{ $item->land_area }} m²</span>
                                @endif
                            </div>
                            @elseif($item->type == 'goods')
                            <div class="flex items-center gap-2 flex-wrap mb-3">
                                @if($item->condition)
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-xs font-semibold rounded-md">{{ $item->condition }}</span>
                                @endif
                                @if($item->brand)
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-xs font-semibold rounded-md">{{ $item->brand }}</span>
                                @endif
                            </div>
                            @elseif($item->type == 'service')
                            <div class="flex items-center gap-1 text-slate-400 text-xs mb-3">
                                <i data-lucide="navigation" class="w-3.5 h-3.5 shrink-0"></i>
                                <span class="line-clamp-1">{{ $item->service_area ?? 'Area layanan belum diisi' }}</span>
                            </div>
                            @endif

                            <div class="text-base font-bold text-[#0194F3] mt-auto">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                        </div>
                    </a>
                    @endforeach
                </div>
                
                <div class="mt-8">
                    {{ $listings->links() }}
                </div>
                @else
                {{-- Empty State --}}
                <div class="bg-white rounded-3xl p-12 border border-slate-200 text-center flex flex-col items-center justify-center min-h-[400px]">
                    <div class="w-24 h-24 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-6">
                        <i data-lucide="folder-x" class="w-12 h-12"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-2">Belum ada iklan</h3>
                    <p class="text-slate-500 mb-6 max-w-md">Pengguna ini belum memposting iklan apapun yang tersedia saat ini.</p>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>

@endsection
