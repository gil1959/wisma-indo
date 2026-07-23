<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeBanner;
use App\Models\HomeButton;
use App\Models\HomeLocation;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\BankPartner;
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
        $testimonials = Testimonial::orderBy('order')->get();
        $bankPartners = BankPartner::orderBy('order')->get();

        return view('admin.home_settings.index', compact('settings', 'banners', 'buttons', 'locations', 'testimonials', 'bankPartners'));
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
            'home_rekomendasi_title' => 'nullable|string|max:120',
            'home_rekomendasi_desc' => 'nullable|string|max:255',
            'home_testimoni_title' => 'nullable|string|max:120',
            'home_partner_title' => 'nullable|string|max:120',
            'home_partner_desc' => 'nullable|string|max:255',
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
        }

        return back()->with('success', 'Teks Section Beranda berhasil diupdate.');
    }

    public function updateFeaturesPanel(Request $request)
    {
        $validated = $request->validate([
            'feature_1_title' => 'nullable|string|max:120',
            'feature_1_desc' => 'nullable|string|max:255',
            'feature_1_icon' => 'nullable|image|max:1024',
            
            'feature_2_title' => 'nullable|string|max:120',
            'feature_2_desc' => 'nullable|string|max:255',
            'feature_2_icon' => 'nullable|image|max:1024',
            
            'feature_3_title' => 'nullable|string|max:120',
            'feature_3_desc' => 'nullable|string|max:255',
            'feature_3_icon' => 'nullable|image|max:1024',
            
            'feature_4_title' => 'nullable|string|max:120',
            'feature_4_desc' => 'nullable|string|max:255',
            'feature_4_icon' => 'nullable|image|max:1024',
        ]);

        foreach (range(1, 4) as $i) {
            $titleKey = "feature_{$i}_title";
            $descKey = "feature_{$i}_desc";
            $iconKey = "feature_{$i}_icon";

            if ($request->has($titleKey)) {
                Setting::updateOrCreate(['key' => $titleKey], ['value' => $request->input($titleKey) ?? '']);
            }
            if ($request->has($descKey)) {
                Setting::updateOrCreate(['key' => $descKey], ['value' => $request->input($descKey) ?? '']);
            }
            if ($request->hasFile($iconKey)) {
                $old = Setting::where('key', $iconKey)->value('value');
                if ($old && str_starts_with($old, '/storage/')) {
                    Storage::delete(str_replace('/storage/', 'public/', $old));
                }
                $path = $request->file($iconKey)->store('public/settings');
                Setting::updateOrCreate(['key' => $iconKey], ['value' => Storage::url($path)]);
            }
        }

        return back()->with('success', 'Panel Fitur Utama berhasil diupdate.');
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

    public function storeTestimonial(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string',
            'avatar' => 'nullable|image|max:1024',
            'order' => 'required|integer',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('public/testimonials');
            $data['avatar'] = Storage::url($path);
        }

        $data['is_active'] = $request->has('is_active');
        Testimonial::create($data);

        return back()->with('success', 'Testimoni berhasil ditambahkan.');
    }

    public function updateTestimonial(Request $request, Testimonial $testimonial)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string',
            'avatar' => 'nullable|image|max:1024',
            'order' => 'required|integer',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('avatar')) {
            if ($testimonial->avatar) {
                Storage::delete(str_replace('/storage/', 'public/', $testimonial->avatar));
            }
            $path = $request->file('avatar')->store('public/testimonials');
            $data['avatar'] = Storage::url($path);
        }

        $data['is_active'] = $request->has('is_active');
        $testimonial->update($data);

        return back()->with('success', 'Testimoni berhasil diupdate.');
    }

    public function destroyTestimonial(Testimonial $testimonial)
    {
        if ($testimonial->avatar) {
            Storage::delete(str_replace('/storage/', 'public/', $testimonial->avatar));
        }
        $testimonial->delete();
        return back()->with('success', 'Testimoni berhasil dihapus.');
    }

    public function storeBankPartner(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required|image|max:1024',
            'order' => 'required|integer',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('public/bank_partners');
            $data['logo'] = Storage::url($path);
        }

        $data['is_active'] = $request->has('is_active');
        BankPartner::create($data);

        return back()->with('success', 'Partner Bank berhasil ditambahkan.');
    }

    public function updateBankPartner(Request $request, BankPartner $bankPartner)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:1024',
            'order' => 'required|integer',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('logo')) {
            if ($bankPartner->logo) {
                Storage::delete(str_replace('/storage/', 'public/', $bankPartner->logo));
            }
            $path = $request->file('logo')->store('public/bank_partners');
            $data['logo'] = Storage::url($path);
        }

        $data['is_active'] = $request->has('is_active');
        $bankPartner->update($data);

        return back()->with('success', 'Partner Bank berhasil diupdate.');
    }

    public function destroyBankPartner(BankPartner $bankPartner)
    {
        if ($bankPartner->logo) {
            Storage::delete(str_replace('/storage/', 'public/', $bankPartner->logo));
        }
        $bankPartner->delete();
        return back()->with('success', 'Partner Bank berhasil dihapus.');
    }
}
