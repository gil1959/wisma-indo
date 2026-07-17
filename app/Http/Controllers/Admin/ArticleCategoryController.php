<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleCategoryController extends Controller
{
    public function index()
    {
        $categories = ArticleCategory::latest()->paginate(10);
        return view('admin.article_categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        ArticleCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, ArticleCategory $articleCategory)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        $articleCategory->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return back()->with('success', 'Kategori berhasil diupdate.');
    }

    public function destroy(ArticleCategory $articleCategory)
    {
        $articleCategory->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
