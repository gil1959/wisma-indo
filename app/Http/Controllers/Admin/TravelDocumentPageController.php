<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\TranslateService;
use Illuminate\Http\Request;

class TravelDocumentPageController extends Controller
{
    public function edit()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.travel-documents.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'travel_docs_meta_title' => ['nullable', 'string', 'max:120'],
            'travel_docs_meta_desc'  => ['nullable', 'string', 'max:180'],

            'travel_docs_hero_badge' => ['nullable', 'string', 'max:80'],
            'travel_docs_hero_title' => ['nullable', 'string', 'max:120'],
            'travel_docs_hero_desc'  => ['nullable', 'string', 'max:250'],

            'travel_docs_tab_passport_title' => ['nullable', 'string', 'max:40'],
            'travel_docs_tab_visa_title'     => ['nullable', 'string', 'max:40'],

            'travel_docs_passport_html' => ['nullable', 'string'],
            'travel_docs_visa_html'     => ['nullable', 'string'],

            'travel_docs_passport_price_title' => ['nullable', 'string', 'max:80'],
            'travel_docs_passport_price_html'  => ['nullable', 'string'],

            'travel_docs_visa_price_title' => ['nullable', 'string', 'max:80'],
            'travel_docs_visa_price_html'  => ['nullable', 'string'],

            'travel_docs_immigration_title' => ['nullable', 'string', 'max:80'],
            'travel_docs_immigration_html'  => ['nullable', 'string'],

            'travel_docs_order_title' => ['nullable', 'string', 'max:120'],
            'travel_docs_order_html'  => ['nullable', 'string'],

            'travel_docs_download_title' => ['nullable', 'string', 'max:80'],

            'travel_docs_order_whatsapp' => ['nullable', 'string', 'max:40'],

            'download_label' => ['nullable', 'array'],
            'download_label.*' => ['nullable', 'string', 'max:120'],
            'download_url' => ['nullable', 'array'],
            'download_url.*' => ['nullable', 'string', 'max:500'],
        ]);

        // Build downloads JSON (ID)
        $labels = $validated['download_label'] ?? [];
        $urls   = $validated['download_url'] ?? [];
        $items  = [];

        $max = max(count($labels), count($urls));
        for ($i = 0; $i < $max; $i++) {
            $label = trim((string)($labels[$i] ?? ''));
            $url   = trim((string)($urls[$i] ?? ''));
            if ($label !== '' && $url !== '') {
                $items[] = ['label' => $label, 'url' => $url];
            }
        }

        // Save original ID settings
        $keysToSave = $validated;
        unset($keysToSave['download_label'], $keysToSave['download_url']);

        foreach ($keysToSave as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
        }

        Setting::updateOrCreate(
            ['key' => 'travel_docs_downloads'],
            ['value' => json_encode($items, JSON_UNESCAPED_UNICODE)]
        );

        // AUTO TRANSLATE to EN (ngikut LegalPagesController)
        try {
            $tx = app(TranslateService::class);

            $textKeys = [
                'travel_docs_meta_title',
                'travel_docs_meta_desc',
                'travel_docs_hero_badge',
                'travel_docs_hero_title',
                'travel_docs_hero_desc',
                'travel_docs_tab_passport_title',
                'travel_docs_tab_visa_title',
                'travel_docs_passport_price_title',
                'travel_docs_visa_price_title',
                'travel_docs_immigration_title',
                'travel_docs_order_title',
                'travel_docs_download_title',
            ];

            $htmlKeys = [
                'travel_docs_passport_html',
                'travel_docs_visa_html',
                'travel_docs_passport_price_html',
                'travel_docs_visa_price_html',
                'travel_docs_immigration_html',
                'travel_docs_order_html',
            ];

            $textVals = [];
            foreach ($textKeys as $k) $textVals[] = (string)($validated[$k] ?? '');

            $htmlVals = [];
            foreach ($htmlKeys as $k) $htmlVals[] = (string)($validated[$k] ?? '');

            $enText = $tx->toEnBatch($textVals, 'text');
            $enHtml = $tx->toEnBatch($htmlVals, 'html');

            foreach ($textKeys as $i => $k) {
                Setting::updateOrCreate(['key' => $k . '_en'], ['value' => $enText[$i] ?? '']);
            }

            foreach ($htmlKeys as $i => $k) {
                Setting::updateOrCreate(['key' => $k . '_en'], ['value' => $enHtml[$i] ?? '']);
            }

            // translate download labels -> EN JSON
            $dlLabels = array_map(fn($x) => (string)($x['label'] ?? ''), $items);
            $enDlLabels = $tx->toEnBatch($dlLabels, 'text');

            $enItems = [];
            foreach ($items as $i => $it) {
                $enItems[] = [
                    'label' => $enDlLabels[$i] ?? ($it['label'] ?? ''),
                    'url'   => $it['url'] ?? '',
                ];
            }

            Setting::updateOrCreate(
                ['key' => 'travel_docs_downloads_en'],
                ['value' => json_encode($enItems, JSON_UNESCAPED_UNICODE)]
            );
        } catch (\Throwable $e) {
            // jangan bikin gagal save
        }

        return back()->with('success', 'Konten halaman Document berhasil disimpan.');
    }
}
