<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages = \App\Models\Page::latest()->paginate(10);
        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages',
            'content' => 'required',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_desc' => 'nullable|string',
            'seo_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'meta_keywords' => 'nullable|string',
            'social_title' => 'nullable|string|max:255',
            'social_desc' => 'nullable|string',
        ]);

        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('seo_image')) {
            $seoPath = $request->file('seo_image')->store('public/pages/seo');
            $data['seo_image'] = \Illuminate\Support\Facades\Storage::url($seoPath);
        }

        if ($request->has('meta_keywords') && $request->meta_keywords) {
            $keywords = json_decode($request->meta_keywords, true);
            if (is_array($keywords)) {
                $data['meta_keywords'] = implode(', ', array_column($keywords, 'value'));
            } else {
                $data['meta_keywords'] = $request->meta_keywords;
            }
        }

        \App\Models\Page::create($data);

        return redirect()->route('admin.pages.index')->with('success', 'Halaman berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        $page = \App\Models\Page::findOrFail($id);
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        $page = \App\Models\Page::findOrFail($id);
        
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'required',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_desc' => 'nullable|string',
            'seo_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'meta_keywords' => 'nullable|string',
            'social_title' => 'nullable|string|max:255',
            'social_desc' => 'nullable|string',
        ]);

        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('seo_image')) {
            if ($page->seo_image) {
                $oldPath = str_replace('/storage/', 'public/', $page->seo_image);
                \Illuminate\Support\Facades\Storage::delete($oldPath);
            }
            $path = $request->file('seo_image')->store('public/pages/seo');
            $data['seo_image'] = \Illuminate\Support\Facades\Storage::url($path);
        }

        if ($request->has('meta_keywords') && $request->meta_keywords) {
            $keywords = json_decode($request->meta_keywords, true);
            if (is_array($keywords)) {
                $data['meta_keywords'] = implode(', ', array_column($keywords, 'value'));
            } else {
                $data['meta_keywords'] = $request->meta_keywords;
            }
        }

        $page->update($data);

        return redirect()->route('admin.pages.index')->with('success', 'Halaman berhasil diupdate.');
    }

    public function destroy(string $id)
    {
        $page = \App\Models\Page::findOrFail($id);
        $page->delete();
        
        return redirect()->route('admin.pages.index')->with('success', 'Halaman berhasil dihapus.');
    }
}
