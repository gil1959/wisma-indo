<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UmrahCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UmrahCategoryController extends Controller
{
    public function index()
    {
        $categories = UmrahCategory::latest()->paginate(20);
        return view('admin.umrah-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.umrah-categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:umrah_categories,slug',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        UmrahCategory::create($data);

        return redirect()->route('admin.umrah-categories.index')
            ->with('success', 'Kategori Umrah berhasil dibuat.');
    }

    public function edit(UmrahCategory $umrahCategory)
    {
        return view('admin.umrah-categories.edit', compact('umrahCategory'));
    }

    public function update(Request $request, UmrahCategory $umrahCategory)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:umrah_categories,slug,' . $umrahCategory->id,
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $umrahCategory->update($data);

        return redirect()->route('admin.umrah-categories.index')
            ->with('success', 'Kategori Umrah berhasil diupdate.');
    }

    public function destroy(UmrahCategory $umrahCategory)
    {
        $umrahCategory->delete();

        return redirect()->route('admin.umrah-categories.index')
            ->with('success', 'Kategori Umrah berhasil dihapus.');
    }
}
