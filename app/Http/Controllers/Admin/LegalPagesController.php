<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Services\TranslateService;

class LegalPagesController extends Controller
{
    public function edit()
    {
        $settings = Setting::pluck('value', 'key');

        return view('admin.legal-pages.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'legal_privacy_title' => ['nullable', 'string', 'max:120'],
            'legal_privacy_html'  => ['nullable', 'string'],

            'legal_terms_title'   => ['nullable', 'string', 'max:120'],
            'legal_terms_html'    => ['nullable', 'string'],

            'legal_contact_title' => ['nullable', 'string', 'max:120'],
            'legal_contact_html'  => ['nullable', 'string'],
        ]);

        // save original (ID)
        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }

        // AUTO TRANSLATE to EN (same system: *_en via TranslateService)
        try {
            $tx = app(TranslateService::class);

            $textKeys = [
                'legal_privacy_title',
                'legal_terms_title',
                'legal_contact_title',
            ];

            $htmlKeys = [
                'legal_privacy_html',
                'legal_terms_html',
                'legal_contact_html',
            ];

            $textVals = [];
            foreach ($textKeys as $k) $textVals[] = $validated[$k] ?? '';

            $htmlVals = [];
            foreach ($htmlKeys as $k) $htmlVals[] = $validated[$k] ?? '';

            $enText = $tx->toEnBatch($textVals, 'text');
            $enHtml = $tx->toEnBatch($htmlVals, 'html');

            foreach ($textKeys as $i => $k) {
                Setting::updateOrCreate(
                    ['key' => $k . '_en'],
                    ['value' => $enText[$i] ?? '']
                );
            }

            foreach ($htmlKeys as $i => $k) {
                Setting::updateOrCreate(
                    ['key' => $k . '_en'],
                    ['value' => $enHtml[$i] ?? '']
                );
            }
        } catch (\Throwable $e) {
            // jangan bikin gagal save. sama pola di tempat lain: silent fail.
        }

        return back()->with('success', 'Konten halaman legal berhasil disimpan.');
    }
}
