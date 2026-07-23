@extends('layouts.front')

@section('content')
<div class="pt-28 pb-20 min-h-screen bg-slate-50 px-4">
    <div class="max-w-7xl mx-auto">
        
        <!-- Search Bar at Top -->
        <div class="mb-10 w-full">
            <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-100">
                <h3 class="font-extrabold text-slate-800 mb-4 text-lg">Pencarian</h3>
                <form action="{{ route('articles') }}" method="GET" class="flex flex-col sm:flex-row gap-3 md:gap-4">
                    @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="relative flex-grow">
                        <i data-lucide="search" class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari artikel..." class="w-full pl-12 pr-4 py-3.5 border border-slate-200 rounded-xl focus:border-[#0194F3] focus:ring focus:ring-[#0194F3]/20 text-slate-700 bg-white shadow-sm transition-all">
                    </div>
                    <button type="submit" class="bg-[#0194F3] hover:bg-blue-600 text-white px-8 py-3.5 font-bold transition-colors rounded-xl shadow-sm flex items-center justify-center gap-2 shrink-0">
                        <i data-lucide="search" class="w-4 h-4"></i> Cari
                    </button>
                </form>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Left: Main Content (Articles Grid) -->
            <div class="lg:w-3/4">
                @if($articles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                    @foreach($articles as $article)
                    <article class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-slate-100 flex flex-col h-full cursor-pointer group" onclick="window.location.href='{{ route('articles.show', $article->slug) }}'">
                        <div class="relative overflow-hidden aspect-[16/10]">
                            <img src="{{ $article->image ? asset($article->image) : 'https://placehold.co/800x600?text=No+Image' }}" alt="{{ $article->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            
                            <!-- Date Badge over image -->
                            <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 shadow-sm flex items-center gap-1.5">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-[#0194F3]"></i>
                                {{ $article->created_at->translatedFormat('d M Y') }}
                            </div>
                        </div>
                        
                        <div class="p-6 flex flex-col flex-grow">
                            <h2 class="text-xl font-extrabold text-slate-800 mb-3 line-clamp-3 group-hover:text-[#0194F3] transition-colors leading-tight">
                                <a href="{{ route('articles.show', $article->slug) }}">
                                    {{ $article->title }}
                                </a>
                            </h2>
                            <p class="text-slate-500 line-clamp-3 mb-6 flex-grow text-sm leading-relaxed">
                                {{ strip_tags($article->content) }}
                            </p>
                            <div class="mt-auto">
                                <a href="{{ route('articles.show', $article->slug) }}" class="text-[15px] font-bold text-[#0194F3] inline-flex items-center gap-1 group-hover:gap-2 transition-all">
                                    Baca Selengkapnya &rarr;
                                </a>
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
                <div class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-3xl border border-slate-100">
                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                        <i data-lucide="file-x" class="w-12 h-12 text-slate-300"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-2">Artikel Tidak Ditemukan</h3>
                    <p class="text-slate-500 max-w-md">Maaf, kami tidak dapat menemukan artikel yang Anda cari. Silakan coba kata kunci lain.</p>
                </div>
                @endif
            </div>

            <!-- Right: Sidebar -->
            <div class="lg:w-1/4">
                <div class="bg-white rounded-3xl border border-slate-100 p-6 sticky top-28 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-extrabold text-slate-800">Artikel Terbaru</h3>
                        <span class="bg-blue-50 text-[#0194F3] px-2.5 py-1 rounded-xl text-xs font-bold flex items-center gap-1">
                            <i data-lucide="sparkles" class="w-3 h-3"></i> Update
                        </span>
                    </div>

                    <div class="space-y-4">
                        @foreach($recentArticles ?? [] as $recent)
                        <a href="{{ route('articles.show', $recent->slug) }}" class="flex gap-4 group">
                            <div class="w-20 h-20 rounded-2xl overflow-hidden shrink-0 bg-slate-100">
                                <img src="{{ $recent->image ? asset($recent->image) : 'https://placehold.co/100x100?text=No+Img' }}" alt="{{ $recent->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            </div>
                            <div class="flex flex-col justify-center">
                                <h4 class="font-bold text-slate-800 text-sm line-clamp-3 group-hover:text-[#0194F3] transition-colors leading-snug mb-1">
                                    {{ $recent->title }}
                                </h4>
                                <div class="flex items-center gap-1.5 text-xs text-slate-400 font-medium">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                    {{ $recent->created_at->translatedFormat('d M Y') }}
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection