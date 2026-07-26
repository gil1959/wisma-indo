<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Listing;
use App\Models\User;
use App\Models\Article;
use App\Models\Page;

class SitemapController extends Controller
{
    public function index()
    {
        // 1. Static URLs
        $staticUrls = [
            route('home'),
            route('dijual'),
            route('disewakan'),
            route('properti'),
            route('barangjasa'),
            route('simulasi'),
            route('simulasi.kemampuan'),
            route('quran'),
            route('cobroke'),
            route('articles'),
            route('privacy'),
            route('terms'),
            route('contact'),
            route('partner.register'),
        ];

        // 2. Active Listings
        $listings = Listing::where('status', 'tersedia')->latest()->get();

        // 3. Partner Profiles
        $partners = User::role('partner')->latest()->get();

        // 4. Articles
        $articles = Article::where('is_published', true)->latest()->get();

        // 5. Pages
        $pages = Page::where('is_active', true)->latest()->get();

        return response()->view('front.sitemap', compact('staticUrls', 'listings', 'partners', 'articles', 'pages'))
            ->header('Content-Type', 'text/xml');
    }
}
