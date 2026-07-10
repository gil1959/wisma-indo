<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function edit()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.seo.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'seo_site_title' => ['required', 'string', 'max:120'],
            'seo_meta_description' => ['nullable', 'string', 'max:300'],
            'seo_keywords' => ['nullable', 'string', 'max:300'],
        ]);

        Setting::updateOrCreate(['key' => 'seo_site_title'], ['value' => $data['seo_site_title']]);
        Setting::updateOrCreate(['key' => 'seo_meta_description'], ['value' => $data['seo_meta_description'] ?? '']);
        Setting::updateOrCreate(['key' => 'seo_keywords'], ['value' => $data['seo_keywords'] ?? '']);

        return back()->with('success', 'SEO berhasil disimpan.');
    }
}
