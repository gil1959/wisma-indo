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

    $homeRekomenTitle = \App\Models\Setting::where('key', 'home_rekomendasi_title')->value('value') ?? 'Rekomendasi Jual Beli & Sewa Properti';
    $homeRekomenDesc = \App\Models\Setting::where('key', 'home_rekomendasi_desc')->value('value') ?? 'Temukan iklan rumah dijual, sewa apartemen murah, dan ruko komersial dari agen terverifikasi.';

    $homeTestimoniTitle = \App\Models\Setting::where('key', 'home_testimoni_title')->value('value') ?? 'TESTIMONI PENGGUNA';
    
    $homePartnerTitle = \App\Models\Setting::where('key', 'home_partner_title')->value('value') ?? 'PARTNER PERBANKAN';
    $homePartnerDesc = \App\Models\Setting::where('key', 'home_partner_desc')->value('value') ?? 'Kredit properti makin mudah dengan partner bank terpercaya.';
@endphp

{{-- HERO SECTION --}}
<div class="max-w-7xl mx-auto px-2 lg:px-4 pt-4">
    <section class="relative overflow-hidden bg-slate-900 pt-48 pb-64 rounded-2xl lg:rounded-[20px] w-full">
    <div class="absolute inset-0 z-0">
        @if($heroBgImage)
        <img src="{{ asset($heroBgImage) }}" alt="Background" class="w-full h-full object-cover">
        @else
        <img src="{{ asset('images/hero-property.jpg') }}" alt="Background" class="w-full h-full object-cover">
        @endif

    </div>

    <div class="max-w-sm mx-auto px-4 relative z-10 text-center w-full">
        <h1 class="text-xl md:text-2xl lg:text-2xl font-extrabold text-white tracking-tight mb-4 leading-tight drop-shadow-[0_4px_4px_rgba(0,0,0,0.8)]">
            {{ $heroTitle }}
        </h1>
        <p class="text-xs md:text-sm text-white mb-6 max-w-sm mx-auto font-medium drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
            {{ $heroSubtitle }}
        </p>

        {{-- SEARCH BAR ASLI --}}
        <div class="max-w-sm mx-auto bg-white/10 p-1.5 backdrop-blur-md rounded-full border border-white/20 shadow-2xl">
            <form action="{{ route('properti') }}" method="GET" class="relative flex items-center bg-white rounded-full overflow-hidden p-1 shadow-inner">
                <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 absolute left-4"></i>
                <input type="text" id="homeSearchLokasi" name="q" placeholder="Cari lokasi..." class="w-full pl-10 pr-3 py-1.5 bg-transparent text-xs text-slate-700 outline-none focus:ring-0 border-none font-medium placeholder-slate-400" autocomplete="off">
                <button type="submit" class="bg-[#0194F3] hover:bg-blue-600 text-white text-xs font-bold py-1.5 px-5 rounded-full transition-colors shrink-0">
                    Cari
                </button>
            </form>
        </div>
    </div>
    </section>
</div>


{{-- CTA GRID & SIDE BANNERS SECTION --}}
<section class="py-12 relative z-20 ">
    <div class="absolute inset-0 bg-slate-50 -z-10"></div>
    <div class="max-w-7xl mx-auto px-4 -mt-24 relative z-10 ">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            {{-- Kiri: Features Panel + Custom Links (Col 8) --}}
            <div class="lg:col-span-8 flex flex-col gap-6">
                
                {{-- FEATURES PANEL --}}
                @php $defaultIcons = ['megaphone', 'file-text', 'shield-check', 'users']; @endphp
                <div class="bg-white rounded-[20px] p-5 shadow-xl border border-slate-100 flex flex-wrap sm:flex-nowrap justify-between gap-4">
                    @foreach(range(1, 4) as $i)
                        @php
                            $fTitle = \App\Models\Setting::where('key', "feature_{$i}_title")->value('value');
                            $fDesc = \App\Models\Setting::where('key', "feature_{$i}_desc")->value('value');
                            $fIcon = \App\Models\Setting::where('key', "feature_{$i}_icon")->value('value');
                        @endphp
                        @if($fTitle)
                        <div class="flex items-start gap-3 flex-1">
                            @if($fIcon)
                            <img src="{{ asset($fIcon) }}" alt="Icon" class="w-10 h-10 object-contain shrink-0">
                            @else
                            <div class="w-10 h-10 bg-blue-50 text-[#0194F3] rounded-xl flex items-center justify-center shrink-0">
                                <i data-lucide="{{ $defaultIcons[$i-1] }}" class="w-5 h-5"></i>
                            </div>
                            @endif
                            <div>
                                <h4 class="font-extrabold text-slate-800 text-sm mb-0.5 leading-tight">{{ $fTitle }}</h4>
                                <p class="text-[10px] text-slate-500 leading-tight">{{ $fDesc }}</p>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>

                {{-- CUSTOM LINKS GRID (4 COLS) --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-3">
                    @foreach($customLinks as $l)
                    <a href="{{ $l->url }}" class="flex items-center gap-2 lg:gap-3 p-2 lg:p-3 rounded-2xl bg-white shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group border border-slate-100">
                        <div class="w-10 h-10 bg-blue-50 rounded-[14px] flex items-center justify-center shrink-0 group-hover:bg-[#0194F3] group-hover:text-white text-[#0194F3] transition-colors overflow-hidden border border-blue-100">
                            @if(!empty($l->icon_image))
                                <img src="{{ asset($l->icon_image) }}" alt="icon" class="w-6 h-6 object-contain">
                            @else
                                <i data-lucide="layout-grid" class="w-4 h-4"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-xs font-extrabold text-slate-800 mb-0.5 group-hover:text-[#0194F3] transition-colors leading-tight truncate">{{ $l->label }}</h3>
                            <p class="text-[10px] text-slate-500 font-medium leading-tight truncate">{{ $l->subtitle ?? '' }}</p>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:text-[#0194F3] shrink-0"></i>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Kanan: CTA Banner (Col 4) --}}
            <div class="lg:col-span-4 flex items-stretch">
                <div class="w-full rounded-3xl overflow-hidden shadow-lg relative bg-gradient-to-br from-[#0194F3] to-blue-700 p-6 flex flex-col justify-center text-white h-full min-h-[160px]">
                    <h3 class="text-xl font-extrabold mb-2">{{ $ctaTitle }}</h3>
                    <p class="text-blue-100 text-sm mb-4 font-medium">{{ $ctaSubtitle }}</p>
                    <a href="{{ $ctaBtnLink }}" class="inline-flex items-center gap-2 bg-white text-[#0194F3] text-sm font-bold py-2.5 px-5 rounded-full hover:bg-slate-50 transition self-start mt-auto">
                        {{ $ctaBtnText }} <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Banners Section --}}
        @if(count($banners) > 0)
        <div class="mt-8 mb-8 relative group lg:max-w-[1128px] mx-auto" x-data="{
            scrollNext() { 
                const card = this.$refs.container.children[0];
                if(card) {
                    const gap = parseInt(window.getComputedStyle(this.$refs.container).gap) || 0;
                    this.$refs.container.scrollBy({ left: card.clientWidth + gap, behavior: 'smooth' }); 
                }
            },
            scrollPrev() { 
                const card = this.$refs.container.children[0];
                if(card) {
                    const gap = parseInt(window.getComputedStyle(this.$refs.container).gap) || 0;
                    this.$refs.container.scrollBy({ left: -(card.clientWidth + gap), behavior: 'smooth' }); 
                }
            }
        }">
            <!-- Arrows Top Right -->
            <div class="flex justify-end gap-3 mb-4">
                <button @click="scrollPrev()" class="w-8 h-8 md:w-10 md:h-10 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-800 hover:text-[#0194F3] hover:border-[#0194F3] transition shadow-sm">
                    <i data-lucide="chevron-left" class="w-4 h-4 md:w-5 md:h-5"></i>
                </button>
                <button @click="scrollNext()" class="w-8 h-8 md:w-10 md:h-10 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-800 hover:text-[#0194F3] hover:border-[#0194F3] transition shadow-sm">
                    <i data-lucide="chevron-right" class="w-4 h-4 md:w-5 md:h-5"></i>
                </button>
            </div>

            <div x-ref="container" class="flex gap-4 lg:gap-6 overflow-x-hidden snap-x snap-mandatory scroll-smooth pb-4 w-full" style="scrollbar-width: none; -ms-overflow-style: none;">
                @foreach($banners as $banner)
                <div class="snap-start shrink-0 rounded-2xl overflow-hidden shadow-sm relative lg:w-[360px] lg:h-[150px] w-[85%] aspect-[21/9] lg:aspect-auto">
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
<section class="py-2 md:py-3 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-1.5 h-6 bg-[#0194F3] rounded-full shrink-0"></div>
            <h2 class="text-xl md:text-2xl font-black text-slate-900 uppercase">{{ $homeTipeTitle }}</h2>
        </div>

        @if(isset($categories['property']) && count($categories['property']) > 0)
        <div class="relative group" x-data="{
            scrollNext() { this.$refs.container.scrollBy({ left: 300, behavior: 'smooth' }); },
            scrollPrev() { this.$refs.container.scrollBy({ left: -300, behavior: 'smooth' }); }
        }">
            <!-- Arrows -->
            <button @click="scrollPrev()" class="absolute left-0 top-1/2 -translate-y-1/2 -ml-5 z-10 w-10 h-10 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-600 hover:text-[#0194F3] shadow-lg transition opacity-0 group-hover:opacity-100 hidden md:flex">
                <i data-lucide="chevron-left" class="w-5 h-5"></i>
            </button>
            <button @click="scrollNext()" class="absolute right-0 top-1/2 -translate-y-1/2 -mr-5 z-10 w-10 h-10 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-600 hover:text-[#0194F3] shadow-lg transition opacity-0 group-hover:opacity-100 hidden md:flex">
                <i data-lucide="chevron-right" class="w-5 h-5"></i>
            </button>

            <div x-ref="container" class="flex gap-3 md:gap-4 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-2 pt-2 -mx-4 px-4 md:mx-0 md:px-2" style="scrollbar-width: none; -ms-overflow-style: none;">
                @foreach($categories['property'] as $cat)
                <a href="{{ route('properti') }}?kategori={{ $cat->slug }}" class="snap-start shrink-0 w-[110px] md:w-[130px] group block">
                    <div class="aspect-[4/3] rounded-xl overflow-hidden bg-slate-100 mb-2 shadow-sm group-hover:shadow-md transition-all">
                        @if($cat->photo)
                            <img src="{{ asset($cat->photo) }}" alt="{{ $cat->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-[#0194F3]/30 bg-blue-50/50">
                                <i data-lucide="home" class="w-8 h-8 group-hover:scale-110 transition-transform duration-500"></i>
                            </div>
                        @endif
                    </div>
                    <h3 class="font-extrabold text-slate-800 text-center text-xs md:text-sm truncate px-1">{{ $cat->name }}</h3>
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
<section class="py-2 md:py-3 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-1.5 h-6 bg-orange-500 rounded-full shrink-0"></div>
            <h2 class="text-xl md:text-2xl font-black text-slate-900 uppercase">{{ $homeKategoriBarangTitle }}</h2>
        </div>

        @if(isset($categories['goods']) && count($categories['goods']) > 0)
        <div class="relative group" x-data="{
            scrollNext() { this.$refs.container.scrollBy({ left: 300, behavior: 'smooth' }); },
            scrollPrev() { this.$refs.container.scrollBy({ left: -300, behavior: 'smooth' }); }
        }">
            <!-- Arrows -->
            <button @click="scrollPrev()" class="absolute left-0 top-1/2 -translate-y-1/2 -ml-5 z-10 w-10 h-10 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-600 hover:text-orange-500 shadow-lg transition opacity-0 group-hover:opacity-100 hidden md:flex">
                <i data-lucide="chevron-left" class="w-5 h-5"></i>
            </button>
            <button @click="scrollNext()" class="absolute right-0 top-1/2 -translate-y-1/2 -mr-5 z-10 w-10 h-10 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-600 hover:text-orange-500 shadow-lg transition opacity-0 group-hover:opacity-100 hidden md:flex">
                <i data-lucide="chevron-right" class="w-5 h-5"></i>
            </button>

            <div x-ref="container" class="flex gap-3 md:gap-4 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-2 pt-2 -mx-4 px-4 md:mx-0 md:px-2" style="scrollbar-width: none; -ms-overflow-style: none;">
                @foreach($categories['goods'] as $cat)
                <a href="{{ route('barangjasa') }}?kategori={{ $cat->slug }}&tipe=goods" class="snap-start shrink-0 w-[110px] md:w-[130px] group block">
                    <div class="aspect-[4/3] rounded-xl overflow-hidden bg-white mb-2 shadow-sm group-hover:shadow-md transition-all">
                        @if($cat->photo)
                            <img src="{{ asset($cat->photo) }}" alt="{{ $cat->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-orange-500/30 bg-orange-50/50">
                                <i data-lucide="shopping-bag" class="w-8 h-8 group-hover:scale-110 transition-transform duration-500"></i>
                            </div>
                        @endif
                    </div>
                    <h3 class="font-extrabold text-slate-800 text-center text-xs md:text-sm truncate px-1">{{ $cat->name }}</h3>
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
<section class="py-2 md:py-3 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-1.5 h-6 bg-emerald-500 rounded-full shrink-0"></div>
            <h2 class="text-xl md:text-2xl font-black text-slate-900 uppercase">{{ $homeKategoriJasaTitle }}</h2>
        </div>

        @if(isset($categories['services']) && count($categories['services']) > 0)
        <div class="relative group" x-data="{
            scrollNext() { this.$refs.container.scrollBy({ left: 300, behavior: 'smooth' }); },
            scrollPrev() { this.$refs.container.scrollBy({ left: -300, behavior: 'smooth' }); }
        }">
            <!-- Arrows -->
            <button @click="scrollPrev()" class="absolute left-0 top-1/2 -translate-y-1/2 -ml-5 z-10 w-10 h-10 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-600 hover:text-emerald-500 shadow-lg transition opacity-0 group-hover:opacity-100 hidden md:flex">
                <i data-lucide="chevron-left" class="w-5 h-5"></i>
            </button>
            <button @click="scrollNext()" class="absolute right-0 top-1/2 -translate-y-1/2 -mr-5 z-10 w-10 h-10 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-600 hover:text-emerald-500 shadow-lg transition opacity-0 group-hover:opacity-100 hidden md:flex">
                <i data-lucide="chevron-right" class="w-5 h-5"></i>
            </button>

            <div x-ref="container" class="flex gap-3 md:gap-4 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-2 pt-2 -mx-4 px-4 md:mx-0 md:px-2" style="scrollbar-width: none; -ms-overflow-style: none;">
                @foreach($categories['services'] as $cat)
                <a href="{{ route('barangjasa') }}?kategori={{ $cat->slug }}&tipe=services" class="snap-start shrink-0 w-[110px] md:w-[130px] group block">
                    <div class="aspect-[4/3] rounded-xl overflow-hidden bg-slate-100 mb-2 shadow-sm group-hover:shadow-md transition-all">
                        @if($cat->photo)
                            <img src="{{ asset($cat->photo) }}" alt="{{ $cat->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-emerald-500/30 bg-emerald-50/50">
                                <i data-lucide="briefcase" class="w-8 h-8 group-hover:scale-110 transition-transform duration-500"></i>
                            </div>
                        @endif
                    </div>
                    <h3 class="font-extrabold text-slate-800 text-center text-xs md:text-sm truncate px-1">{{ $cat->name }}</h3>
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



{{-- REKOMENDASI JUAL BELI & SEWA PROPERTI --}}
<section class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-1.5 h-6 bg-[#0194F3] rounded-full shrink-0"></div>
                <h2 class="text-xl md:text-2xl font-black text-slate-900 uppercase">{{ $homeRekomenTitle }}</h2>
            </div>
            <a href="{{ route('properti') }}" class="shrink-0 font-bold text-[#0194F3] hover:text-blue-700 flex items-center gap-1 text-sm md:text-base">
                Lihat Semua <i data-lucide="arrow-right" class="w-4 h-4 md:w-5 md:h-5"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 md:gap-4">
            @if(isset($propertyListings) && $propertyListings->count() > 0)
                @foreach ($propertyListings as $listing)
                <a href="{{ route('listing.show', $listing->slug) }}" class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all group flex flex-col h-full border border-slate-100">
                    <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                        @if($listing->primary_image)
                        <img src="{{ asset($listing->primary_image) }}" alt="{{ $listing->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                            <i data-lucide="image" class="w-8 h-8"></i>
                        </div>
                        @endif
                        
                        <!-- Heart Icon top right -->
                        <div class="absolute top-2 right-2 w-7 h-7 rounded-full bg-black/20 backdrop-blur-sm flex items-center justify-center text-white/80 hover:bg-white hover:text-red-500 transition-colors">
                            <i data-lucide="heart" class="w-4 h-4"></i>
                        </div>
                        
                        @if($listing->is_premium)
                        <div class="absolute bottom-2 right-2 bg-[#0194F3] text-white px-2 py-0.5 rounded-full text-[10px] font-bold shadow-md flex items-center gap-1 z-10">
                            <i class="fas fa-crown text-[10px]"></i> Premium
                        </div>
                        @endif
                    </div>
                    <div class="p-3 flex flex-col flex-1">
                        <h3 class="font-bold text-slate-800 text-xs mb-1 line-clamp-2 group-hover:text-[#0194F3] transition">{{ $listing->title }}</h3>
                        <p class="text-[10px] text-slate-400 mb-2 truncate">by {{ $listing->user->name ?? 'Wisma Indo' }}</p>

                        <div class="text-sm font-bold text-[#0194F3] mb-3">Rp {{ number_format($listing->price, 0, ',', '.') }}</div>
                        
                        <div class="flex items-center gap-2 text-slate-400 text-[10px] mt-auto">
                            @if($listing->bedrooms)
                            <span class="flex items-center gap-1"><i data-lucide="bed" class="w-3 h-3"></i> {{ $listing->bedrooms }}</span>
                            @endif
                            @if($listing->bathrooms)
                            <span class="flex items-center gap-1"><i data-lucide="bath" class="w-3 h-3"></i> {{ $listing->bathrooms }}</span>
                            @endif
                            @if($listing->building_area)
                            <span class="flex items-center gap-1"><i data-lucide="maximize-2" class="w-3 h-3"></i> {{ $listing->building_area }}m²</span>
                            @elseif($listing->land_area)
                            <span class="flex items-center gap-1"><i data-lucide="map" class="w-3 h-3"></i> {{ $listing->land_area }}m²</span>
                            @endif
                        </div>
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

{{-- TESTIMONI PENGGUNA SECTION --}}
@if(isset($testimonials) && count($testimonials) > 0)
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 lg:pt-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl md:text-2xl font-extrabold text-slate-800 flex items-center gap-3">
            <span class="w-2 h-6 md:h-8 bg-[#0194F3] rounded-full inline-block"></span>
            {{ mb_strtoupper($homeTestimoniTitle) }}
        </h2>
    </div>
    
    <div class="relative group" x-data="{
        scrollNext() { this.$refs.container.scrollBy({ left: 300, behavior: 'smooth' }); },
        scrollPrev() { this.$refs.container.scrollBy({ left: -300, behavior: 'smooth' }); }
    }">
        <!-- Arrow Kiri -->
        <button @click="scrollPrev()" class="absolute left-0 top-1/2 -translate-y-1/2 -ml-5 z-10 w-10 h-10 bg-white shadow-lg rounded-full flex items-center justify-center text-slate-700 hover:text-[#0194F3] border border-slate-100 opacity-0 group-hover:opacity-100 transition-opacity hidden md:flex">
            <i data-lucide="chevron-left" class="w-5 h-5"></i>
        </button>
        
        <div x-ref="container" class="flex gap-4 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-4" style="scrollbar-width: none; -ms-overflow-style: none;">
            @foreach($testimonials as $t)
            <!-- Ubah lebar kartu agar pas 4 di layar desktop (karena max-w-7xl sekitar 1280px, 4 card = ~288px per card) -->
            <div class="snap-start shrink-0 w-[85vw] md:w-72 bg-white rounded-xl p-5 border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:border-[#0194F3]/20 hover:shadow-[0_8px_24px_-4px_rgba(1,148,243,0.15)] transition-all duration-300 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        @if($t->avatar)
                            <img src="{{ asset($t->avatar) }}" alt="{{ $t->name }}" class="w-10 h-10 rounded-full object-cover border-2 border-slate-100">
                        @else
                            <div class="w-10 h-10 rounded-full bg-slate-100 border-2 border-slate-200 flex items-center justify-center">
                                <i data-lucide="user" class="w-5 h-5 text-slate-400"></i>
                            </div>
                        @endif
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm leading-tight">{{ $t->name }}</h4>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $t->role }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-1 mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $t->rating ? 'text-amber-400' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        @endfor
                    </div>
                    
                    <p class="text-xs leading-relaxed text-slate-600 line-clamp-3">
                        {{ $t->content }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Arrow Kanan -->
        <button @click="scrollNext()" class="absolute right-0 top-1/2 -translate-y-1/2 -mr-5 z-10 w-10 h-10 bg-white shadow-lg rounded-full flex items-center justify-center text-slate-700 hover:text-[#0194F3] border border-slate-100 opacity-0 group-hover:opacity-100 transition-opacity hidden md:flex">
            <i data-lucide="chevron-right" class="w-5 h-5"></i>
        </button>
        
        <style>
            div[x-ref="container"]::-webkit-scrollbar { display: none; }
        </style>
    </div>
</section>
@endif

{{-- PARTNER PERBANKAN SECTION --}}
@if(isset($bankPartners) && count($bankPartners) > 0)
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 lg:pt-12 pb-16">
    <div class="mb-6">
        <h2 class="text-xl md:text-2xl font-extrabold text-slate-800 flex items-center gap-3">
            <span class="w-2 h-6 md:h-8 bg-[#0194F3] rounded-full inline-block"></span>
            {{ mb_strtoupper($homePartnerTitle) }}
        </h2>
        @if($homePartnerDesc)
        <p class="text-sm md:text-base text-slate-500 mt-2 font-medium ml-5 md:ml-11">{{ $homePartnerDesc }}</p>
        @endif
    </div>
    
    <div class="relative group" x-data="{
        scrollNext() { this.$refs.partnerContainer.scrollBy({ left: 200, behavior: 'smooth' }); },
        scrollPrev() { this.$refs.partnerContainer.scrollBy({ left: -200, behavior: 'smooth' }); }
    }">
        <!-- Arrow Kiri -->
        <button @click="scrollPrev()" class="absolute left-0 top-1/2 -translate-y-1/2 -ml-5 z-10 w-10 h-10 bg-white shadow-lg rounded-full flex items-center justify-center text-slate-700 hover:text-[#0194F3] border border-slate-100 opacity-0 group-hover:opacity-100 transition-opacity hidden md:flex">
            <i data-lucide="chevron-left" class="w-5 h-5"></i>
        </button>

        <div x-ref="partnerContainer" class="flex gap-4 md:gap-6 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-4 px-2" style="scrollbar-width: none; -ms-overflow-style: none;">
            @foreach($bankPartners as $b)
            <div class="snap-start shrink-0 bg-white rounded-2xl border border-slate-100 py-4 px-6 md:py-5 md:px-8 shadow-sm hover:shadow-md transition duration-300 w-36 md:w-44 flex items-center justify-center group/logo">
                <img src="{{ asset($b->logo) }}" alt="{{ $b->name }}" class="h-6 md:h-8 object-contain grayscale opacity-60 group-hover/logo:grayscale-0 group-hover/logo:opacity-100 transition-all duration-300">
            </div>
            @endforeach
        </div>

        <!-- Arrow Kanan -->
        <button @click="scrollNext()" class="absolute right-0 top-1/2 -translate-y-1/2 -mr-5 z-10 w-10 h-10 bg-white shadow-lg rounded-full flex items-center justify-center text-slate-700 hover:text-[#0194F3] border border-slate-100 opacity-0 group-hover:opacity-100 transition-opacity hidden md:flex">
            <i data-lucide="chevron-right" class="w-5 h-5"></i>
        </button>
        
        <style>
            div[x-ref="partnerContainer"]::-webkit-scrollbar { display: none; }
        </style>
    </div>
</section>
@endif



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
