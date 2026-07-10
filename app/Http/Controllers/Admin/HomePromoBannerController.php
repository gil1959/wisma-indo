<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomePromoBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class HomePromoBannerController extends Controller
{
    private function guardSection(string $section): array
    {
        $map = [
            'discount' => 'Home Banner: Discount',
            'missions' => 'Home Banner: Missions',
        ];

        if (!isset($map[$section])) {
            abort(404);
        }

        return [$section, $map[$section]];
    }

    public function index(string $section)
    {
        [$section, $title] = $this->guardSection($section);

        $items = HomePromoBanner::query()
            ->where('section', $section)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.promos.home-banners.index', compact('section', 'title', 'items'));
    }

    public function create(string $section)
    {
        [$section, $title] = $this->guardSection($section);

        $banner = new HomePromoBanner([
            'section' => $section,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        return view('admin.promos.home-banners.form', compact('section', 'title', 'banner'));
    }

    public function store(Request $request, string $section)
    {
        [$section, $title] = $this->guardSection($section);

        $data = $request->validate([
            'thumbnail' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'link_url'  => ['required', 'string', 'max:255'],
            'sort_order'=> ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $path = $request->file('thumbnail')->store('home-banners', 'public');

        HomePromoBanner::create([
            'section' => $section,
            'thumbnail_path' => $path,
            'link_url' => $data['link_url'],
            'sort_order' => (int)($data['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.promos.home-banners.index', ['section' => $section])
            ->with('success', 'Banner berhasil ditambahkan.');
    }

    public function edit(string $section, HomePromoBanner $banner)
    {
        [$section, $title] = $this->guardSection($section);

        if ($banner->section !== $section) abort(404);

        return view('admin.promos.home-banners.form', compact('section', 'title', 'banner'));
    }

    public function update(Request $request, string $section, HomePromoBanner $banner)
    {
        [$section, $title] = $this->guardSection($section);

        if ($banner->section !== $section) abort(404);

        $data = $request->validate([
            'thumbnail' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'link_url'  => ['required', 'string', 'max:255'],
            'sort_order'=> ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('thumbnail')) {
            // hapus file lama kalau ada
            if ($banner->thumbnail_path && Storage::disk('public')->exists($banner->thumbnail_path)) {
                Storage::disk('public')->delete($banner->thumbnail_path);
            }
            $banner->thumbnail_path = $request->file('thumbnail')->store('home-banners', 'public');
        }

        $banner->link_url = $data['link_url'];
        $banner->sort_order = (int)($data['sort_order'] ?? 0);
        $banner->is_active = $request->boolean('is_active');
        $banner->save();

        return redirect()
            ->route('admin.promos.home-banners.index', ['section' => $section])
            ->with('success', 'Banner berhasil diupdate.');
    }

    public function destroy(string $section, HomePromoBanner $banner)
    {
        [$section, $title] = $this->guardSection($section);

        if ($banner->section !== $section) abort(404);

        if ($banner->thumbnail_path && Storage::disk('public')->exists($banner->thumbnail_path)) {
            Storage::disk('public')->delete($banner->thumbnail_path);
        }

        $banner->delete();

        return redirect()
            ->route('admin.promos.home-banners.index', ['section' => $section])
            ->with('success', 'Banner berhasil dihapus.');
    }
}
