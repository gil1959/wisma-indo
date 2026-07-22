@extends('layouts.front')

@section('content')

@php
    $heroBgImage = \App\Models\Setting::where('key', 'home_hero_bg_image')->value('value') ?? \App\Models\Setting::where('key', 'hero_image')->value('value');
    $heroTitle = \App\Models\Setting::where('key', 'hero_title')->value('value') ?? 'Temukan Properti Impian Anda Hari Ini.';
    $heroSubtitle = \App\Models\Setting::where('key', 'hero_subtitle')->value('value') ?? 'Pilihan terbaik untuk rumah, apartemen, ruko, dan barang & jasa pendukung properti.';

    $customLinks = $buttons;
    
    // Fallback if empty
    if($customLinks->isEmpty()) {
        $customLinks = collect([
            (object)['label' => 'Cari Properti', 'subtitle' => 'Beli & Sewa', 'url' => '/properti', 'icon_image' => ''],
            (object)['label' => 'Pasang Iklan', 'subtitle' => 'Jual & Sewa', 'url' => '/pasang-iklan', 'icon_image' => ''],
            (object)['label' => 'Simulasi & Nilai Properti', 'subtitle' => 'Kalkulator KPR', 'url' => '/simulasi', 'icon_image' => ''],
            (object)['label' => 'Kebutuhan Barang & Jasa', 'subtitle' => 'Pusat Perlengkapan', 'url' => '/barang-jasa', 'icon_image' => ''],
        ]);
    }

    $ctaTitle = \App\Models\Setting::where('key', 'cta_title')->value('value') ?? 'Pasang Iklan Sekarang!';
    $ctaSubtitle = \App\Models\Setting::where('key', 'cta_subtitle')->value('value') ?? 'Jangkau jutaan pencari properti dengan mudah.';
    $ctaBtnText = \App\Models\Setting::where('key', 'cta_button_text')->value('value') ?? 'Pasang Iklan';
    $ctaBtnLink = \App\Models\Setting::where('key', 'cta_button_link')->value('value') ?? '/pasang-iklan';

    $homeTipeTitle = \App\Models\Setting::where('key', 'home_tipe_title')->value('value') ?? 'Tipe Properti Terpopuler';
    $homeTipeDesc = \App\Models\Setting::where('key', 'home_tipe_desc')->value('value') ?? 'Cari properti impian Anda mulai dari rumah minimalis, apartemen modern, ruko strategis, hingga tanah kavling siap bangun.';

    $homeKategoriBarangTitle = \App\Models\Setting::where('key', 'home_kategori_barang_title')->value('value') ?? 'Kategori Barang Terpopuler';
    $homeKategoriBarangDesc = \App\Models\Setting::where('key', 'home_kategori_barang_desc')->value('value') ?? 'Temukan berbagai macam barang keperluan rumah tangga dan kantor.';

    $homeKategoriJasaTitle = \App\Models\Setting::where('key', 'home_kategori_jasa_title')->value('value') ?? 'Kategori Jasa Terpopuler';
    $homeKategoriJasaDesc = \App\Models\Setting::where('key', 'home_kategori_jasa_desc')->value('value') ?? 'Penyedia layanan dan jasa terbaik untuk semua kebutuhan Anda.';

    $homeLokasiTitle = \App\Models\Setting::where('key', 'home_lokasi_title')->value('value') ?? 'Lokasi Unggulan & Strategis';
    $homeLokasiDesc = \App\Models\Setting::where('key', 'home_lokasi_desc')->value('value') ?? 'Daftar kawasan favorit dengan akses transportasi mudah, fasilitas publik lengkap, dan nilai investasi properti tinggi.';

    $homeRekomenTitle = \App\Models\Setting::where('key', 'home_rekomendasi_title')->value('value') ?? 'Rekomendasi Jual Beli & Sewa Properti';
    $homeRekomenDesc = \App\Models\Setting::where('key', 'home_rekomendasi_desc')->value('value') ?? 'Temukan iklan rumah dijual, sewa apartemen murah, dan ruko komersial dari agen terverifikasi.';

    $homeKebutuhanTitle = \App\Models\Setting::where('key', 'home_kebutuhan_title')->value('value') ?? 'Kebutuhan Barang & Jasa';
    $homeKebutuhanDesc = \App\Models\Setting::where('key', 'home_kebutuhan_desc')->value('value') ?? 'Pusat penyedia jasa renovasi rumah, perawatan properti, dan perlengkapan rumah tangga terpercaya terlengkap di sekitar Anda.';
@endphp

{{-- HERO SECTION --}}
<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-900 pt-20 pb-32">
    <div class="absolute inset-0 z-0">
        @if($heroBgImage)
        <img src="{{ asset($heroBgImage) }}" alt="Background" class="w-full h-full object-cover">
        @else
        <img src="{{ asset('images/hero-property.jpg') }}" alt="Background" class="w-full h-full object-cover">
        @endif

    </div>

    <div class="max-w-4xl mx-auto px-4 relative z-10 text-center w-full">
        <h1 class="text-4xl md:text-5xl lg:text-7xl font-extrabold text-slate-900 tracking-tight mb-6 leading-tight drop-shadow-md">
            {{ $heroTitle }}
        </h1>
        <p class="text-lg md:text-xl text-slate-800 mb-12 max-w-2xl mx-auto font-medium drop-shadow-sm">
            {{ $heroSubtitle }}
        </p>

        {{-- SEARCH BAR ASLI --}}
        <div class="max-w-2xl mx-auto bg-white/10 p-2 backdrop-blur-md rounded-full border border-white/20 shadow-2xl">
            <form action="{{ route('properti') }}" method="GET" class="relative flex items-center bg-white rounded-full overflow-hidden p-1 shadow-inner">
                <i data-lucide="search" class="w-5 h-5 text-slate-400 absolute left-4"></i>
                <input type="text" id="homeSearchLokasi" name="q" placeholder="Sedang mencari lokasi Anda..." class="w-full pl-12 pr-4 py-3 bg-transparent text-slate-700 outline-none focus:ring-0 border-none font-medium placeholder-slate-400" autocomplete="off">
                <button type="submit" class="bg-[#0194F3] hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-full transition-colors shrink-0">
                    Cari
                </button>
            </form>
        </div>
    </div>
</section>

{{-- CTA GRID & SIDE BANNERS SECTION --}}
<section class="py-12 bg-slate-50 relative -mt-32 z-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Kiri: Custom Links (Col 7) --}}
            <div class="lg:col-span-7">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($customLinks as $l)
                    <a href="{{ $l->url }}" class="flex items-center gap-4 p-5 rounded-3xl bg-white shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group border border-slate-100">
                        <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-[#0194F3] group-hover:text-white text-[#0194F3] transition-colors overflow-hidden border border-blue-100">
                            @if(!empty($l->icon_image))
                                <img src="{{ asset($l->icon_image) }}" alt="icon" class="w-10 h-10 object-contain p-1">
                            @else
                                <i data-lucide="layout-grid" class="w-6 h-6"></i>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800 mb-0.5 group-hover:text-[#0194F3] transition-colors">{{ $l->label }}</h3>
                            <p class="text-sm text-slate-500 font-medium">{{ $l->subtitle ?? '' }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Kanan: CTA Banner (Col 5) --}}
            <div class="lg:col-span-5 flex items-stretch">
                <div class="w-full rounded-3xl overflow-hidden shadow-lg relative bg-gradient-to-br from-[#0194F3] to-blue-700 p-8 flex flex-col justify-center text-white h-full min-h-[160px]">
                    <h3 class="text-2xl font-extrabold mb-2">{{ $ctaTitle }}</h3>
                    <p class="text-blue-100 mb-6 font-medium">{{ $ctaSubtitle }}</p>
                    <a href="{{ $ctaBtnLink }}" class="inline-flex items-center gap-2 bg-white text-[#0194F3] font-bold py-3 px-6 rounded-full hover:bg-slate-50 transition self-start mt-auto">
                        {{ $ctaBtnText }} <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Banners Section --}}
        @if(count($banners) > 0)
        <div class="mt-12 mb-8 relative group" x-data="{
            scrollNext() { this.$refs.container.scrollBy({ left: 300, behavior: 'smooth' }); },
            scrollPrev() { this.$refs.container.scrollBy({ left: -300, behavior: 'smooth' }); }
        }">
            <!-- Arrows Top Right -->
            <div class="flex justify-end gap-3 mb-4">
                <button @click="scrollPrev()" class="w-10 h-10 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-800 hover:text-[#0194F3] hover:border-[#0194F3] transition shadow-sm">
                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                </button>
                <button @click="scrollNext()" class="w-10 h-10 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-800 hover:text-[#0194F3] hover:border-[#0194F3] transition shadow-sm">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </button>
            </div>

            <div x-ref="container" class="flex gap-4 md:gap-6 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-4 {{ count($banners) == 1 ? 'justify-center' : '' }}" style="scrollbar-width: none; -ms-overflow-style: none;">
                @foreach($banners as $banner)
                <div class="snap-center shrink-0 rounded-2xl md:rounded-3xl overflow-hidden shadow-sm relative" style="width: 85%; max-width: 400px; aspect-ratio: 21/9; flex: 0 0 auto;">
                    <a href="{{ $banner->url ?: '#' }}" class="block w-full h-full">
                        <img src="{{ asset($banner->image) }}" alt="Banner" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                    </a>
                </div>
                @endforeach
            </div>
            
            <style>
                /* Menyembunyikan scrollbar untuk Chrome, Safari dan Opera */
                div[x-ref="container"]::-webkit-scrollbar {
                    display: none;
                }
            </style>
        </div>
        @endif
    </div>
</section>

{{-- KATEGORI PROPERTI TERPOPULER --}}
<section class="py-16 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-start gap-4 mb-8">
            <div class="w-2 h-10 bg-[#0194F3] rounded-full shrink-0"></div>
            <div>
                <h2 class="text-3xl font-black text-slate-900 uppercase mb-2">{{ $homeTipeTitle }}</h2>
                <p class="text-slate-600 font-medium max-w-4xl">{{ $homeTipeDesc }}</p>
            </div>
        </div>

        @if(isset($categories['property']) && count($categories['property']) > 0)
        <div class="relative group" x-data="{
            scrollNext() { this.$refs.container.scrollBy({ left: 300, behavior: 'smooth' }); },
            scrollPrev() { this.$refs.container.scrollBy({ left: -300, behavior: 'smooth' }); }
        }">
            <!-- Arrows -->
            <button @click="scrollPrev()" class="absolute left-0 top-1/3 -translate-y-1/2 -ml-5 z-10 w-12 h-12 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-600 hover:text-[#0194F3] shadow-lg transition opacity-0 group-hover:opacity-100 hidden md:flex">
                <i data-lucide="chevron-left" class="w-6 h-6"></i>
            </button>
            <button @click="scrollNext()" class="absolute right-0 top-1/3 -translate-y-1/2 -mr-5 z-10 w-12 h-12 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-600 hover:text-[#0194F3] shadow-lg transition opacity-0 group-hover:opacity-100 hidden md:flex">
                <i data-lucide="chevron-right" class="w-6 h-6"></i>
            </button>

            <div x-ref="container" class="flex gap-6 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-8 pt-4 -mx-4 px-4 md:mx-0 md:px-2" style="scrollbar-width: none; -ms-overflow-style: none;">
                @foreach($categories['property'] as $cat)
                <a href="{{ route('properti') }}?kategori={{ $cat->slug }}" class="snap-center shrink-0 w-[160px] sm:w-[180px] group block">
                    <div class="aspect-square rounded-[2rem] overflow-hidden bg-slate-100 mb-4 shadow-sm group-hover:shadow-xl transition-all border-4 border-white ring-1 ring-slate-100 group-hover:ring-[#0194F3]/30">
                        @if($cat->photo)
                            <img src="{{ asset($cat->photo) }}" alt="{{ $cat->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-[#0194F3]/30 bg-blue-50/50">
                                <i data-lucide="home" class="w-12 h-12 group-hover:scale-110 transition-transform duration-500"></i>
                            </div>
                        @endif
                    </div>
                    <h3 class="font-extrabold text-slate-800 text-center text-lg">{{ $cat->name }}</h3>
                </a>
                @endforeach
            </div>
            
            <style>
                div[x-ref="container"]::-webkit-scrollbar { display: none; }
            </style>
        </div>
        @else
        <div class="p-8 text-center text-slate-500 bg-slate-50 rounded-3xl border border-dashed border-slate-300">Belum ada kategori properti.</div>
        @endif
    </div>
</section>

{{-- KATEGORI BARANG TERPOPULER --}}
<section class="py-16 bg-slate-50 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-start gap-4 mb-8">
            <div class="w-2 h-10 bg-orange-500 rounded-full shrink-0"></div>
            <div>
                <h2 class="text-3xl font-black text-slate-900 uppercase mb-2">{{ $homeKategoriBarangTitle }}</h2>
                <p class="text-slate-600 font-medium max-w-4xl">{{ $homeKategoriBarangDesc }}</p>
            </div>
        </div>

        @if(isset($categories['goods']) && count($categories['goods']) > 0)
        <div class="relative group" x-data="{
            scrollNext() { this.$refs.container.scrollBy({ left: 300, behavior: 'smooth' }); },
            scrollPrev() { this.$refs.container.scrollBy({ left: -300, behavior: 'smooth' }); }
        }">
            <!-- Arrows -->
            <button @click="scrollPrev()" class="absolute left-0 top-1/3 -translate-y-1/2 -ml-5 z-10 w-12 h-12 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-600 hover:text-orange-500 shadow-lg transition opacity-0 group-hover:opacity-100 hidden md:flex">
                <i data-lucide="chevron-left" class="w-6 h-6"></i>
            </button>
            <button @click="scrollNext()" class="absolute right-0 top-1/3 -translate-y-1/2 -mr-5 z-10 w-12 h-12 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-600 hover:text-orange-500 shadow-lg transition opacity-0 group-hover:opacity-100 hidden md:flex">
                <i data-lucide="chevron-right" class="w-6 h-6"></i>
            </button>

            <div x-ref="container" class="flex gap-6 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-8 pt-4 -mx-4 px-4 md:mx-0 md:px-2" style="scrollbar-width: none; -ms-overflow-style: none;">
                @foreach($categories['goods'] as $cat)
                <a href="{{ route('barangjasa') }}?kategori={{ $cat->slug }}&tipe=goods" class="snap-center shrink-0 w-[160px] sm:w-[180px] group block">
                    <div class="aspect-square rounded-[2rem] overflow-hidden bg-white mb-4 shadow-sm group-hover:shadow-xl transition-all border-4 border-white ring-1 ring-slate-100 group-hover:ring-orange-500/30">
                        @if($cat->photo)
                            <img src="{{ asset($cat->photo) }}" alt="{{ $cat->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-orange-500/30 bg-orange-50/50">
                                <i data-lucide="shopping-bag" class="w-12 h-12 group-hover:scale-110 transition-transform duration-500"></i>
                            </div>
                        @endif
                    </div>
                    <h3 class="font-extrabold text-slate-800 text-center text-lg">{{ $cat->name }}</h3>
                </a>
                @endforeach
            </div>
            
            <style>
                div[x-ref="container"]::-webkit-scrollbar { display: none; }
            </style>
        </div>
        @else
        <div class="p-8 text-center text-slate-500 bg-white rounded-3xl border border-dashed border-slate-300">Belum ada kategori barang.</div>
        @endif
    </div>
</section>

{{-- KATEGORI JASA TERPOPULER --}}
<section class="py-16 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-start gap-4 mb-8">
            <div class="w-2 h-10 bg-emerald-500 rounded-full shrink-0"></div>
            <div>
                <h2 class="text-3xl font-black text-slate-900 uppercase mb-2">{{ $homeKategoriJasaTitle }}</h2>
                <p class="text-slate-600 font-medium max-w-4xl">{{ $homeKategoriJasaDesc }}</p>
            </div>
        </div>

        @if(isset($categories['services']) && count($categories['services']) > 0)
        <div class="relative group" x-data="{
            scrollNext() { this.$refs.container.scrollBy({ left: 300, behavior: 'smooth' }); },
            scrollPrev() { this.$refs.container.scrollBy({ left: -300, behavior: 'smooth' }); }
        }">
            <!-- Arrows -->
            <button @click="scrollPrev()" class="absolute left-0 top-1/3 -translate-y-1/2 -ml-5 z-10 w-12 h-12 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-600 hover:text-emerald-500 shadow-lg transition opacity-0 group-hover:opacity-100 hidden md:flex">
                <i data-lucide="chevron-left" class="w-6 h-6"></i>
            </button>
            <button @click="scrollNext()" class="absolute right-0 top-1/3 -translate-y-1/2 -mr-5 z-10 w-12 h-12 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-600 hover:text-emerald-500 shadow-lg transition opacity-0 group-hover:opacity-100 hidden md:flex">
                <i data-lucide="chevron-right" class="w-6 h-6"></i>
            </button>

            <div x-ref="container" class="flex gap-6 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-8 pt-4 -mx-4 px-4 md:mx-0 md:px-2" style="scrollbar-width: none; -ms-overflow-style: none;">
                @foreach($categories['services'] as $cat)
                <a href="{{ route('barangjasa') }}?kategori={{ $cat->slug }}&tipe=services" class="snap-center shrink-0 w-[160px] sm:w-[180px] group block">
                    <div class="aspect-square rounded-[2rem] overflow-hidden bg-slate-100 mb-4 shadow-sm group-hover:shadow-xl transition-all border-4 border-white ring-1 ring-slate-100 group-hover:ring-emerald-500/30">
                        @if($cat->photo)
                            <img src="{{ asset($cat->photo) }}" alt="{{ $cat->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-emerald-500/30 bg-emerald-50/50">
                                <i data-lucide="briefcase" class="w-12 h-12 group-hover:scale-110 transition-transform duration-500"></i>
                            </div>
                        @endif
                    </div>
                    <h3 class="font-extrabold text-slate-800 text-center text-lg">{{ $cat->name }}</h3>
                </a>
                @endforeach
            </div>
            
            <style>
                div[x-ref="container"]::-webkit-scrollbar { display: none; }
            </style>
        </div>
        @else
        <div class="p-8 text-center text-slate-500 bg-slate-50 rounded-3xl border border-dashed border-slate-300">Belum ada kategori jasa.</div>
        @endif
    </div>
</section>

{{-- LOKASI UNGGULAN & STRATEGIS --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-3xl font-bold text-slate-800 mb-4">{{ $homeLokasiTitle }}</h2>
                <p class="text-slate-600 max-w-2xl">{{ $homeLokasiDesc }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @if(isset($locations) && count($locations) > 0)
                @foreach($locations as $loc)
                <a href="{{ $loc->url ?: route('properti') }}" class="group relative rounded-3xl overflow-hidden aspect-[4/3]">
                    <img src="{{ asset($loc->image) }}" alt="{{ $loc->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
                    <div class="absolute bottom-6 left-6 right-6">
                        <h3 class="text-2xl font-bold text-white mb-2">{{ $loc->title }}</h3>
                        <p class="text-slate-300">{{ $loc->subtitle }}</p>
                    </div>
                </a>
                @endforeach
            @else
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
            @endif
        </div>
    </div>
</section>

{{-- REKOMENDASI JUAL BELI & SEWA PROPERTI --}}
<section class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-800 mb-4">{{ $homeRekomenTitle }}</h2>
                <p class="text-slate-600 max-w-2xl">{{ $homeRekomenDesc }}</p>
            </div>
            <a href="{{ route('properti') }}" class="shrink-0 font-bold text-[#0194F3] hover:text-blue-700 flex items-center gap-1">
                Lihat Semua <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @if(isset($propertyListings) && $propertyListings->count() > 0)
                @foreach ($propertyListings as $listing)
                <a href="{{ route('listing.show', $listing->slug) }}" class="bg-white rounded-3xl overflow-hidden border border-slate-100 hover:shadow-xl transition-all group flex flex-col h-full">
                    <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                        @if($listing->primary_image)
                        <img src="{{ asset($listing->primary_image) }}" alt="{{ $listing->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                            <i data-lucide="image" class="w-12 h-12"></i>
                        </div>
                        @endif
                        @if($listing->is_premium)
                        <div class="absolute top-4 left-1/2 -translate-x-1/2 bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-xs font-bold shadow-md flex items-center gap-1 z-10">
                            <i class="fas fa-star text-xs"></i> Premium
                        </div>
                        @endif

                        @if($listing->condition)
                        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-[#0194F3]">
                            {{ $listing->condition }}
                        </div>
                        @endif
                        <div class="absolute top-4 right-4 bg-emerald-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-sm capitalize">
                            {{ $listing->transaction_type ?? 'Jual/Sewa' }}
                        </div>
                    </div>
                    <div class="p-4 flex flex-col flex-1">
                        <h3 class="font-bold text-slate-800 text-base mb-1.5 line-clamp-2 group-hover:text-[#0194F3] transition">{{ $listing->title }}</h3>
                        <div class="flex justify-between items-center mb-3">
                            <div class="text-slate-400 text-xs flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 shrink-0"></i>
                                <span class="line-clamp-1">{{ $listing->location ?? $listing->address ?? 'Lokasi tidak diketahui' }}</span>
                            </div>
                            <div class="text-slate-400 text-xs flex items-center gap-1">
                                <i data-lucide="eye" class="w-3.5 h-3.5 shrink-0"></i>
                                <span>{{ $listing->views ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-slate-500 text-xs mb-3 flex-wrap">
                            @if($listing->bedrooms)
                            <span class="flex items-center gap-1"><i data-lucide="bed" class="w-3.5 h-3.5"></i> {{ $listing->bedrooms }} KT</span>
                            @endif
                            @if($listing->bathrooms)
                            <span class="flex items-center gap-1"><i data-lucide="bath" class="w-3.5 h-3.5"></i> {{ $listing->bathrooms }} KM</span>
                            @endif
                            @if($listing->building_area)
                            <span class="flex items-center gap-1"><i data-lucide="maximize-2" class="w-3.5 h-3.5"></i> {{ $listing->building_area }} m²</span>
                            @endif
                            @if($listing->land_area && !$listing->building_area)
                            <span class="flex items-center gap-1"><i data-lucide="map" class="w-3.5 h-3.5"></i> {{ $listing->land_area }} m²</span>
                            @endif
                        </div>

                        <div class="text-lg font-bold text-[#0194F3] mt-auto">Rp {{ number_format($listing->price, 0, ',', '.') }}</div>
                    </div>
                </a>
                @endforeach
            @else
                <div class="col-span-full py-12 text-center text-slate-500 bg-white rounded-3xl border border-dashed border-slate-300">
                    Belum ada properti yang tersedia saat ini.
                </div>
            @endif
        </div>
    </div>
</section>

{{-- KEBUTUHAN BARANG & JASA --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-slate-800 mb-4">{{ $homeKebutuhanTitle }}</h2>
            <p class="text-slate-600 max-w-2xl mx-auto">{{ $homeKebutuhanDesc }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $combinedGS = collect();
                if(isset($goodsListings)) $combinedGS = $combinedGS->merge($goodsListings);
                if(isset($servicesListings)) $combinedGS = $combinedGS->merge($servicesListings);
                $combinedGS = $combinedGS->sortBy([
                    ['is_premium', 'desc'],
                    ['bump_count', 'desc'],
                    ['bumped_at', 'desc'],
                    ['created_at', 'desc']
                ])->take(8);
            @endphp
            
            @if($combinedGS->count() > 0)
                @foreach ($combinedGS as $listing)
                <a href="{{ route('listing.show', $listing->slug) }}" class="bg-white rounded-3xl overflow-hidden border border-slate-100 hover:shadow-xl transition-all group flex flex-col h-full">
                    <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                        @if($listing->primary_image)
                        <img src="{{ asset($listing->primary_image) }}" alt="{{ $listing->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                            <i data-lucide="{{ $listing->type == 'goods' ? 'package' : 'briefcase' }}" class="w-12 h-12"></i>
                        </div>
                        @endif
                        @if($listing->is_premium)
                        <div class="absolute top-4 left-1/2 -translate-x-1/2 bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-xs font-bold shadow-md flex items-center gap-1 z-10">
                            <i class="fas fa-star text-xs"></i> Premium
                        </div>
                        @endif
                        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold {{ $listing->type == 'goods' ? 'text-orange-500' : 'text-emerald-500' }}">
                            {{ $listing->type == 'goods' ? 'Barang' : 'Jasa' }}
                        </div>
                    </div>
                    <div class="p-4 flex flex-col flex-1">
                        <h3 class="font-bold text-slate-800 text-base mb-1.5 line-clamp-2 group-hover:text-[#0194F3] transition">{{ $listing->title }}</h3>
                        <div class="flex justify-between items-center mb-3">
                            <div class="text-slate-400 text-xs flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 shrink-0"></i>
                                <span class="line-clamp-1">{{ $listing->location ?? $listing->address ?? 'Lokasi tidak diketahui' }}</span>
                            </div>
                            <div class="text-slate-400 text-xs flex items-center gap-1">
                                <i data-lucide="eye" class="w-3.5 h-3.5 shrink-0"></i>
                                <span>{{ $listing->views ?? 0 }}</span>
                            </div>
                        </div>

                        @if($listing->type == 'goods')
                        <div class="flex items-center gap-2 flex-wrap mb-3">
                            @if($listing->condition)
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-xs font-semibold rounded-md">{{ $listing->condition }}</span>
                            @endif
                            @if($listing->brand)
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-xs font-semibold rounded-md">{{ $listing->brand }}</span>
                            @endif
                        </div>
                        @elseif($listing->type == 'services')
                        <div class="flex items-center gap-1 text-slate-400 text-xs mb-3">
                            <i data-lucide="navigation" class="w-3.5 h-3.5 shrink-0"></i>
                            <span class="line-clamp-1">{{ $listing->service_area ?? 'Area layanan belum diisi' }}</span>
                        </div>
                        @endif

                        <div class="text-lg font-bold text-[#0194F3] mt-auto">Rp {{ number_format($listing->price, 0, ',', '.') }}</div>
                    </div>
                </a>
                @endforeach
            @else
                <div class="col-span-full py-12 text-center text-slate-500 bg-white rounded-3xl border border-dashed border-slate-300">
                    Belum ada barang/jasa yang tersedia saat ini.
                </div>
            @endif
        </div>
        <div class="text-center mt-10">
            <a href="{{ route('barangjasa') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#0194F3]/10 text-[#0194F3] font-bold rounded-xl hover:bg-[#0194F3]/20 transition">
                Lihat Semua Kebutuhan <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
@if(isset($siteSettings['google_maps_api_key']) && $siteSettings['google_maps_api_key'] != '')
<script src="https://maps.googleapis.com/maps/api/js?key={{ $siteSettings['google_maps_api_key'] }}&libraries=places"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var input = document.getElementById('homeSearchLokasi');
        if (input) {
            new google.maps.places.Autocomplete(input);
            
            if (!input.value) {
                const fallbackToIP = function() {
                    fetch('https://get.geojs.io/v1/ip/geo.json')
                        .then(response => response.json())
                        .then(data => {
                            if(data.city) {
                                input.value = data.city + (data.region ? ', ' + data.region : '');
                            } else {
                                input.placeholder = "Cari lokasi, nama properti, atau area...";
                            }
                        }).catch(err => {
                            input.placeholder = "Cari lokasi, nama properti, atau area...";
                        });
                };

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var pos = { lat: position.coords.latitude, lng: position.coords.longitude };
                        var geocoder = new google.maps.Geocoder();
                        geocoder.geocode({ location: pos }, function(results, status) {
                            if (status === 'OK' && results[0]) {
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
