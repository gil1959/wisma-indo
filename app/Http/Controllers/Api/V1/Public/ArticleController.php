<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Http\Resources\ArticleResource;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['category', 'author'])
            ->where('status', 'published');

        if ($request->has('category_slug')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category_slug);
            });
        }

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(10);

        return ArticleResource::collection($articles)->additional([
            'success' => true
        ]);
    }

    public function show($slug)
    {
        $article = Article::with(['category', 'author'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found'
            ], 404);
        }

        $article->increment('views');

        return (new ArticleResource($article))->additional([
            'success' => true
        ]);
    }

    public function categories()
    {
        $categories = ArticleCategory::all();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
