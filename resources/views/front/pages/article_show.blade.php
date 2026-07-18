@extends('layouts.front')

@section('title', $article->title . ' - Wisma Indo')
@section('meta_description', $article->meta_desc ?? strip_tags(Str::limit($article->content, 150)))

@section('content')
<div class="pt-32 pb-20 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4">
        
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm font-semibold text-slate-500 mb-8">
            <a href="{{ route('home') }}" class="hover:text-[#0194F3] transition-colors">Beranda</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <a href="{{ route('articles') }}" class="hover:text-[#0194F3] transition-colors">Artikel</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <a href="{{ route('articles', ['category' => $article->category->slug]) }}" class="hover:text-[#0194F3] transition-colors">{{ $article->category->name ?? 'Uncategorized' }}</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-slate-800 line-clamp-1">{{ $article->title }}</span>
        </div>

        <!-- Article Header -->
        <header class="mb-10 text-center">
            <a href="{{ route('articles', ['category' => $article->category->slug]) }}" class="inline-block px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-full text-xs font-bold mb-4 hover:bg-indigo-100 transition-colors">
                {{ $article->category->name ?? 'Uncategorized' }}
            </a>
            <h1 class="text-4xl md:text-5xl font-extrabold text-slate-800 mb-6 leading-tight">
                {{ $article->title }}
            </h1>
            <div class="flex flex-wrap items-center justify-center gap-6 text-sm font-medium text-slate-500">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-full bg-slate-200 overflow-hidden flex items-center justify-center">
                        <img src="https://ui-avatars.com/api/?name=Admin&background=0194F3&color=fff" alt="Admin" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <span class="block text-slate-700 font-bold text-left">Admin Wisma Indo</span>
                        <span class="block text-xs">Penulis</span>
                    </div>
                </div>
                <div class="w-1 h-1 rounded-full bg-slate-300"></div>
                <div class="flex items-center gap-2">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                    {{ $article->created_at->translatedFormat('d F Y') }}
                </div>
                <div class="w-1 h-1 rounded-full bg-slate-300"></div>
                <div class="flex items-center gap-2">
                    <i data-lucide="eye" class="w-5 h-5"></i>
                    {{ number_format($article->views) }}x dibaca
                </div>
            </div>
        </header>

        <!-- Featured Image -->
        <div class="w-full aspect-[21/9] md:aspect-[2.5/1] rounded-3xl overflow-hidden shadow-xl mb-12">
            <img src="{{ $article->image ? asset($article->image) : 'https://placehold.co/1200x600?text=No+Image' }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
        </div>

            <!-- Share Horizontal Buttons -->
            <div class="flex items-center gap-4 mb-8 pb-8 border-b border-slate-100">
                <span class="text-sm font-bold text-slate-500 uppercase tracking-widest">Bagikan:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 hover:text-white hover:bg-blue-600 shadow-sm flex items-center justify-center transition-all duration-300">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($article->title) }}" target="_blank" class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 hover:text-white hover:bg-sky-500 shadow-sm flex items-center justify-center transition-all duration-300">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                <a href="https://api.whatsapp.com/send?text={{ urlencode($article->title . ' ' . url()->current()) }}" target="_blank" class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 hover:text-white hover:bg-green-500 shadow-sm flex items-center justify-center transition-all duration-300">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                </a>
            </div>

            <div class="prose prose-lg prose-slate max-w-none 
                prose-headings:font-bold prose-headings:text-slate-800 
                prose-p:text-slate-600 prose-p:leading-relaxed
                prose-a:text-[#0194F3] prose-a:no-underline hover:prose-a:underline
                prose-img:rounded-xl prose-img:shadow-sm">
                {!! $article->content !!}
            </div>

            <!-- Tags (If any) -->
            @if($article->tags)
            <div class="mt-10 pt-8 border-t border-slate-100">
                <div class="flex flex-wrap gap-2">
                    @foreach(explode(',', $article->tags) as $tag)
                    <span class="px-3 py-1 bg-slate-100 text-slate-600 text-sm font-semibold rounded-lg">#{{ trim($tag) }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Related Articles -->
        @if($relatedArticles->count() > 0)
        <div>
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-bold text-slate-800">Artikel Terkait</h3>
                <a href="{{ route('articles', ['category' => $article->category->slug]) }}" class="text-[#0194F3] font-semibold hover:underline">Lihat Semua</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($relatedArticles as $related)
                <article class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 group border border-slate-100 flex flex-col cursor-pointer" onclick="window.location.href='{{ route('articles.show', $related->slug) }}'">
                    <div class="relative overflow-hidden aspect-[4/3]">
                        <img src="{{ $related->image ? asset($related->image) : 'https://placehold.co/800x600?text=No+Image' }}" alt="{{ $related->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-3">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                            {{ $related->created_at->translatedFormat('d M Y') }}
                        </div>
                        <h4 class="text-lg font-bold text-slate-800 mb-2 line-clamp-2 group-hover:text-[#0194F3] transition-colors">
                            <a href="{{ route('articles.show', $related->slug) }}">
                                {{ $related->title }}
                            </a>
                        </h4>
                        <p class="text-sm text-slate-600 line-clamp-2 mt-auto">
                            {{ strip_tags($related->content) }}
                        </p>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
