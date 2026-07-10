@php
$isEn = app()->getLocale() === 'en';
$enabled = isset($homeArticlesEnabled)
? (bool) $homeArticlesEnabled
: (($siteSettings['home_articles_enabled'] ?? '1') === '1');

$title = $homeArticlesTitle
?? ($siteSettings['home_articles_title'] ?? 'Baca dan bangkitkan semangat liburanmu');

$desc = $homeArticlesDesc
?? ($siteSettings['home_articles_desc'] ?? '');

$buttonText = $homeArticlesButtonText
?? ($siteSettings['home_articles_button_text'] ?? 'Baca Artikel Inspirasi');

$buttonUrl = $homeArticlesButtonUrl
?? ($siteSettings['home_articles_button_url'] ?? route('articles'));

$items = isset($homeArticles) ? $homeArticles : collect();
@endphp

@if($enabled)
<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 py-10 lg:py-12" data-aos="fade-up">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <div class="pill pill-azure">
                    <i data-lucide="book-open" class="w-4 h-4"></i>
                    Artikel
                </div>

                <h2 class="mt-4 text-2xl lg:text-3xl font-extrabold text-slate-900">
                    {{ $title }}
                </h2>

                @if(!empty($desc))
                <p class="mt-2 text-slate-600 max-w-2xl">
                    {{ $desc }}
                </p>
                @endif
            </div>

            <a href="{{ $buttonUrl }}" class="hidden sm:inline-flex btn btn-ghost">
                {{ $buttonText }}
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="mt-7 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($items as $article)
            @php
            $cover = !empty($article->cover_image) ? asset('storage/' . ltrim($article->cover_image, '/')) : null;
            $href = route('article.show', $article->slug);
            $publishedAt = $article->published_at ? $article->published_at->format('d M Y') : null;


            $articleTitle = $isEn ? (($article->title_en ?? null) ?: ($article->title ?? '')) : ($article->title ?? '');
            @endphp


            <div class="group rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm hover:shadow-md transition">
                <a href="{{ $href }}" class="block">
                    <div class="relative aspect-[16/10] bg-slate-100 overflow-hidden">
                        @if($cover)
                        <img
                            src="{{ $cover }}"
                            alt="{{ $articleTitle }}"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.03] transition duration-500"
                            loading="lazy">
                        @else
                        <div class="absolute inset-0 bg-gradient-to-tr from-slate-100 via-white to-white"></div>
                        <div class="absolute inset-0 grid place-items-center text-slate-500 text-xs">
                            {{ $isEn ? 'No Image' : 'Tidak ada gambar' }}
                        </div>
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/35 via-slate-950/0 to-transparent"></div>
                    </div>

                    <div class="p-4">
                        <div class="text-[15px] font-extrabold text-slate-900 line-clamp-2 group-hover:text-[#0194F3] transition">
                            {{ $articleTitle }}
                        </div>

                        @if($publishedAt)
                        <div class="mt-2 text-xs text-slate-500 inline-flex items-center gap-1.5">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                            <span>{{ $publishedAt }}</span>
                        </div>
                        @endif
                    </div>
                </a>

                <div class="px-4 pb-4">
                    <a href="{{ $href }}" class="btn btn-primary w-full justify-center">
                        {{ $isEn ? 'Read More' : 'Lihat Selengkapnya' }}
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="sm:col-span-2 lg:col-span-4 rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center text-slate-600">
                {{ $isEn ? 'No articles to display yet.' : 'Belum ada artikel untuk ditampilkan.' }}
            </div>
            @endforelse
        </div>

        <div class="mt-6 sm:hidden">
            <a href="{{ $buttonUrl }}" class="btn btn-ghost w-full justify-center">
                {{ $buttonText }}
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</section>
@endif