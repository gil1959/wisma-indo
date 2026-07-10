<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Documentation;
use App\Models\Setting;

class DocumentationController extends Controller
{
    public function tour()
    {
        return $this->renderDocs('tour');
    }

    public function ship()
    {
        return $this->renderDocs('ship');
    }

    public function umrah()
    {
        return $this->renderDocs('umrah');
    }

    private function renderDocs(string $category)
    {
        $isEn = app()->getLocale() === 'en';

        $pageTitle = match ($category) {
            'ship'  => $isEn ? 'Ship Rental Documentation' : 'Dokumentasi Sewa Kapal',
            'umrah' => $isEn ? 'Umrah Documentation' : 'Dokumentasi Umrah',
            default => $isEn ? 'Tour Documentation' : 'Dokumentasi Paket Tour',
        };

        $photos = Documentation::query()
            ->where('category', $category)
            ->where('type', 'photo')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        $videos = Documentation::query()
            ->where('category', $category)
            ->where('type', 'video')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        $kBadge = "docs_{$category}_hero_badge";
        $kTitle = "docs_{$category}_hero_title";
        $kDesc  = "docs_{$category}_hero_desc";

        // locale-aware: coba *_en dulu kalau EN, fallback ke ID, lalu fallback global docs_*
        $heroBadge = $isEn
            ? Setting::getValue($kBadge . '_en', Setting::getValue($kBadge, Setting::getValue('docs_hero_badge_en', Setting::getValue('docs_hero_badge', $pageTitle))))
            : Setting::getValue($kBadge, Setting::getValue('docs_hero_badge', $pageTitle));

        $heroTitle = $isEn
            ? Setting::getValue($kTitle . '_en', Setting::getValue($kTitle, Setting::getValue('docs_hero_title_en', Setting::getValue('docs_hero_title', $pageTitle))))
            : Setting::getValue($kTitle, Setting::getValue('docs_hero_title', $pageTitle));

        $fallbackDescId = 'Galeri dokumentasi perjalanan dan aktivitas layanan kami, terdiri dari foto dan video.';
        $fallbackDescEn = 'A gallery of our trips and service activities, consisting of photos and videos.';

        $heroDesc = $isEn
            ? Setting::getValue($kDesc . '_en', Setting::getValue($kDesc, Setting::getValue('docs_hero_desc_en', Setting::getValue('docs_hero_desc', $fallbackDescEn))))
            : Setting::getValue($kDesc, Setting::getValue('docs_hero_desc', $fallbackDescId));

        return view('front.pages.docs', compact('photos', 'videos', 'pageTitle', 'heroBadge', 'heroTitle', 'heroDesc'));
    }
}
