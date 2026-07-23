<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::where('is_published', true)->latest();
        
        if ($request->has('category') && $request->category != '') {
            $category = ArticleCategory::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }
        
        if ($request->has('q') && $request->q != '') {
            $query->where('title', 'like', '%' . $request->q . '%');
        }

        $articles = $query->paginate(9)->withQueryString();
        $categories = ArticleCategory::orderBy('name')->get();
        $recentArticles = Article::where('is_published', true)->latest()->take(5)->get();

        return view('front.pages.articles', compact('articles', 'categories', 'recentArticles'));
    }

    public function show($slug)
    {
        $article = Article::where('slug', $slug)->where('is_published', true)->firstOrFail();
        
        // Update views count
        $article->increment('views');

        $relatedArticles = Article::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->where('is_published', true)
            ->latest()
            ->take(3)
            ->get();

        return view('front.pages.article_show', compact('article', 'relatedArticles'));
    }
}
