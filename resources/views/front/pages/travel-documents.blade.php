@extends('layouts.front')
@php $isEn = app()->getLocale() === 'en'; @endphp

@section('title', $pageTitle . ' - ' . ($siteSettings['seo_site_title'] ?? 'Bintang Wisata'))
@if(!empty($metaDesc))
@section('meta_description', $metaDesc)
@endif

@section('content')

<section class="relative overflow-hidden bg-white">
    <div class="absolute inset-0 travel-grid opacity-70"></div>

    <div class="max-w-7xl mx-auto px-4 py-10 lg:py-14 relative">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-extrabold border"
                style="background:rgba(1,148,243,.08);border-color:rgba(1,148,243,.20);color:#0194F3;">
                <span class="h-2 w-2 rounded-full" style="background:#0194F3;"></span>
                {{ $heroBadge }}
            </div>

            <h1 class="mt-4 text-3xl lg:text-5xl font-extrabold tracking-tight text-slate-900">
                {{ $heroTitle }}
            </h1>

            <p class="mt-3 text-slate-600 text-base lg:text-lg leading-relaxed">
                {{ $heroDesc }}
            </p>
        </div>
    </div>
</section>

<section class="bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 py-10 lg:py-14"
        x-data="{ tab: 'passport' }">

        {{-- Tabs (style mirip foto, tetap tema brand) --}}
        <div class="w-full overflow-x-auto">
            <div class="inline-flex rounded-2xl overflow-hidden border"
                style="border-color: rgba(1,148,243,.20); background: rgba(1,148,243,.08);">

                @php
                $tabs = [
                ['key' => 'passport', 'label' => $tabPassportTitle],
                ['key' => 'visa', 'label' => $tabVisaTitle],
                ['key' => 'passport_price', 'label' => $passportPriceTitle],
                ['key' => 'visa_price', 'label' => $visaPriceTitle],
                ['key' => 'immigration', 'label' => $immigrationTitle],
                ['key' => 'order', 'label' => $orderTitle],
                ['key' => 'download', 'label' => $downloadTitle],
                ];
                @endphp

                @foreach($tabs as $i => $t)
                <button type="button"
                    @click="tab='{{ $t['key'] }}'"
                    class="px-5 py-3 text-sm font-extrabold whitespace-nowrap border-r last:border-r-0 transition"
                    :class="tab==='{{ $t['key'] }}' ? 'bg-white text-slate-900' : 'text-white'"
                    :style="tab==='{{ $t['key'] }}'
                            ? 'background:#fff;'
                            : 'background:var(--brand);'"
                    style="border-color: rgba(255,255,255,.25);">
                    {{ $t['label'] }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Panel --}}
        <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

            {{-- Passport --}}
            <div x-show="tab==='passport'" x-cloak>
                <div class="px-6 py-4 font-extrabold text-white"
                    style="background:var(--brand);">
                    {{ $tabPassportTitle }}
                </div>
                <div class="p-6">
                    <div class="prose max-w-none quill-content">{!! $passportHtml !!}</div>
                </div>
            </div>

            {{-- Visa --}}
            <div x-show="tab==='visa'" x-cloak>
                <div class="px-6 py-4 font-extrabold text-white"
                    style="background:var(--brand);">
                    {{ $tabVisaTitle }}
                </div>
                <div class="p-6">
                    <div class="prose max-w-none quill-content">{!! $visaHtml !!}</div>
                </div>
            </div>

            {{-- Harga Paspor --}}
            <div x-show="tab==='passport_price'" x-cloak>
                <div class="px-6 py-4 font-extrabold text-white"
                    style="background:var(--brand);">
                    {{ $passportPriceTitle }}
                </div>
                <div class="p-6">
                    <div class="prose max-w-none quill-content">{!! $passportPriceHtml !!}</div>
                </div>
            </div>

            {{-- Harga Visa --}}
            <div x-show="tab==='visa_price'" x-cloak>
                <div class="px-6 py-4 font-extrabold text-white"
                    style="background:var(--brand);">
                    {{ $visaPriceTitle }}
                </div>
                <div class="p-6">
                    <div class="prose max-w-none quill-content">{!! $visaPriceHtml !!}</div>
                </div>
            </div>

            {{-- Info Imigrasi --}}
            <div x-show="tab==='immigration'" x-cloak>
                <div class="px-6 py-4 font-extrabold text-white"
                    style="background:var(--brand);">
                    {{ $immigrationTitle }}
                </div>
                <div class="p-6">
                    <div class="prose max-w-none quill-content">{!! $immigrationHtml !!}</div>
                </div>
            </div>

            {{-- Pemesanan --}}
            <div x-show="tab==='order'" x-cloak>
                <div class="px-6 py-4 font-extrabold text-white flex items-center justify-between"
                    style="background:var(--brand);">
                    <span>{{ $orderTitle }}</span>

                    @if(!empty($orderWa))
                    @php
                    $wa = preg_replace('/[^0-9]/', '', $orderWa);
                    $waLink = 'https://wa.me/' . $wa;
                    @endphp
                    <a href="{{ $waLink }}" target="_blank"
                        class="rounded-xl bg-white px-4 py-2 text-sm font-extrabold"
                        style="color: var(--brand);">
                        WhatsApp
                    </a>
                    @endif
                </div>

                <div class="p-6">
                    <div class="prose max-w-none quill-content">{!! $orderHtml !!}</div>
                </div>
            </div>

            {{-- Download --}}
            <div x-show="tab==='download'" x-cloak>
                <div class="px-6 py-4 font-extrabold text-white"
                    style="background:var(--brand);">
                    {{ $downloadTitle }}
                </div>

                <div class="p-6">
                    @if(empty($downloads))
                    <div class="text-sm text-slate-600">
                        {{ $isEn ? 'No downloads available yet.' : 'Belum ada file untuk diunduh.' }}
                    </div>
                    @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($downloads as $d)
                        @php
                        $label = $d['label'] ?? '';
                        $url = $d['url'] ?? '';
                        @endphp
                        @if($label && $url)
                        <a href="{{ $url }}" target="_blank"
                            class="group rounded-2xl border border-slate-200 bg-white px-4 py-3 hover:bg-slate-50 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-extrabold text-slate-900 truncate">{{ $label }}</div>
                                <div class="text-xs text-slate-500 truncate">{{ $url }}</div>
                            </div>
                            <span class="h-9 w-9 rounded-xl grid place-items-center border"
                                style="background:rgba(1,148,243,.08);border-color:rgba(1,148,243,.20)">
                                <i data-lucide="download" class="w-4 h-4" style="color:var(--brand);"></i>
                            </span>
                        </a>
                        @endif
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</section>
@endsection