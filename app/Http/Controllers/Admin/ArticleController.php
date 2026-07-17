<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('category')->latest()->paginate(10);
        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        $categories = ArticleCategory::all();
        return view('admin.articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:article_categories,id',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_desc' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($request->title);
        $validated['is_published'] = $request->has('is_published');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/articles');
            $validated['image'] = Storage::url($path);
        }

        Article::create($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function edit(Article $article)
    {
        $categories = ArticleCategory::all();
        return view('admin.articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:article_categories,id',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_desc' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($request->title);
        $validated['is_published'] = $request->has('is_published');

        if ($request->hasFile('image')) {
            if ($article->image) {
                $oldPath = str_replace('/storage/', 'public/', $article->image);
                Storage::delete($oldPath);
            }
            $path = $request->file('image')->store('public/articles');
            $validated['image'] = Storage::url($path);
        }

        $article->update($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil diupdate.');
    }

    public function destroy(Article $article)
    {
        if ($article->image) {
            $oldPath = str_replace('/storage/', 'public/', $article->image);
            Storage::delete($oldPath);
        }
        $article->delete();
        return back()->with('success', 'Artikel berhasil dihapus.');
    }
}
