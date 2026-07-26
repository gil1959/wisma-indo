<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

    @foreach($staticUrls as $url)
    <url>
        <loc>{{ $url }}</loc>
        <lastmod>{{ now()->tz('Asia/Jakarta')->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    @foreach($listings as $listing)
    <url>
        <loc>{{ route('listing.show', $listing->slug) }}</loc>
        <lastmod>{{ $listing->updated_at->tz('Asia/Jakarta')->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
    @endforeach

    @foreach($partners as $partner)
    <url>
        <loc>{{ route('agent.show', $partner->id) }}</loc>
        <lastmod>{{ $partner->updated_at->tz('Asia/Jakarta')->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    @foreach($articles as $article)
    <url>
        <loc>{{ route('articles.show', $article->slug) }}</loc>
        <lastmod>{{ $article->updated_at->tz('Asia/Jakarta')->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

    @foreach($pages as $page)
    <url>
        <loc>{{ route('page.show', $page->slug) }}</loc>
        <lastmod>{{ $page->updated_at->tz('Asia/Jakarta')->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    @endforeach

</urlset>
