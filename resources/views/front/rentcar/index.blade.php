@extends('layouts.front')

@php $isEn = app()->getLocale() === 'en'; @endphp
@section('title', $isEn ? 'Car Rental' : 'Rental Mobil')

@section('content')

{{-- ================= HERO ================= --}}
<section class="relative overflow-hidden bg-white">
    <div class="absolute inset-0 travel-dots opacity-70"></div>

    <svg class="absolute -top-16 -left-16 w-[520px] h-[520px] opacity-80" viewBox="0 0 600 600" fill="none" aria-hidden="true">
        <defs>
            <radialGradient id="rentGlow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(260 320) rotate(90) scale(280)">
                <stop stop-color="#0194F3" stop-opacity="0.20" />
                <stop offset="1" stop-color="#0194F3" stop-opacity="0" />
            </radialGradient>
        </defs>
        <circle cx="260" cy="320" r="280" fill="url(#rentGlow)" />
        <path d="M120 260c60 40 110 60 170 60 90 0 155-45 250-125" stroke="#0194F3" stroke-opacity="0.22" stroke-width="2" stroke-linecap="round" />
    </svg>

    <div class="max-w-7xl mx-auto px-4 pt-12 pb-10 relative">
        <div class="grid gap-8 lg:grid-cols-12 items-center">
            {{-- Left --}}
            <div class="lg:col-span-7" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-extrabold"
                    style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                    <span class="h-2 w-2 rounded-full" style="background:#0194F3;"></span>
                    {{ $siteSettings['rentcar_hero_badge'] ?? 'Rental Mobil' }}

                </div>

                <h1 class="mt-4 text-3xl lg:text-4xl font-extrabold text-slate-900 leading-tight">
                    {{ $siteSettings['rentcar_hero_title'] ?? 'Pilihan Mobil Terbaik untuk Perjalanan Anda' }}
                </h1>


                <p class="mt-3 max-w-2xl text-slate-600">
                    {{ $siteSettings['rentcar_hero_desc'] ?? 'Armada terawat, harga transparan, dan proses booking cepat tanpa ribet.' }}
                </p>



                <div class="mt-6 flex flex-wrap gap-2">
                    <span class="pill pill-azure"><i data-lucide="shield-check" class="w-4 h-4"></i> {{ $siteSettings['rentcar_chip1'] ?? 'Terawat' }}</span>
                    <span class="pill pill-azure"><i data-lucide="wallet" class="w-4 h-4"></i> {{ $siteSettings['rentcar_chip2'] ?? 'Transparan' }}</span>
                    <span class="pill pill-azure"><i data-lucide="clock" class="w-4 h-4"></i> {{ $siteSettings['rentcar_chip3'] ?? 'Cepat' }}</span>
                    <span class="pill pill-azure"><i data-lucide="map" class="w-4 h-4"></i> {{ $siteSettings['rentcar_chip4'] ?? 'Travel Ready' }}</span>
                </div>

            </div>

            {{-- Right --}}
            <div class="lg:col-span-5" data-aos="fade-up" data-aos-delay="80">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft relative overflow-hidden">
                    <div class="absolute inset-0 travel-grid opacity-60 pointer-events-none"></div>

                    <div class="relative">
                        <div class="flex items-start gap-3">
                            <div class="icon-badge mt-0.5">
                                <i data-lucide="info" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="font-extrabold text-slate-900">{{ $siteSettings['rentcar_note_title'] ?? 'Catatan' }}</div>
                                <div class="text-sm text-slate-600 mt-1">
                                    {{ $siteSettings['rentcar_note_desc'] ?? 'Klik “Booking Sekarang” untuk lihat detail unit.' }}
                                </div>

                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="fuel" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['rentcar_note1_title'] ?? 'Hemat' }}

                                </div>
                                <div class="text-xs text-slate-600 mt-1">{{ $siteSettings['rentcar_note1_desc'] ?? 'Nyaman untuk perjalanan' }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="sparkles" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['rentcar_note2_title'] ?? 'Bersih' }}

                                </div>
                                <div class="text-xs text-slate-600 mt-1">{{ $siteSettings['rentcar_note2_desc'] ?? 'Unit terawat' }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="users" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['rentcar_note3_title'] ?? 'Kapasitas' }}

                                </div>
                                <div class="text-xs text-slate-600 mt-1">{{ $siteSettings['rentcar_note3_desc'] ?? 'Cocok keluarga/grup' }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="route" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['rentcar_note4_title'] ?? 'Fleksibel' }}

                                </div>
                                <div class="text-xs text-slate-600 mt-1">{{ $siteSettings['rentcar_note4_desc'] ?? 'Untuk wisata & kerja' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Divider ke section bawah --}}
        <svg class="block w-full mt-10" viewBox="0 0 1440 90" fill="none" aria-hidden="true">
            <path d="M0 35C180 85 360 85 540 50C720 15 900 15 1080 50C1260 85 1350 80 1440 55V90H0V35Z" fill="#F8FAFC" />
        </svg>
    </div>
</section>

{{-- ================= FILTER BAR ================= --}}
{{-- ================= FILTER BAR (Rent Car style like Tours) ================= --}}
<section class="max-w-7xl mx-auto px-4">
    <div class="card p-5 -mt-8 relative z-10" data-aos="fade-up" data-aos-delay="100">
        <form method="GET" action="{{ route('rentcar.index') }}" class="grid gap-4 md:grid-cols-12 items-end">

            {{-- SEARCH --}}
            <div class="md:col-span-5">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">{{ $isEn ? 'Search' : 'Pencarian' }}</label>
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        value="{{ old('q', $q ?? request('q')) }}"
                        placeholder="{{ $isEn ? 'Example: Avanza, Innova, Hiace...' : 'Contoh: Avanza, Innova, Hiace...' }}"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 pl-11 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </span>
                </div>
            </div>

            {{-- CATEGORY --}}
            <div class="md:col-span-3">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">{{ $isEn ? 'Category' : 'Kategori' }}</label>
                <select
                    name="category_id"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200">
                    <option value="">{{ $isEn ? 'All Categories' : 'Semua Kategori' }}</option>
                    @foreach(($categories ?? []) as $c)
                    <option value="{{ $c->id }}"
                        {{ (string)($categoryId ?? request('category_id')) === (string)$c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- SORT --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">{{ $isEn ? 'Sort' : 'Urutkan' }}</label>
                <select
                    name="sort"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200">
                    <option value="latest" {{ ($sort ?? request('sort','latest'))==='latest' ? 'selected' : '' }}>{{ $isEn ? 'Latest' : 'Terbaru' }}</option>
                    <option value="price_asc" {{ ($sort ?? request('sort'))==='price_asc' ? 'selected' : '' }}>{{ $isEn ? 'Price ↑' : 'Harga ↑' }}</option>
                    <option value="price_desc" {{ ($sort ?? request('sort'))==='price_desc' ? 'selected' : '' }}>{{ $isEn ? 'Price ↓' : 'Harga ↓' }}</option>
                    <option value="title_asc" {{ ($sort ?? request('sort'))==='title_asc' ? 'selected' : '' }}>{{ $isEn ? 'Name (A–Z)' : 'Nama (A-Z)' }}</option>
                </select>
            </div>

            {{-- ACTIONS --}}
            <div class="md:col-span-2 flex gap-3">
                <button class="btn btn-primary w-full" type="submit">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                    {{ $isEn ? 'Apply' : 'Terapkan' }}
                </button>

                <a class="btn btn-ghost w-full" href="{{ route('rentcar.index') }}">
                    {{ $isEn ? 'Reset' : 'Reset' }}
                </a>
            </div>
        </form>

        {{-- Active filters summary --}}
        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-slate-600">
            <span class="font-extrabold text-slate-700">{{ $isEn ? 'Active filters:' : 'Filter aktif:' }}</span>

            @if(request('q'))
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-1">
                <i data-lucide="type" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                {{ $isEn ? 'Keyword:' : 'Kata kunci:' }} <span class="font-extrabold">{{ request('q') }}</span>
            </span>
            @endif

            @if(request('category_id'))
            @php
            $activeCat = collect($categories ?? [])->firstWhere('id', (int) request('category_id'));
            @endphp
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-1">
                <i data-lucide="tag" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                {{ $isEn ? 'Category:' : 'Kategori:' }} <span class="font-extrabold">{{ $activeCat?->name ?? '—' }}</span>
            </span>
            @endif

            @if(request('sort'))
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-1">
                <i data-lucide="arrow-up-down" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                Sort: <span class="font-extrabold">{{ request('sort') }}</span>
            </span>
            @endif

            @if(!request('q') && !request('category_id') && !request('sort'))
            <span class="text-slate-500">{{ $isEn ? 'None' : 'Tidak ada' }}</span>
            @endif
        </div>
    </div>
</section>


{{-- ================= GRID ================= --}}
<section class="bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 pb-14">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">

            @forelse ($packages as $package)
            <a href="{{ route('rentcar.show', $package->slug) }}"
                class="group block bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">

                <div class="relative h-44 overflow-hidden bg-slate-100">
                    <img
                        src="{{ asset('storage/' . $package->thumbnail_path) }}"
                        alt="{{ $package->title }}"
                        class="h-full w-full object-cover"
                        loading="lazy">

                    {{-- badge kiri --}}
                    <div class="absolute top-3 left-3">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/92 border border-slate-200 px-3 py-1 text-xs font-extrabold text-slate-700 shadow">
                            <i data-lucide="car" class="w-4 h-4" style="color:#0194F3;"></i>
                            {{ $isEn ? 'Car Rental' : 'Rental Mobil' }}
                        </span>
                    </div>

                    {{-- label kanan --}}
                    @if(!empty($package->label))
                    <div class="absolute top-3 right-3">
                        <span class="inline-flex items-center rounded-full bg-white/90 backdrop-blur border border-white/60 px-3 py-1 text-xs font-extrabold text-slate-900 shadow">
                            {{ $package->label }}
                        </span>
                    </div>
                    @endif
                </div>

                <div class="px-4 pt-4 pb-3">
                    <div class="text-[15px] font-extrabold text-[#0194F3] line-clamp-2">
                        @php
                        $cardTitle = $isEn ? ($package->title_en ?: $package->title) : $package->title;
                        $cardFeatSrc = $isEn ? ($package->features_en ?: $package->features) : $package->features;
                        @endphp

                        {{ $cardTitle }}
                    </div>

                    <div class="mt-2 text-sm">
                        <span class="text-slate-600">{{ $isEn ? 'From ' : 'Mulai ' }}</span>
                        <span class="font-extrabold text-rose-600">
                            Rp {{ number_format((int)$package->price_per_12_hours, 0, ',', '.') }}
                        </span>
                        <span class="text-slate-500">{{ $isEn ? '/ 12 Hours' : '/ 12 Jam' }}</span>
                    </div>
                </div>

                <div class="border-t border-slate-200 px-4 pt-3 pb-4">
                    {{-- ambil 1 feature biar “sesuai isi paket” tanpa bikin card jadi rame --}}
                    <div class="flex items-center gap-2 text-xs text-slate-600">
                        <i data-lucide="info" class="w-4 h-4" style="color:#0194F3;"></i>
                        <span class="line-clamp-1">
                            {{ !empty($cardFeatSrc[0]['name']) ? $cardFeatSrc[0]['name'] : ($isEn ? 'Unit available for booking' : 'Unit tersedia untuk booking') }}
                        </span>
                    </div>

                    <div class="mt-3">
                        <div class="btn btn-primary w-full justify-center !rounded-md !py-2">
                            {{ $isEn ? 'Book Now' : 'Booking Sekarang' }}
                        </div>
                    </div>
                </div>
            </a>


            @empty
            <div class="col-span-full">
                <div class="card p-10 text-center">
                    <div class="mx-auto h-14 w-14 rounded-2xl border flex items-center justify-center"
                        style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22);">
                        <i data-lucide="car" class="w-6 h-6" style="color:#0194F3;"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-extrabold text-slate-900">{{ $isEn ? 'No rental packages yet' : 'Belum ada paket rental' }}</h3>
                    <p class="mt-2 text-slate-600">{{ $isEn ? 'Please check back later, or contact us for recommendations.' : 'Silakan cek kembali nanti, atau konsultasi untuk rekomendasi unit.' }}</p>

                </div>
            </div>
            @endforelse

        </div>

        {{-- Pagination (kalau ada) --}}
        @if(method_exists($packages, 'links'))
        <div class="mt-10">
            {{ $packages->withQueryString()->links() }}
        </div>
        @endif
    </div>
</section>

@endsection