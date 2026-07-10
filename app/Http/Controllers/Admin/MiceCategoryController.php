<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MiceCategoryController extends Controller
{
    public function index()
    {
        $categories = MiceCategory::orderBy('name')->get();
        return view('admin.mice-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.mice-categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:mice_categories,slug'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        MiceCategory::create($data);

        return redirect()
            ->route('admin.mice-categories.index')
            ->with('success', 'Kategori MICE berhasil dibuat.');
    }

    public function edit(MiceCategory $mice_category)
    {
        return view('admin.mice-categories.edit', ['category' => $mice_category]);
    }

    public function update(Request $request, MiceCategory $mice_category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:mice_categories,slug,' . $mice_category->id],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $mice_category->update($data);

        return redirect()
            ->route('admin.mice-categories.index')
            ->with('success', 'Kategori MICE berhasil diupdate.');
    }

    public function destroy(MiceCategory $mice_category)
    {
        // kalau ada package, nanti restrict (FK) bakal nolak — ini sesuai request lo.
        $mice_category->delete();

        return redirect()
            ->route('admin.mice-categories.index')
            ->with('success', 'Kategori MICE berhasil dihapus.');
    }
}
