<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeBanner;
use App\Models\HomeButton;
use App\Models\HomeLocation;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        $banners = HomeBanner::orderBy('order')->get();
        $buttons = HomeButton::orderBy('order')->get();
        $locations = HomeLocation::orderBy('order')->get();

        return view('admin.home_settings.index', compact('settings', 'banners', 'buttons', 'locations'));
    }

    public function updateHero(Request $request)
    {
        $request->validate([
            'home_hero_bg_image' => 'nullable|image|max:2048',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:255',
            'cta_title' => 'nullable|string|max:255',
            'cta_subtitle' => 'nullable|string|max:255',
            'cta_button_text' => 'nullable|string|max:255',
            'cta_button_link' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('home_hero_bg_image')) {
            $old = Setting::where('key', 'home_hero_bg_image')->value('value');
            if ($old && str_starts_with($old, '/storage/')) {
                Storage::delete(str_replace('/storage/', 'public/', $old));
            }
            $path = $request->file('home_hero_bg_image')->store('public/settings');
            Setting::updateOrCreate(['key' => 'home_hero_bg_image'], ['value' => Storage::url($path)]);
        }

        $textKeys = ['hero_title', 'hero_subtitle', 'cta_title', 'cta_subtitle', 'cta_button_text', 'cta_button_link'];
        foreach ($textKeys as $key) {
            if ($request->has($key)) {
                Setting::updateOrCreate(['key' => $key], ['value' => $request->input($key) ?? '']);
            }
        }

        return back()->with('success', 'Background & Teks Hero berhasil diupdate.');
    }

    public function updateSectionTexts(Request $request)
    {
        $data = $request->validate([
            'home_tipe_title' => 'nullable|string|max:120',
            'home_tipe_desc' => 'nullable|string|max:255',
            'home_kategori_barang_title' => 'nullable|string|max:120',
            'home_kategori_barang_desc' => 'nullable|string|max:255',
            'home_kategori_jasa_title' => 'nullable|string|max:120',
            'home_kategori_jasa_desc' => 'nullable|string|max:255',
            'home_lokasi_title' => 'nullable|string|max:120',
            'home_lokasi_desc' => 'nullable|string|max:255',
            'home_rekomendasi_title' => 'nullable|string|max:120',
            'home_rekomendasi_desc' => 'nullable|string|max:255',
            'home_kebutuhan_title' => 'nullable|string|max:120',
            'home_kebutuhan_desc' => 'nullable|string|max:255',
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
        }

        return back()->with('success', 'Teks Section Beranda berhasil diupdate.');
    }

    // --- HOME BANNER CRUD ---
    public function storeBanner(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|max:2048',
            'url' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;
        $path = $request->file('image')->store('public/home_banners');
        $validated['image'] = Storage::url($path);

        HomeBanner::create($validated);
        return back()->with('success', 'Banner berhasil ditambahkan.');
    }

    public function updateBanner(Request $request, HomeBanner $banner)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'url' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::delete(str_replace('/storage/', 'public/', $banner->image));
            }
            $path = $request->file('image')->store('public/home_banners');
            $validated['image'] = Storage::url($path);
        }

        $banner->update($validated);
        return back()->with('success', 'Banner berhasil diupdate.');
    }

    public function destroyBanner(HomeBanner $banner)
    {
        if ($banner->image) {
            Storage::delete(str_replace('/storage/', 'public/', $banner->image));
        }
        $banner->delete();
        return back()->with('success', 'Banner berhasil dihapus.');
    }

    // --- HOME BUTTON CRUD ---
    public function storeButton(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'icon_image' => 'required|image|max:1024',
            'url' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;
        $path = $request->file('icon_image')->store('public/home_buttons');
        $validated['icon_image'] = Storage::url($path);

        HomeButton::create($validated);
        return back()->with('success', 'Tombol berhasil ditambahkan.');
    }

    public function updateButton(Request $request, HomeButton $button)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'icon_image' => 'nullable|image|max:1024',
            'url' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        if ($request->hasFile('icon_image')) {
            if ($button->icon_image) {
                Storage::delete(str_replace('/storage/', 'public/', $button->icon_image));
            }
            $path = $request->file('icon_image')->store('public/home_buttons');
            $validated['icon_image'] = Storage::url($path);
        }

        $button->update($validated);
        return back()->with('success', 'Tombol berhasil diupdate.');
    }

    public function destroyButton(HomeButton $button)
    {
        if ($button->icon_image) {
            Storage::delete(str_replace('/storage/', 'public/', $button->icon_image));
        }
        $button->delete();
        return back()->with('success', 'Tombol berhasil dihapus.');
    }

    // --- HOME LOCATION CRUD ---
    public function storeLocation(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'required|image|max:2048',
            'url' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;
        $path = $request->file('image')->store('public/home_locations');
        $validated['image'] = Storage::url($path);

        HomeLocation::create($validated);
        return back()->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function updateLocation(Request $request, HomeLocation $location)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'url' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        if ($request->hasFile('image')) {
            if ($location->image) {
                Storage::delete(str_replace('/storage/', 'public/', $location->image));
            }
            $path = $request->file('image')->store('public/home_locations');
            $validated['image'] = Storage::url($path);
        }

        $location->update($validated);
        return back()->with('success', 'Lokasi berhasil diupdate.');
    }

    public function destroyLocation(HomeLocation $location)
    {
        if ($location->image) {
            Storage::delete(str_replace('/storage/', 'public/', $location->image));
        }
        $location->delete();
        return back()->with('success', 'Lokasi berhasil dihapus.');
    }
}
