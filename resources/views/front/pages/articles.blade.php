@extends('layouts.front')

@section('content')
<div class="pt-32 pb-20 min-h-screen bg-slate-50 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-slate-800 mb-4">Artikel & Inspirasi</h1>
            <p class="text-slate-600 max-w-xl mx-auto text-lg">Temukan beragam inspirasi desain, tips properti, dan informasi terbaru seputar hunian idaman Anda.</p>
        </div>

        <!-- Filter & Search -->
        <div class="flex flex-col md:flex-row gap-4 justify-between items-center mb-10">
            <div class="flex gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0 scrollbar-hide">
                <a href="{{ route('articles') }}" class="px-5 py-2 rounded-full whitespace-nowrap text-sm font-semibold transition-all duration-300 {{ !request('category') ? 'bg-[#0194F3] text-white shadow-md shadow-[#0194F3]/30' : 'bg-white text-slate-600 border border-slate-200 hover:border-[#0194F3] hover:text-[#0194F3]' }}">
                    Semua
                </a>
                @foreach($categories as $cat)
                <a href="{{ route('articles', ['category' => $cat->slug]) }}" class="px-5 py-2 rounded-full whitespace-nowrap text-sm font-semibold transition-all duration-300 {{ request('category') == $cat->slug ? 'bg-[#0194F3] text-white shadow-md shadow-[#0194F3]/30' : 'bg-white text-slate-600 border border-slate-200 hover:border-[#0194F3] hover:text-[#0194F3]' }}">
                    {{ $cat->name }}
                </a>
                @endforeach
            </div>

            <form action="{{ route('articles') }}" method="GET" class="w-full md:w-80 relative">
                @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari artikel..." class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20 shadow-sm transition-all duration-300">
                <i data-lucide="search" class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
            </form>
        </div>

        <!-- Articles Grid -->
        @if($articles->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            @foreach($articles as $article)
            <article class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 group border border-slate-100 flex flex-col h-full cursor-pointer" onclick="window.location.href='{{ route('articles.show', $article->slug) }}'">
                <div class="relative overflow-hidden aspect-[4/3]">
                    <img src="{{ $article->image ? asset($article->image) : 'https://placehold.co/800x600?text=No+Image' }}" alt="{{ $article->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-full text-xs font-bold text-[#0194F3] shadow-sm">
                        {{ $article->category->name ?? 'Uncategorized' }}
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex items-center gap-4 text-xs font-semibold text-slate-500 mb-4">
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                            {{ $article->created_at->translatedFormat('d M Y') }}
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            {{ number_format($article->views) }}x dibaca
                        </div>
                    </div>
                    <h2 class="text-xl font-bold text-slate-800 mb-3 line-clamp-2 group-hover:text-[#0194F3] transition-colors">
                        <a href="{{ route('articles.show', $article->slug) }}">
                            {{ $article->title }}
                        </a>
                    </h2>
                    <p class="text-slate-600 line-clamp-3 mb-6 flex-grow">
                        {{ strip_tags($article->content) }}
                    </p>
                    <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-slate-200 overflow-hidden flex items-center justify-center">
                                <img src="https://ui-avatars.com/api/?name=Admin&background=0194F3&color=fff" alt="Admin" class="w-full h-full object-cover">
                            </div>
                            <span class="text-sm font-semibold text-slate-700">Admin Wisma Indo</span>
                        </div>
                        <span class="text-sm font-bold text-[#0194F3] flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                            Baca <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </span>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $articles->links() }}
        </div>
        
        @else
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mb-6">
                <i data-lucide="file-x" class="w-12 h-12 text-slate-400"></i>
            </div>
            <h3 class="text-2xl font-bold text-slate-800 mb-2">Artikel Tidak Ditemukan</h3>
            <p class="text-slate-500 max-w-md">Maaf, kami tidak dapat menemukan artikel yang Anda cari. Silakan coba kata kunci lain atau pilih kategori yang berbeda.</p>
        </div>
        @endif
    </div>
</div>
@endsection