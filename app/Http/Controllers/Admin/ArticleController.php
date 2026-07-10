<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Jobs\Translate\ArticleToEn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        $items = Article::latest()->paginate(15);
        return view('admin.articles.index', compact('items'));
    }

    public function create()
    {
        return view('admin.articles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required',
            'cover_image' => 'nullable|image|max:2048',
            'is_published' => 'boolean',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:500',

            // adsense code bisa panjang
            'ads_code' => 'nullable|string',

            // input tags dari form: string CSV (nanti diolah)
            'tags' => 'nullable|string|max:2000',
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')
                ->store('articles/covers', 'public');
        }

        $data['slug'] = Str::slug($data['title']);
        $data['user_id'] = Auth::id();
        $data['tags'] = $this->normalizeTags($request->input('tags'));


        $article = Article::create($data);

        ArticleToEn::dispatch($article->id)
            ->onQueue('translations')
            ->afterCommit();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dibuat');
    }

    public function edit(Article $article)
    {
        return view('admin.articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required',
            'cover_image' => 'nullable|image|max:2048',
            'is_published' => 'nullable|boolean',

            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:500',

            'ads_code' => 'nullable|string',
            'tags' => 'nullable|string|max:2000',
        ]);

        // penting: paksa boolean biar gak jadi NULL
        $data['is_published'] = $request->boolean('is_published');

        // penting: normalisasi tags harus SELALU jalan
        $data['tags'] = $this->normalizeTags($request->input('tags'));

        if ($request->hasFile('cover_image')) {
            if ($article->cover_image) {
                Storage::disk('public')->delete($article->cover_image);
            }

            $data['cover_image'] = $request->file('cover_image')
                ->store('articles/covers', 'public');
        }

        $article->update($data);

        ArticleToEn::dispatch($article->id)
            ->onQueue('translations')
            ->afterCommit();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel diperbarui');
    }


    public function destroy(Article $article)
    {
        if ($article->cover_image) {
            Storage::disk('public')->delete($article->cover_image);
        }

        $article->delete();

        return back()->with('success', 'Artikel dihapus');
    }
    private function normalizeTags(?string $raw): ?array
    {
        if (!$raw) return null;

        $tags = collect(explode(',', $raw))
            ->map(fn($t) => trim($t))
            ->filter()
            ->map(function ($t) {
                // rapihin spasi dobel di tengah
                $t = preg_replace('/\s+/', ' ', $t);
                return $t;
            })
            ->unique()
            ->take(20)
            ->values()
            ->all();

        return count($tags) ? $tags : null;
    }
}
