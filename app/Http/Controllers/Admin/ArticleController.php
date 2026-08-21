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
            'slug' => 'nullable|string|max:255|unique:articles',
            'category_id' => 'nullable|exists:article_categories,id',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_desc' => 'nullable|string',
            'seo_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'meta_keywords' => 'nullable|string',
            'social_title' => 'nullable|string|max:255',
            'social_desc' => 'nullable|string',
        ]);

        if (!empty($request->slug)) {
            $validated['slug'] = Str::slug($request->slug);
        } else {
            $validated['slug'] = Str::slug($request->title);
        }
        
        $validated['is_published'] = $request->has('is_published');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/articles');
            $validated['image'] = Storage::url($path);
        }

        if ($request->hasFile('seo_image')) {
            $seoPath = $request->file('seo_image')->store('public/articles/seo');
            $validated['seo_image'] = Storage::url($seoPath);
        }

        if ($request->has('meta_keywords') && $request->meta_keywords) {
            $keywords = json_decode($request->meta_keywords, true);
            if (is_array($keywords)) {
                $validated['meta_keywords'] = implode(', ', array_column($keywords, 'value'));
            }
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
            'slug' => 'nullable|string|max:255|unique:articles,slug,' . $article->id,
            'category_id' => 'nullable|exists:article_categories,id',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_desc' => 'nullable|string',
            'seo_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'meta_keywords' => 'nullable|string',
            'social_title' => 'nullable|string|max:255',
            'social_desc' => 'nullable|string',
        ]);

        if (!empty($request->slug)) {
            $validated['slug'] = Str::slug($request->slug);
        } else {
            $validated['slug'] = Str::slug($request->title);
        }
        
        $validated['is_published'] = $request->has('is_published');

        if ($request->hasFile('image')) {
            if ($article->image) {
                $oldPath = str_replace('/storage/', 'public/', $article->image);
                Storage::delete($oldPath);
            }
            $path = $request->file('image')->store('public/articles');
            $validated['image'] = Storage::url($path);
        }

        if ($request->hasFile('seo_image')) {
            if ($article->seo_image) {
                $oldSeoPath = str_replace('/storage/', 'public/', $article->seo_image);
                Storage::delete($oldSeoPath);
            }
            $seoPath = $request->file('seo_image')->store('public/articles/seo');
            $validated['seo_image'] = Storage::url($seoPath);
        }

        if ($request->has('meta_keywords') && $request->meta_keywords) {
            $keywords = json_decode($request->meta_keywords, true);
            if (is_array($keywords)) {
                $validated['meta_keywords'] = implode(', ', array_column($keywords, 'value'));
            }
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
        if ($article->seo_image) {
            $oldSeoPath = str_replace('/storage/', 'public/', $article->seo_image);
            Storage::delete($oldSeoPath);
        }
        $article->delete();
        return back()->with('success', 'Artikel berhasil dihapus.');
    }
}
