@extends('layouts.front')

@if($page->meta_title)
@section('title', $page->meta_title)
@endif

@if($page->meta_desc)
@section('meta_desc', $page->meta_desc)
@endif

@if($page->meta_keywords)
@section('meta_keywords', $page->meta_keywords)
@endif

@if($page->social_title)
@section('social_title', $page->social_title)
@endif

@if($page->social_desc)
@section('social_desc', $page->social_desc)
@endif

@if($page->seo_image)
@section('seo_image', asset($page->seo_image))
@endif

@section('content')
<div class="bg-slate-50 min-h-screen py-10 pt-24">
    <div class="max-w-4xl mx-auto px-4">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-6 flex-wrap">
            <a href="{{ route('home') }}" class="hover:text-[#0194F3]">WismaIndo</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-slate-800 font-semibold truncate">{{ $page->title }}</span>
        </div>

        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
            <h1 class="text-3xl font-extrabold text-slate-900 mb-6">{{ $page->title }}</h1>
            
            <div class="prose prose-slate max-w-none">
                {!! $page->content !!}
            </div>
        </div>
    </div>
</div>
@endsection
