@extends('layouts.front')

@section('content')

<div class="bg-slate-50 min-h-screen py-10 pt-24">
    <div class="max-w-7xl mx-auto px-4">
        
        {{-- Breadcrumb & Title --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                <a href="{{ route('home') }}" class="hover:text-[#0194F3]">Rumaindo</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span class="text-slate-800 font-semibold capitalize">{{ str_replace('-', ' ', $type) }}</span>
            </div>
            <h1 class="text-3xl font-bold text-slate-800 capitalize">
                {{ str_replace('-', ' ', $type) }} Terkini
            </h1>
        </div>

        {{-- Tab Kategori --}}
        @php
            $isBarangJasaPage = $type == 'barang-jasa';
            
            // Determine active states
            $isBarang = $isBarangJasaPage && request('kategori', 'barang') == 'barang';
            $isJasa = $isBarangJasaPage && request('kategori') == 'jasa';
            $isProperti = !$isBarangJasaPage;

            // Generate strict links
            $linkProperti = $isProperti ? url()->current() : route('properti');
            $linkBarang = route('barangjasa', ['kategori' => 'barang']);
            $linkJasa = route('barangjasa', ['kategori' => 'jasa']);
        @endphp

        <div class="flex items-center gap-2 mb-8 border-b border-slate-200">
            <a href="{{ $linkProperti }}" class="px-6 py-3 font-semibold text-sm border-b-2 {{ $isProperti ? 'border-[#0194F3] text-[#0194F3]' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Properti
            </a>
            <a href="{{ $linkBarang }}" class="px-6 py-3 font-semibold text-sm border-b-2 {{ $isBarang ? 'border-[#0194F3] text-[#0194F3]' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Barang
            </a>
            <a href="{{ $linkJasa }}" class="px-6 py-3 font-semibold text-sm border-b-2 {{ $isJasa ? 'border-[#0194F3] text-[#0194F3]' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Jasa
            </a>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            
            {{-- SIDEBAR FILTER --}}
            <div class="w-full lg:w-1/4 shrink-0">
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm sticky top-24">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-slate-800 flex items-center gap-2">
                            <i data-lucide="filter" class="w-5 h-5 text-[#0194F3]"></i> Filter
                        </h3>
                        <a href="{{ url()->current() }}" class="text-sm font-semibold text-rose-500 hover:text-rose-600">Reset</a>
                    </div>

                    <form action="{{ url()->current() }}" method="GET" class="space-y-5">
                        <input type="hidden" name="kategori" value="{{ request('kategori') }}">

                        {{-- Kata Kunci --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Kata Kunci</label>
                            <input type="text" name="q" placeholder="Cari judul iklan..." value="{{ request('q') }}" class="w-full border-slate-300 rounded-xl focus:ring-[#0194F3] focus:border-[#0194F3] bg-slate-50 text-sm">
                        </div>

                        {{-- Jenis Transaksi (Hanya untuk Properti) --}}
                        @if($isProperti)
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Transaksi</label>
                            <div class="grid grid-cols-2 gap-2 bg-slate-100 p-1 rounded-xl">
                                <label class="cursor-pointer text-center">
                                    <input type="radio" name="transaksi" value="dijual" class="peer sr-only" {{ request('transaksi', $type == 'dijual' ? 'dijual' : '') == 'dijual' ? 'checked' : '' }}>
                                    <div class="py-2 text-sm font-semibold rounded-lg text-slate-500 peer-checked:bg-white peer-checked:text-[#0194F3] peer-checked:shadow-sm transition">Jual</div>
                                </label>
                                <label class="cursor-pointer text-center">
                                    <input type="radio" name="transaksi" value="disewa" class="peer sr-only" {{ request('transaksi', $type == 'disewakan' ? 'disewa' : '') == 'disewa' ? 'checked' : '' }}>
                                    <div class="py-2 text-sm font-semibold rounded-lg text-slate-500 peer-checked:bg-white peer-checked:text-[#0194F3] peer-checked:shadow-sm transition">Sewa</div>
                                </label>
                            </div>
                        </div>

                        {{-- Tipe Properti --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Tipe Properti</label>
                            <select name="tipe" class="w-full border-slate-300 rounded-xl focus:ring-[#0194F3] focus:border-[#0194F3] bg-slate-50 text-sm">
                                <option value="">Semua Tipe</option>
                                <option value="rumah" {{ request('tipe') == 'rumah' ? 'selected' : '' }}>Rumah</option>
                                <option value="apartemen" {{ request('tipe') == 'apartemen' ? 'selected' : '' }}>Apartemen</option>
                                <option value="ruko" {{ request('tipe') == 'ruko' ? 'selected' : '' }}>Ruko</option>
                                <option value="tanah" {{ request('tipe') == 'tanah' ? 'selected' : '' }}>Tanah</option>
                            </select>
                        </div>
                        @endif

                        {{-- Harga --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Rentang Harga (Rp)</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}" class="w-full border-slate-300 rounded-xl focus:ring-[#0194F3] focus:border-[#0194F3] bg-slate-50 text-sm">
                                <span class="text-slate-400">-</span>
                                <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}" class="w-full border-slate-300 rounded-xl focus:ring-[#0194F3] focus:border-[#0194F3] bg-slate-50 text-sm">
                            </div>
                        </div>

                        {{-- Lokasi --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Lokasi / Daerah</label>
                            <input type="text" id="searchLokasi" name="lokasi" placeholder="Mencari lokasi saat ini..." value="{{ request('lokasi') }}" class="w-full border-slate-300 rounded-xl focus:ring-[#0194F3] focus:border-[#0194F3] bg-slate-50 text-sm">
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="w-full py-3 bg-[#0194F3] hover:bg-blue-600 text-white font-bold rounded-xl shadow-md transition flex items-center justify-center gap-2">
                            Terapkan Filter
                        </button>
                    </form>
                </div>
            </div>

            {{-- GRID HASIL LISTING --}}
            <div class="w-full lg:w-3/4">
                
                {{-- Toolbar --}}
                <div class="flex items-center justify-between mb-6">
                    <p class="text-slate-600 font-medium">Menampilkan <span class="font-bold text-slate-800">{{ $listings->total() }}</span> properti</p>
                    
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-slate-500 font-medium hidden sm:block">Urutkan:</span>
                        <select class="border-slate-300 rounded-xl focus:ring-[#0194F3] focus:border-[#0194F3] bg-white text-sm font-semibold">
                            <option value="terbaru">Terbaru</option>
                            <option value="harga_murah">Harga Terendah</option>
                            <option value="harga_mahal">Harga Tertinggi</option>
                        </select>
                    </div>
                </div>

                @if($listings->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
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
                            @if($item->is_premium)
                            <div class="absolute top-3 right-3 bg-yellow-400 text-yellow-900 px-2 py-1 rounded-md text-xs font-bold shadow-sm flex items-center gap-1 z-10">
                                <i class="fas fa-star text-xs"></i> Premium
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
                            <h3 class="font-bold text-slate-800 text-base mb-1.5 line-clamp-2 group-hover:text-[#0194F3] transition">{{ $item->title }}</h3>
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

                            <div class="text-lg font-bold text-[#0194F3] mt-auto">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
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
                    <h3 class="text-2xl font-bold text-slate-800 mb-2">Data tidak ditemukan</h3>
                    <p class="text-slate-500 mb-6 max-w-md">Coba ubah atau hapus beberapa filter pencarian Anda untuk melihat lebih banyak hasil.</p>
                    <a href="{{ url()->current() }}" class="px-6 py-2 border-2 border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition">
                        Reset Filter
                    </a>
                </div>
                @endif

            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
@if(isset($siteSettings['google_maps_api_key']) && $siteSettings['google_maps_api_key'] != '')
<script src="https://maps.googleapis.com/maps/api/js?key={{ $siteSettings['google_maps_api_key'] }}&libraries=places"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var input = document.getElementById('searchLokasi');
        if (input) {
            new google.maps.places.Autocomplete(input);
            
            // Otomatis deteksi jika kosong
            if (!input.value) {
                const fallbackToIP = function() {
                    fetch('https://get.geojs.io/v1/ip/geo.json')
                        .then(response => response.json())
                        .then(data => {
                            if(data.city) {
                                input.value = data.city + (data.region ? ', ' + data.region : '');
                            } else {
                                input.placeholder = "Ketik nama lokasi...";
                            }
                        }).catch(err => {
                            input.placeholder = "Ketik nama lokasi...";
                        });
                };

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var pos = { lat: position.coords.latitude, lng: position.coords.longitude };
                        var geocoder = new google.maps.Geocoder();
                        geocoder.geocode({ location: pos }, function(results, status) {
                            if (status === 'OK' && results[0]) {
                                // Ambil kota atau address
                                let city = results[0].address_components.find(c => c.types.includes('administrative_area_level_2'));
                                let val = city ? city.long_name : results[0].formatted_address;
                                input.value = val;
                            } else {
                                fallbackToIP();
                            }
                        });
                    }, function(error) {
                        console.warn("Geolokasi browser gagal (kode " + error.code + "): " + error.message);
                        fallbackToIP();
                    }, { enableHighAccuracy: true });
                } else {
                    fallbackToIP();
                }
            }
        }
    });
</script>
@endif
@endpush
