<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function general()
    {
        $settings = Setting::pluck('value', 'key');
        $footerLogos = \App\Models\FooterLogo::orderBy('order')->get();
        $offlineMethods = \App\Models\OfflinePaymentMethod::all();
        return view('admin.settings.general', compact('settings', 'footerLogos', 'offlineMethods'));
    }

    public function storeFooterLogo(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|max:2048',
            'url' => 'nullable|url|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $path = $request->file('image')->store('public/footer_logos');
        $data['image'] = Storage::url($path);
        $data['is_active'] = $request->has('is_active');
        $data['order'] = $data['order'] ?? 0;

        \App\Models\FooterLogo::create($data);

        return redirect()->back()->with('success', 'Logo footer berhasil ditambahkan!');
    }

    public function destroyFooterLogo(\App\Models\FooterLogo $logo)
    {
        $logo->delete();
        return redirect()->back()->with('success', 'Logo footer berhasil dihapus!');
    }

    public function saveGeneral(Request $request)
    {
        $data = $request->validate([
            'brand_name'       => ['nullable', 'string', 'max:100'],
            'seo_meta_title'   => ['nullable', 'string', 'max:120'],
            'seo_meta_desc'    => ['nullable', 'string', 'max:255'],
            'seo_meta_keywords'=> ['nullable', 'string', 'max:255'],
            
            'footer_address'   => ['nullable', 'string', 'max:1000'],
            'footer_phone'     => ['nullable', 'string', 'max:50'],
            'footer_email'     => ['nullable', 'email', 'max:255'],
            'footer_whatsapp'  => ['nullable', 'string', 'max:30'],
            'footer_tagline'   => ['nullable', 'string', 'max:400'],
            'footer_copyright' => ['nullable', 'string', 'max:200'],

            'site_logo'        => ['nullable', 'image', 'max:2048'],
            'site_favicon'     => ['nullable', 'file', 'mimes:ico,png,jpg,jpeg', 'max:1024'],

            'tripay_merchant_code' => ['nullable', 'string', 'max:100'],
            'tripay_api_key'       => ['nullable', 'string', 'max:255'],
            'tripay_private_key'   => ['nullable', 'string', 'max:255'],
            'tripay_active'        => ['nullable', 'boolean'],
            'tripay_mode'          => ['nullable', 'string', 'in:sandbox,production'],
            'xendit_api_key'       => ['nullable', 'string', 'max:255'],
            'xendit_active'        => ['nullable', 'boolean'],
            'xendit_callback_token'=> ['nullable', 'string', 'max:255'],
            'offline_unique_code_min' => ['nullable', 'numeric'],
            'offline_unique_code_max' => ['nullable', 'numeric'],
            
            // Integrations
            'google_login_active'  => ['nullable', 'boolean'],
            'google_client_id'     => ['nullable', 'string', 'max:255'],
            'google_client_secret' => ['nullable', 'string', 'max:255'],
            'google_maps_api_key'  => ['nullable', 'string', 'max:255'],
            'gemini_api_key'       => ['nullable', 'string', 'max:255'],
        ]);

        $keys = [
            'brand_name', 'seo_meta_title', 'seo_meta_desc', 'seo_meta_keywords',
            'footer_address', 'footer_phone', 'footer_email', 'footer_whatsapp',
            'footer_tagline', 'footer_copyright',
            'tripay_merchant_code', 'tripay_api_key', 'tripay_private_key', 'tripay_active', 'tripay_mode',
            'xendit_api_key', 'xendit_active', 'xendit_callback_token',
            'offline_unique_code_min', 'offline_unique_code_max',
            'google_login_active', 'google_client_id', 'google_client_secret', 'google_maps_api_key', 'gemini_api_key'
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::updateOrCreate(['key' => $key], ['value' => $data[$key] ?? '']);
            }
        }

        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('public/settings');
            Setting::updateOrCreate(['key' => 'site_logo'], ['value' => Storage::url($path)]);
        }

        if ($request->hasFile('site_favicon')) {
            $path = $request->file('site_favicon')->store('public/settings');
            Setting::updateOrCreate(['key' => 'site_favicon'], ['value' => Storage::url($path)]);
        }

        return redirect()->back()->with('success', 'General Settings berhasil disimpan!');
    }
}
