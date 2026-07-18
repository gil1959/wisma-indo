@extends('layouts.front')

@section('content')

@php
    /* ── kumpulkan semua foto ─────────────────────── */
    $allImages = collect();
    if ($listing->cover_image) {
        $allImages->push(asset($listing->cover_image));
    }
    foreach ($listing->images as $img) {
        $allImages->push(asset($img->image_path));
    }
    if ($allImages->isEmpty()) {
        $allImages->push(asset('images/placeholder.jpg'));
    }
    $totalImages   = $allImages->count();
    $allImagesJson = json_encode($allImages->values()->all());

    /* ── maps: coba berbagai format URL ─────────────── */
    $rawMaps   = $listing->maps_url ?? $listing->map_url ?? null;
    $iframeSrc = null;

    if ($listing->latitude && $listing->longitude) {
        $lat = $listing->latitude;
        $lng = $listing->longitude;
        $iframeSrc = 'https://maps.google.com/maps?q='.$lat.','.$lng.'&t=&z=15&ie=UTF8&iwloc=&output=embed';
        if (!$rawMaps) {
            $rawMaps = 'https://www.google.com/maps/search/?api=1&query='.$lat.','.$lng;
        }
    } elseif ($rawMaps) {
        if (preg_match('/src="([^"]+)"/', $rawMaps, $matches)) {
            $iframeSrc = $matches[1];
        } else {
            if (strpos($rawMaps, 'maps.app.goo.gl') !== false || strpos($rawMaps, 'goo.gl/maps') !== false) {
                try {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $rawMaps);
                    curl_setopt($ch, CURLOPT_HEADER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
                    curl_exec($ch);
                    $expanded = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                    curl_close($ch);
                    if ($expanded) $rawMaps = $expanded;
                } catch (\Exception $e) {}
            }

            if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $rawMaps, $matches)) {
                $lat = $matches[1];
                $lng = $matches[2];
                $iframeSrc = 'https://maps.google.com/maps?q='.$lat.','.$lng.'&t=&z=14&ie=UTF8&iwloc=&output=embed';
            } else {
                // Fallback: Use location/address as query for Google Maps embed
                $query = $listing->location ?? $listing->address ?? $listing->title;
                $iframeSrc = 'https://maps.google.com/maps?q='.urlencode($query).'&t=&z=14&ie=UTF8&iwloc=&output=embed';
            }
        }
    }
@endphp

<style>
/* GALLERY */
#gallery-cover { position:relative; aspect-ratio:16/9; background:#0f172a; overflow:hidden; cursor:pointer; }
.g-img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
#g-hint { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; pointer-events:none; opacity:0; transition:opacity .2s; }
#g-hint span { background:rgba(255,255,255,.82); border-radius:50%; width:54px; height:54px; display:flex; align-items:center; justify-content:center; }
#g-ctr { position:absolute; bottom:10px; right:12px; background:rgba(0,0,0,.6); color:#fff; font-size:12px; font-weight:700; padding:3px 10px; border-radius:20px; }

/* THUMBNAIL STRIP */
#tw { position:relative; border-top:1px solid #f1f5f9; }
#ts { display:flex; gap:8px; overflow-x:auto; scroll-behavior:smooth; padding:12px 44px; scrollbar-width:none; }
#ts::-webkit-scrollbar { display:none; }
.tb { flex:0 0 auto; width:88px; height:60px; border-radius:10px; overflow:hidden; border:2.5px solid transparent; transition:all .2s; cursor:pointer; background:none; padding:0; }
.tb.ton { border-color:#0194F3; transform:scale(1.06); box-shadow:0 0 0 3px rgba(1,148,243,.2); }
.tb img { width:100%; height:100%; object-fit:cover; display:block; }
.sn { position:absolute; top:50%; transform:translateY(-50%); width:32px; height:32px; border-radius:50%; background:#fff; border:1px solid #e2e8f0; box-shadow:0 1px 6px rgba(0,0,0,.12); display:flex; align-items:center; justify-content:center; cursor:pointer; z-index:5; }
.sn:hover { background:#f1f5f9; }

/* LIGHTBOX */
#lb { position:fixed; inset:0; background:rgba(0,0,0,.93); z-index:9999; display:none; align-items:center; justify-content:center; }
#lb.open { display:flex; }
#lb-img { max-width:calc(100vw - 140px); max-height:calc(100vh - 80px); object-fit:contain; border-radius:12px; display:block; }
.lbb { border:none; background:rgba(255,255,255,.15); color:#fff; border-radius:50%; width:48px; height:48px; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:background .2s; }
.lbb:hover { background:rgba(255,255,255,.28); }
#lb-close { position:absolute; top:14px; right:14px; }
#lb-ctr { position:absolute; top:16px; left:50%; transform:translateX(-50%); background:rgba(0,0,0,.55); color:#fff; font-size:12px; font-weight:700; padding:4px 14px; border-radius:20px; }
#lb-prev { position:absolute; left:12px; top:50%; transform:translateY(-50%); }
#lb-next { position:absolute; right:12px; top:50%; transform:translateY(-50%); }

/* SPEC CARD */
.sc { background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:10px 12px; display:flex; align-items:center; gap:10px; min-width:0; overflow:hidden; }
.si { width:32px; height:32px; border-radius:8px; background:#eff6ff; display:flex; align-items:center; justify-content:center; color:#0194F3; flex-shrink:0; }
.sl { font-size:9px; color:#94a3b8; font-weight:700; text-transform:uppercase; letter-spacing:.05em; }
.sv { font-size:13px; font-weight:700; color:#1e293b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:1px; }

/* CONTACT BUTTONS */
.btn-tel { display:flex; align-items:center; justify-content:center; gap:8px; width:100%; padding:13px 18px; border-radius:14px; font-weight:700; font-size:15px; border:2px solid #e2e8f0; background:#fff; color:#334155; text-decoration:none; transition:all .2s; }
.btn-tel:hover { border-color:#0194F3; color:#0194F3; background:#eff6ff; }
.btn-wa { display:flex; align-items:center; justify-content:center; gap:8px; width:100%; padding:13px 18px; border-radius:14px; font-weight:700; font-size:15px; background:#25D366; color:#fff; text-decoration:none; transition:all .2s; box-shadow:0 4px 18px rgba(37,211,102,.35); }
.btn-wa:hover { background:#128C7E; }

/* MAPS WRAPPER */
.maps-box { position:relative; height:300px; border-radius:16px; overflow:hidden; border:1px solid #e2e8f0; cursor:pointer; }
.maps-box iframe { width:100%; height:100%; border:0; pointer-events:none; display:block; }
.maps-box .maps-link { position:absolute; inset:0; display:block; }
.maps-box .maps-badge { position:absolute; bottom:10px; right:10px; background:#fff; border-radius:8px; padding:5px 10px; font-size:11px; font-weight:700; color:#1a73e8; display:flex; align-items:center; gap:4px; box-shadow:0 1px 6px rgba(0,0,0,.18); }
</style>

<div class="bg-slate-50 min-h-screen py-10 pt-24">
<div class="max-w-7xl mx-auto px-4">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-slate-500 mb-6 flex-wrap">
        <a href="{{ route('home') }}" class="hover:text-[#0194F3]">WismaIndo</a>
        <i data-lucide="chevron-right" class="w-4 h-4"></i>
        <a href="{{ route($listing->type == 'property' ? 'properti' : 'barangjasa') }}" class="hover:text-[#0194F3] capitalize">{{ $listing->type == 'property' ? 'Properti' : ($listing->type == 'goods' ? 'Barang' : 'Jasa') }}</a>
        <i data-lucide="chevron-right" class="w-4 h-4"></i>
        <span class="text-slate-800 font-semibold truncate max-w-xs">{{ $listing->title }}</span>
    </div>

    {{-- Layout: left 8/12, right 4/12 --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- ═══════ KIRI (8/12) ═══════ --}}
        <div class="lg:col-span-8 space-y-5 min-w-0">

            {{-- GALLERY --}}
            <div class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm">
                {{-- Cover (klik = lightbox, tidak ada prev/next di sini) --}}
                <div id="gallery-cover" onclick="lbOpen(GB_IDX)">
                    @foreach($allImages as $i => $src)
                    <img src="{{ $src }}"
                         alt="Foto {{ $i+1 }}"
                         class="g-img"
                         id="gi{{ $i }}"
                         style="{{ $i > 0 ? 'display:none;' : '' }}"
                         loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                    @endforeach
                    <div id="g-hint">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#334155" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                        </span>
                    </div>
                    @if($totalImages > 1)
                    <div id="g-ctr">1 / {{ $totalImages }}</div>
                    @endif
                </div>

                {{-- Thumbnail Strip (tombol slider ada di sini saja) --}}
                @if($totalImages > 1)
                <div id="tw">
                    <button class="sn" style="left:4px; z-index:10;" onclick="gbGoto(GB_IDX - 1)" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <div id="ts">
                        @foreach($allImages as $i => $src)
                        <button class="tb {{ $i===0?'ton':'' }}" id="tb{{ $i }}" onclick="gbGoto({{ $i }})" type="button">
                            <img src="{{ $src }}" alt="thumb {{ $i+1 }}" loading="lazy">
                        </button>
                        @endforeach
                    </div>
                    <button class="sn" style="right:4px; z-index:10;" onclick="gbGoto(GB_IDX + 1)" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
                @endif
            </div>

            {{-- LIGHTBOX --}}
            <div id="lb" onclick="if(event.target===this)lbClose()">
                <button id="lb-close" class="lbb" onclick="lbClose()" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <div id="lb-ctr"></div>
                @if($totalImages > 1)
                <button id="lb-prev" class="lbb" onclick="lbSlide(-1)" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                @endif
                <img id="lb-img" src="" alt="Preview">
                @if($totalImages > 1)
                <button id="lb-next" class="lbb" onclick="lbSlide(1)" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
                @endif
            </div>

            {{-- INFO UTAMA --}}
            <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-200 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-lg">{{ $listing->listingCategory->name ?? '' }}</span>
                        @if($listing->transaction_type)<span class="px-3 py-1 bg-[#0194F3]/10 text-[#0194F3] text-xs font-bold rounded-lg capitalize">{{ $listing->transaction_type }}</span>@endif
                        @if($listing->condition)<span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-xs font-bold rounded-lg">{{ $listing->condition }}</span>@endif
                    </div>
                    
                    @auth
                    @php
                        $isFavorited = \App\Models\FavoriteListing::where('user_id', auth()->id())->where('listing_id', $listing->id)->exists();
                    @endphp
                    <button type="button" x-data="{ fav: {{ $isFavorited ? 'true' : 'false' }} }" 
                            @click="fav = !fav; fetch('{{ route('iklan.favorit.store', $listing->id) }}', { method: fav ? 'POST' : 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })"
                            class="flex items-center justify-center w-10 h-10 rounded-full border border-slate-200 transition shrink-0"
                            :class="fav ? 'text-rose-500 bg-rose-50 border-rose-200' : 'text-slate-400 bg-white hover:bg-slate-50'">
                        <i data-lucide="heart" class="w-5 h-5" :fill="fav ? 'currentColor' : 'none'"></i>
                    </button>
                    @else
                    <a href="{{ route('login') }}" class="flex items-center justify-center w-10 h-10 rounded-full border border-slate-200 text-slate-400 bg-white transition hover:bg-slate-50 shrink-0">
                        <i data-lucide="heart" class="w-5 h-5"></i>
                    </a>
                    @endauth
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-800 mb-3 leading-tight">{{ $listing->title }}</h1>
                <div class="flex items-start gap-2 text-slate-500 mb-6 pb-5 border-b border-slate-100">
                    <i data-lucide="map-pin" class="w-4 h-4 text-rose-500 shrink-0 mt-0.5"></i>
                    <span class="text-sm">{{ $listing->address ?? $listing->location }}</span>
                </div>

                {{-- SPESIFIKASI --}}
                @if($listing->type == 'property')
                <div class="mb-6">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Spesifikasi Properti</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @if($listing->land_area)<div class="sc"><div class="si"><i data-lucide="map" style="width:15px;height:15px;"></i></div><div style="min-width:0"><p class="sl">Luas Tanah</p><p class="sv">{{ $listing->land_area }} m²</p></div></div>@endif
                        @if($listing->building_area)<div class="sc"><div class="si"><i data-lucide="maximize-2" style="width:15px;height:15px;"></i></div><div style="min-width:0"><p class="sl">Luas Bangunan</p><p class="sv">{{ $listing->building_area }} m²</p></div></div>@endif
                        @if($listing->bedrooms)<div class="sc"><div class="si"><i data-lucide="bed" style="width:15px;height:15px;"></i></div><div style="min-width:0"><p class="sl">K. Tidur</p><p class="sv">{{ $listing->bedrooms }}</p></div></div>@endif
                        @if($listing->bathrooms)<div class="sc"><div class="si"><i data-lucide="bath" style="width:15px;height:15px;"></i></div><div style="min-width:0"><p class="sl">K. Mandi</p><p class="sv">{{ $listing->bathrooms }}</p></div></div>@endif
                        @if($listing->floors)<div class="sc"><div class="si"><i data-lucide="layers" style="width:15px;height:15px;"></i></div><div style="min-width:0"><p class="sl">Jml Lantai</p><p class="sv">{{ $listing->floors }}</p></div></div>@endif
                        @if($listing->carport)<div class="sc"><div class="si"><i data-lucide="car" style="width:15px;height:15px;"></i></div><div style="min-width:0"><p class="sl">Carport</p><p class="sv">{{ $listing->carport }}</p></div></div>@endif
                        @if($listing->garage)<div class="sc"><div class="si"><i data-lucide="warehouse" style="width:15px;height:15px;"></i></div><div style="min-width:0"><p class="sl">Garasi</p><p class="sv">{{ $listing->garage }}</p></div></div>@endif
                        @if($listing->build_year)<div class="sc"><div class="si"><i data-lucide="calendar" style="width:15px;height:15px;"></i></div><div style="min-width:0"><p class="sl">Thn Dibangun</p><p class="sv">{{ $listing->build_year }}</p></div></div>@endif
                        @if($listing->certificate)<div class="sc"><div class="si"><i data-lucide="file-text" style="width:15px;height:15px;"></i></div><div style="min-width:0"><p class="sl">Sertifikat</p><p class="sv">{{ explode(' - ', $listing->certificate)[0] }}</p></div></div>@endif
                        @if($listing->furnished_status)<div class="sc"><div class="si"><i data-lucide="sofa" style="width:15px;height:15px;"></i></div><div style="min-width:0"><p class="sl">Perabotan</p><p class="sv">{{ $listing->furnished_status }}</p></div></div>@endif
                        @if($listing->imb)<div class="sc" style="background:#f0fdf4;border-color:#bbf7d0;"><div class="si" style="background:#dcfce7;color:#16a34a;"><i data-lucide="check-circle" style="width:15px;height:15px;"></i></div><div style="min-width:0"><p class="sv" style="color:#15803d;">Ada IMB</p></div></div>@endif
                        @if($listing->pbb)<div class="sc" style="background:#f0fdf4;border-color:#bbf7d0;"><div class="si" style="background:#dcfce7;color:#16a34a;"><i data-lucide="check-circle" style="width:15px;height:15px;"></i></div><div style="min-width:0"><p class="sv" style="color:#15803d;">Ada PBB</p></div></div>@endif
                    </div>
                </div>
                @endif

                {{-- Deskripsi --}}
                <div class="mb-6 pt-5 border-t border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Deskripsi</p>
                    <div class="prose prose-sm prose-slate max-w-none text-slate-600 leading-relaxed">{!! $listing->description !!}</div>
                </div>

                {{-- Fasilitas --}}
                @if($listing->facilities && is_array($listing->facilities) && count($listing->facilities) > 0)
                <div class="mb-6 pt-5 border-t border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Fasilitas Properti</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach($listing->facilities as $f)
                        <div class="flex items-center gap-2 text-sm text-slate-700 bg-slate-50 rounded-xl px-3 py-2.5 border border-slate-100">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-[#0194F3] shrink-0"></i>
                            <span class="truncate">{{ $f }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Area Sekitar --}}
                @if($listing->surroundings && is_array($listing->surroundings) && count($listing->surroundings) > 0)
                <div class="mb-6 pt-5 border-t border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Area Sekitar</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($listing->surroundings as $a)
                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-50 text-slate-700 rounded-xl border border-slate-100 text-sm">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-rose-400 shrink-0"></i> {{ $a }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- YouTube --}}
                @if($listing->youtube_url)
                @php
                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $listing->youtube_url, $ym);
                    $ytId = $ym[1] ?? null;
                @endphp
                @if($ytId)
                <div class="pt-5 border-t border-slate-100 mb-6">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Video Properti</p>
                    <div class="relative rounded-2xl overflow-hidden shadow-sm bg-slate-900" style="aspect-ratio:16/9;">
                        <iframe class="absolute inset-0 w-full h-full" src="https://www.youtube.com/embed/{{ $ytId }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
                @endif
                @endif

                {{-- MAPS PREVIEW (Berdasarkan Geolokasi) --}}
                @if($listing->latitude && $listing->longitude && isset($siteSettings['google_maps_api_key']) && $siteSettings['google_maps_api_key'] != '')
                <div class="pt-5 border-t border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Lokasi di Peta</p>
                    <div class="relative rounded-2xl overflow-hidden shadow-sm border border-slate-200 group" style="height: 300px;">
                        <a href="https://www.google.com/maps?q={{ $listing->latitude }},{{ $listing->longitude }}" target="_blank" class="absolute inset-0 z-10 block cursor-pointer" title="Buka di Google Maps"></a>
                        <div id="listingMap" class="absolute inset-0 w-full h-full bg-slate-100 flex items-center justify-center pointer-events-none">
                            <span class="text-slate-400 font-medium">Memuat Peta...</span>
                        </div>
                    </div>
                </div>
                @push('scripts')
                <script src="https://maps.googleapis.com/maps/api/js?key={{ $siteSettings['google_maps_api_key'] }}"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var lat = parseFloat("{{ $listing->latitude }}");
                        var lng = parseFloat("{{ $listing->longitude }}");
                        var mapEl = document.getElementById('listingMap');
                        if(mapEl && !isNaN(lat) && !isNaN(lng)) {
                            var map = new google.maps.Map(mapEl, {
                                center: {lat: lat, lng: lng},
                                zoom: 16,
                                mapTypeControl: false,
                                streetViewControl: false,
                            });
                            new google.maps.Marker({
                                position: {lat: lat, lng: lng},
                                map: map,
                                animation: google.maps.Animation.DROP,
                            });
                        }
                    });
                </script>
                @endpush
                @endif
            </div>
        </div>

        {{-- ═══════ KANAN (4/12) ═══════ --}}
        <div class="lg:col-span-4 min-w-0">
            <div class="sticky top-24 space-y-5">

                {{-- CARD HARGA & KONTAK --}}
                <div class="bg-white rounded-3xl p-7 border border-slate-200 shadow-sm">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                        Harga {{ $listing->transaction_type == 'disewa' ? 'Sewa' : 'Jual' }}
                    </p>
                    {{-- Harga: font besar, no-wrap, aman dari text-breaking --}}
                    <p class="text-3xl md:text-4xl lg:text-3xl xl:text-4xl font-black text-[#0194F3] whitespace-nowrap mb-1 overflow-visible">
                        Rp {{ number_format($listing->price, 0, ',', '.') }}
                    </p>
                    @if($listing->rental_period)<p class="text-sm text-slate-500 mb-2">/ {{ $listing->rental_period }}</p>@endif
                    @if($listing->negotiable)
                    <span class="inline-block px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-lg mb-1 mt-2">Bisa Nego</span>
                    @endif

                    <hr class="border-slate-100 mt-8 mb-5">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider text-center mb-4">Hubungi Penjual</p>

                    @php
                        $cPhone = $listing->phone ?? $listing->whatsapp ?? ($listing->user->phone ?? '');
                        $cWa    = $listing->whatsapp ?? $listing->phone ?? ($listing->user->phone ?? '');
                        $telNum = '+62' . ltrim(preg_replace('/[^0-9]/', '', $cPhone), '0');
                        $waNum  = '62'  . ltrim(preg_replace('/[^0-9]/', '', $cWa), '0');
                        $waText = urlencode('Halo, saya tertarik dengan iklan: ' . $listing->title);
                    @endphp

                    <div class="flex flex-col gap-3">
                        <a href="tel:{{ $telNum }}" class="btn-tel">
                            <i data-lucide="phone" style="width:18px;height:18px;flex-shrink:0;"></i> Telepon
                        </a>
                        <a href="https://wa.me/{{ $waNum }}?text={{ $waText }}" target="_blank" class="btn-wa">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;flex-shrink:0;fill:currentColor;" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            WhatsApp
                        </a>
                    </div>
                </div>

                {{-- PROFIL PENJUAL --}}
                <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-slate-100 shadow shrink-0 bg-slate-100">
                            @if($listing->user && $listing->user->avatar)
                                <img src="{{ asset($listing->user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400"><i data-lucide="user" style="width:26px;height:26px;"></i></div>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 text-sm truncate">{{ $listing->user->name ?? 'Pengguna' }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">Bergabung {{ optional($listing->user->created_at)->format('M Y') }}</p>
                        </div>
                    </div>
                </div>

                {{-- IKLAN LAIN DARI PENJUAL INI --}}
                @if(isset($userListings) && $userListings->count() > 0)
                <div class="mt-6">
                    <div class="block w-full py-3 px-4 border border-slate-200 rounded-xl text-center text-sm font-bold text-slate-600 bg-white shadow-sm mb-4">
                        <i data-lucide="layout-grid" class="inline-block w-4 h-4 mr-1.5 -mt-0.5"></i> BUKA IKLAN LAINNYA
                    </div>
                    
                    <div class="flex flex-col gap-4">
                        @foreach($userListings as $uItem)
                        <a href="{{ route('listing.show', $uItem->slug) }}" class="group bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-xl hover:border-[#0194F3]/30 transition-all duration-300 flex flex-col">
                            <div class="relative overflow-hidden bg-slate-100" style="aspect-ratio:4/3;">
                                @if($uItem->cover_image)<img src="{{ asset($uItem->cover_image) }}" alt="{{ $uItem->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else<div class="w-full h-full flex items-center justify-center text-slate-300"><i data-lucide="image" style="width:40px;height:40px;"></i></div>@endif
                                <div class="absolute top-3 left-3"><span class="px-2 py-1 bg-white/90 backdrop-blur-sm text-slate-700 text-xs font-bold rounded-lg capitalize">{{ $uItem->listingCategory->name ?? '' }}</span></div>
                            </div>
                            <div class="p-4 flex flex-col flex-1">
                                <h3 class="font-bold text-slate-800 text-sm mb-1.5 line-clamp-2 group-hover:text-[#0194F3] transition">{{ $uItem->title }}</h3>
                                <p class="text-slate-400 text-xs flex items-center gap-1 mb-2"><i data-lucide="map-pin" style="width:12px;height:12px;flex-shrink:0;"></i><span class="line-clamp-1">{{ $uItem->location ?? 'Lokasi tidak diketahui' }}</span></p>
                                <p class="text-base font-bold text-[#0194F3] mt-auto">Rp {{ number_format($uItem->price, 0, ',', '.') }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>

    {{-- IKLAN TERKAIT --}}
    @if($relatedListings && $relatedListings->count() > 0)
    <div class="mt-16">
        <h2 class="text-2xl font-bold text-slate-800 mb-6">Mungkin Anda juga tertarik</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($relatedListings as $item)
            <a href="{{ route('listing.show', $item->slug) }}" class="group bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-xl hover:border-[#0194F3]/30 transition-all duration-300 flex flex-col">
                <div class="relative overflow-hidden bg-slate-100" style="aspect-ratio:4/3;">
                    @if($item->cover_image)<img src="{{ asset($item->cover_image) }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else<div class="w-full h-full flex items-center justify-center text-slate-300"><i data-lucide="image" style="width:40px;height:40px;"></i></div>@endif
                    <div class="absolute top-3 left-3"><span class="px-2 py-1 bg-white/90 backdrop-blur-sm text-slate-700 text-xs font-bold rounded-lg capitalize">{{ $item->listingCategory->name ?? '' }}</span></div>
                </div>
                <div class="p-4 flex flex-col flex-1">
                    <h3 class="font-bold text-slate-800 text-sm mb-1.5 line-clamp-2 group-hover:text-[#0194F3] transition">{{ $item->title }}</h3>
                    <p class="text-slate-400 text-xs flex items-center gap-1 mb-2"><i data-lucide="map-pin" style="width:12px;height:12px;flex-shrink:0;"></i><span class="line-clamp-1">{{ $item->location ?? 'Lokasi tidak diketahui' }}</span></p>
                    <p class="text-base font-bold text-[#0194F3] mt-auto">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>
</div>
@endsection

@push('scripts')
<script>
/* ======================================================
   SEMUA VARIABEL & FUNGSI GLOBAL — agar onclick bisa akses
   ====================================================== */
var GB_IMGS  = {!! $allImagesJson !!};
var GB_TOTAL = {{ $totalImages }};
var GB_IDX   = 0;
var LB_IDX   = 0;

/* ── switch cover image ── */
function gbGoto(next) {
    /* hide image lama */
    var oldImg = document.getElementById('gi' + GB_IDX);
    var oldThb = document.getElementById('tb' + GB_IDX);
    if (oldImg) oldImg.style.display = 'none';
    if (oldThb) oldThb.classList.remove('ton');

    GB_IDX = ((next % GB_TOTAL) + GB_TOTAL) % GB_TOTAL;

    /* show image baru */
    var newImg = document.getElementById('gi' + GB_IDX);
    var newThb = document.getElementById('tb' + GB_IDX);
    if (newImg) newImg.style.display = 'block';
    if (newThb) {
        newThb.classList.add('ton');
        newThb.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }

    var ctr = document.getElementById('g-ctr');
    if (ctr) ctr.textContent = (GB_IDX + 1) + ' / ' + GB_TOTAL;
}

/* ── scroll thumbnail strip ── */
function scrollStrip(dir) {
    var el = document.getElementById('ts');
    if (el) {
        el.scrollBy({ left: dir * 220, behavior: 'smooth' });
    }
}

/* ── hover hint ── */
var cover = document.getElementById('gallery-cover');
var hint  = document.getElementById('g-hint');
if (cover && hint) {
    cover.addEventListener('mouseenter', function() { hint.style.opacity = '1'; });
    cover.addEventListener('mouseleave', function() { hint.style.opacity = '0'; });
}

/* ── lightbox ── */
function lbUpdate() {
    var img = document.getElementById('lb-img');
    var ctr = document.getElementById('lb-ctr');
    if (img) img.src = GB_IMGS[LB_IDX];
    if (ctr) ctr.textContent = (LB_IDX + 1) + ' / ' + GB_TOTAL;
}

function lbOpen(idx) {
    LB_IDX = (typeof idx !== 'undefined') ? idx : GB_IDX;
    lbUpdate();
    var lb = document.getElementById('lb');
    if (lb) { lb.classList.add('open'); document.body.style.overflow = 'hidden'; }
}

function lbClose() {
    var lb = document.getElementById('lb');
    if (lb) { lb.classList.remove('open'); document.body.style.overflow = ''; }
}

function lbSlide(dir) {
    LB_IDX = ((LB_IDX + dir) % GB_TOTAL + GB_TOTAL) % GB_TOTAL;
    lbUpdate();
}

document.addEventListener('keydown', function(e) {
    var lb = document.getElementById('lb');
    if (!lb || !lb.classList.contains('open')) return;
    if (e.key === 'Escape')     lbClose();
    if (e.key === 'ArrowLeft')  lbSlide(-1);
    if (e.key === 'ArrowRight') lbSlide(1);
});
</script>
@endpush
