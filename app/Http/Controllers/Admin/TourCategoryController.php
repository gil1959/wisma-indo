<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourCategory;
use Illuminate\Http\Request;

class TourCategoryController extends Controller
{
    public function index()
    {
        $categories = TourCategory::query()
    ->whereNull('parent_id')
    ->with('children')
    ->orderBy('name')
    ->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
{
    $parents = TourCategory::query()
        ->whereNull('parent_id')
        ->orderBy('name')
        ->get(['id','name']);

    return view('admin.categories.create', compact('parents'));
}


    public function store(Request $request)
    {
        $request->validate([
  'name' => ['required','string','max:190'],
  'slug' => ['required','string','max:190','unique:tour_categories,slug'],
  'parent_id' => ['nullable','exists:tour_categories,id'],
]);

TourCategory::create($request->only('name','slug','parent_id'));


        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dibuat.');
    }

    public function edit(TourCategory $category)
{
    $parents = TourCategory::query()
        ->whereNull('parent_id')
        ->where('id', '!=', $category->id) // cegah dia jadi parent dirinya sendiri
        ->orderBy('name')
        ->get(['id','name']);

    return view('admin.categories.edit', compact('category', 'parents'));
}


    public function update(Request $request, TourCategory $category)
{
    $data = $request->validate([
        'name' => ['required', 'string', 'max:190'],
        'slug' => ['required', 'string', 'max:190', 'unique:tour_categories,slug,' . $category->id],
        'parent_id' => ['nullable', 'exists:tour_categories,id'],
    ]);

    // Guard: cegah category jadi parent dirinya sendiri
    if (!empty($data['parent_id']) && (int)$data['parent_id'] === (int)$category->id) {
        return back()
            ->withErrors(['parent_id' => 'Parent kategori tidak boleh dirinya sendiri.'])
            ->withInput();
    }

    $category->update([
        'name'      => $data['name'],
        'slug'      => $data['slug'],
        'parent_id' => $data['parent_id'] ?? null,
    ]);

    return redirect()
        ->route('admin.categories.index')
        ->with('success', 'Kategori berhasil diperbarui.');
}


    public function destroy(TourCategory $category)
    {
        // opsional: cek apakah masih dipakai tour package
        if ($category->packages()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan paket wisata.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
    public function subcategories(TourCategory $category)
{
    $items = $category->children()->get(['id','name']);
    return response()->json(['items' => $items]);
}

}
