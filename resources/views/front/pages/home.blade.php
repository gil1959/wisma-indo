@extends('layouts.front')

@section('content')

{{-- HERO SECTION --}}
<section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden bg-slate-900">
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/hero-property.jpg') }}" alt="Background" class="w-full h-full object-cover opacity-40">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl lg:text-7xl font-extrabold text-white tracking-tight mb-6 leading-tight">
            Temukan Properti Impian<br>Anda Hari Ini.
        </h1>
        <p class="text-lg md:text-xl text-slate-300 mb-10 max-w-2xl mx-auto font-light">
            Pilihan terbaik untuk rumah, apartemen, ruko, dan barang & jasa pendukung properti. Mulai perjalanan Anda bersama kami.
        </p>

        {{-- CTA GRID (3 Pairs) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-w-4xl mx-auto">
            
            <a href="{{ route('properti') }}" class="flex items-start gap-4 p-5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20 transition group text-left">
                <div class="p-3 bg-[#0194F3] rounded-xl text-white shrink-0 group-hover:scale-110 transition-transform">
                    <i data-lucide="search" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-1">Cari Properti</h3>
                    <p class="text-sm text-slate-300">Beli & Sewa</p>
                </div>
            </a>

            <a href="{{ route('pasang.iklan') }}" class="flex items-start gap-4 p-5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20 transition group text-left">
                <div class="p-3 bg-orange-500 rounded-xl text-white shrink-0 group-hover:scale-110 transition-transform">
                    <i data-lucide="megaphone" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-1">Pasang Iklan</h3>
                    <p class="text-sm text-slate-300">Jual & Sewa</p>
                </div>
            </a>

            <a href="{{ route('simulasi') }}" class="flex items-start gap-4 p-5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20 transition group text-left">
                <div class="p-3 bg-emerald-500 rounded-xl text-white shrink-0 group-hover:scale-110 transition-transform">
                    <i data-lucide="calculator" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-1">Simulasi KPR</h3>
                    <p class="text-sm text-slate-300">Nilai Properti</p>
                </div>
            </a>

            <a href="{{ route('barangjasa') }}" class="flex items-start gap-4 p-5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20 transition group text-left">
                <div class="p-3 bg-purple-500 rounded-xl text-white shrink-0 group-hover:scale-110 transition-transform">
                    <i data-lucide="package" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-1">Kebutuhan Barang</h3>
                    <p class="text-sm text-slate-300">& Jasa Properti</p>
                </div>
            </a>

            <a href="{{ route('quran') }}" class="flex items-start gap-4 p-5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20 transition group text-left">
                <div class="p-3 bg-teal-500 rounded-xl text-white shrink-0 group-hover:scale-110 transition-transform">
                    <i data-lucide="book-open" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-1">Baca Al Quran</h3>
                    <p class="text-sm text-slate-300">Surah & Terjemahan</p>
                </div>
            </a>

            <a href="{{ route('cobroke') }}" class="flex items-start gap-4 p-5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20 transition group text-left">
                <div class="p-3 bg-rose-500 rounded-xl text-white shrink-0 group-hover:scale-110 transition-transform">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-1">Co-Broke System</h3>
                    <p class="text-sm text-slate-300">Kerjasama Agen</p>
                </div>
            </a>

        </div>
    </div>
</section>

{{-- KATEGORI PROPERTI TERPOPULER --}}
<section class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-slate-800 mb-4">Tipe Properti Terpopuler</h2>
            <p class="text-slate-600 max-w-2xl mx-auto">Cari properti impian Anda mulai dari rumah minimalis, apartemen modern, ruko strategis, hingga tanah kavling siap bangun.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <a href="{{ route('properti') }}?tipe=rumah" class="bg-white rounded-3xl p-6 text-center shadow-sm hover:shadow-xl transition-all group border border-slate-100 hover:border-[#0194F3]/30">
                <div class="w-16 h-16 mx-auto bg-blue-50 text-[#0194F3] rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i data-lucide="home" class="w-8 h-8"></i>
                </div>
                <h3 class="font-bold text-slate-800">Rumah</h3>
            </a>
            <a href="{{ route('properti') }}?tipe=apartemen" class="bg-white rounded-3xl p-6 text-center shadow-sm hover:shadow-xl transition-all group border border-slate-100 hover:border-[#0194F3]/30">
                <div class="w-16 h-16 mx-auto bg-blue-50 text-[#0194F3] rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i data-lucide="building" class="w-8 h-8"></i>
                </div>
                <h3 class="font-bold text-slate-800">Apartemen</h3>
            </a>
            <a href="{{ route('properti') }}?tipe=ruko" class="bg-white rounded-3xl p-6 text-center shadow-sm hover:shadow-xl transition-all group border border-slate-100 hover:border-[#0194F3]/30">
                <div class="w-16 h-16 mx-auto bg-blue-50 text-[#0194F3] rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i data-lucide="store" class="w-8 h-8"></i>
                </div>
                <h3 class="font-bold text-slate-800">Ruko</h3>
            </a>
            <a href="{{ route('properti') }}?tipe=tanah" class="bg-white rounded-3xl p-6 text-center shadow-sm hover:shadow-xl transition-all group border border-slate-100 hover:border-[#0194F3]/30">
                <div class="w-16 h-16 mx-auto bg-blue-50 text-[#0194F3] rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i data-lucide="map" class="w-8 h-8"></i>
                </div>
                <h3 class="font-bold text-slate-800">Tanah Kavling</h3>
            </a>
        </div>
    </div>
</section>

{{-- LOKASI UNGGULAN & STRATEGIS --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-3xl font-bold text-slate-800 mb-4">Lokasi Unggulan & Strategis</h2>
                <p class="text-slate-600 max-w-2xl">Daftar kawasan favorit dengan akses transportasi mudah, fasilitas publik lengkap, dan nilai investasi properti tinggi.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('properti') }}?lokasi=jakarta-selatan" class="group relative rounded-3xl overflow-hidden aspect-[4/3]">
                <img src="{{ asset('images/loc-jaksel.jpg') }}" alt="Jakarta Selatan" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
                <div class="absolute bottom-6 left-6 right-6">
                    <h3 class="text-2xl font-bold text-white mb-2">Jakarta Selatan</h3>
                    <p class="text-slate-300">Pusat Bisnis & Hiburan</p>
                </div>
            </a>
            <a href="{{ route('properti') }}?lokasi=bsd-city" class="group relative rounded-3xl overflow-hidden aspect-[4/3]">
                <img src="{{ asset('images/loc-bsd.jpg') }}" alt="BSD City" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
                <div class="absolute bottom-6 left-6 right-6">
                    <h3 class="text-2xl font-bold text-white mb-2">BSD City</h3>
                    <p class="text-slate-300">Kota Mandiri Modern</p>
                </div>
            </a>
            <a href="{{ route('properti') }}?lokasi=pik" class="group relative rounded-3xl overflow-hidden aspect-[4/3]">
                <img src="{{ asset('images/loc-pik.jpg') }}" alt="PIK" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
                <div class="absolute bottom-6 left-6 right-6">
                    <h3 class="text-2xl font-bold text-white mb-2">Pantai Indah Kapuk</h3>
                    <p class="text-slate-300">Kawasan Eksklusif Terpadu</p>
                </div>
            </a>
        </div>
    </div>
</section>

{{-- REKOMENDASI JUAL BELI & SEWA PROPERTI --}}
<section class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-800 mb-4">Rekomendasi Jual Beli & Sewa Properti</h2>
                <p class="text-slate-600 max-w-2xl">Temukan iklan rumah dijual, sewa apartemen murah, dan ruko komersial dari agen terverifikasi.</p>
            </div>
            <a href="{{ route('properti') }}" class="shrink-0 font-bold text-[#0194F3] hover:text-blue-700 flex items-center gap-1">
                Lihat Semua <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Properti Card Dummy --}}
            @for ($i = 1; $i <= 4; $i++)
            <div class="bg-white rounded-3xl overflow-hidden border border-slate-100 hover:shadow-xl transition-all group">
                <div class="relative aspect-[4/3] overflow-hidden">
                    <img src="{{ asset('images/property-' . $i . '.jpg') }}" alt="Properti" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-[#0194F3]">
                        Baru
                    </div>
                    <div class="absolute top-4 right-4 bg-emerald-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-sm">
                        Dijual
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-slate-800 text-lg mb-1 truncate">Rumah Mewah {{ $i }}</h3>
                    <div class="text-[#0194F3] font-bold text-xl mb-3">Rp {{ number_format(1500000000 + ($i * 100000000), 0, ',', '.') }}</div>
                    <div class="flex items-center gap-1 text-slate-500 text-sm mb-4">
                        <i data-lucide="map-pin" class="w-4 h-4 text-rose-500"></i> Jakarta Selatan
                    </div>
                    <div class="flex items-center gap-4 text-slate-600 text-sm pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-1" title="Kamar Tidur"><i data-lucide="bed" class="w-4 h-4 text-slate-400"></i> {{ $i + 2 }}</div>
                        <div class="flex items-center gap-1" title="Kamar Mandi"><i data-lucide="bath" class="w-4 h-4 text-slate-400"></i> {{ $i + 1 }}</div>
                        <div class="flex items-center gap-1" title="Luas Bangunan"><i data-lucide="maximize" class="w-4 h-4 text-slate-400"></i> {{ 100 + ($i * 20) }}m²</div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

{{-- KEBUTUHAN BARANG & JASA --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-slate-800 mb-4">Kebutuhan Barang & Jasa</h2>
            <p class="text-slate-600 max-w-2xl mx-auto">Pusat penyedia jasa renovasi rumah, perawatan properti, dan perlengkapan rumah tangga terpercaya terlengkap di sekitar Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Barang/Jasa Card Dummy --}}
            @for ($i = 1; $i <= 4; $i++)
            <div class="bg-white rounded-3xl overflow-hidden border border-slate-100 hover:shadow-xl transition-all group">
                <div class="relative aspect-[4/3] overflow-hidden">
                    <img src="{{ asset('images/service-' . $i . '.jpg') }}" alt="Service" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-orange-500">
                        {{ $i % 2 == 0 ? 'Jasa' : 'Barang' }}
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-slate-800 text-lg mb-1 truncate">{{ $i % 2 == 0 ? 'Jasa Renovasi Atap' : 'Sofa Minimalis Modern' }}</h3>
                    <div class="text-[#0194F3] font-bold text-xl mb-3">Rp {{ number_format(500000 + ($i * 100000), 0, ',', '.') }}</div>
                    <div class="flex items-center gap-1 text-slate-500 text-sm">
                        <i data-lucide="map-pin" class="w-4 h-4 text-rose-500"></i> BSD City
                    </div>
                </div>
            </div>
            @endfor
        </div>
        <div class="text-center mt-10">
            <a href="{{ route('barangjasa') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#0194F3]/10 text-[#0194F3] font-bold rounded-xl hover:bg-[#0194F3]/20 transition">
                Lihat Semua Kebutuhan <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</section>

@endsection
