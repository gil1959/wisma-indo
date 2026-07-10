<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\TourCategory;
use Illuminate\Http\Request;

class TourCategoryController extends Controller
{
    private function guardTourPartner(): void
    {
        abort_unless(auth()->user()->partner_type === 'agency_paket_tour', 403);
    }

    public function index()
    {
        $this->guardTourPartner();

        $categories = TourCategory::query()
            ->where('created_by_partner_id', auth()->id())
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        return view('partner.tour-categories.index', compact('categories'));
    }

    public function create()
    {
        $this->guardTourPartner();

        $parents = TourCategory::query()
            ->where('created_by_partner_id', auth()->id())
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get(['id','name']);

        return view('partner.tour-categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $this->guardTourPartner();

        $request->validate([
            'name' => ['required','string','max:190'],
            'slug' => ['required','string','max:190','unique:tour_categories,slug'],
            'parent_id' => ['nullable','exists:tour_categories,id'],
        ]);

        // parent harus kategori milik partner sendiri
        if ($request->filled('parent_id')) {
            $ok = TourCategory::where('id', $request->parent_id)
                ->where('created_by_partner_id', auth()->id())
                ->exists();
            abort_unless($ok, 403);
        }

        TourCategory::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'parent_id' => $request->parent_id,
            'created_by_partner_id' => auth()->id(),
        ]);

        return redirect()->route('partner.tour-categories.index')
            ->with('success', 'Kategori tour berhasil dibuat.');
    }

    public function edit(TourCategory $tour_category)
    {
        $this->guardTourPartner();

        if ((int) $tour_category->created_by_partner_id !== (int) auth()->id()) {
    return redirect()
        ->route('partner.tour-categories.index')
        ->with('error', 'Kamu tidak punya akses untuk edit kategori ini.');
}

        $category = $tour_category;

        $parents = TourCategory::query()
            ->where('created_by_partner_id', auth()->id())
            ->whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get(['id','name']);

        return view('partner.tour-categories.edit', compact('category','parents'));
    }

    public function update(Request $request, TourCategory $tour_category)
    {
        $this->guardTourPartner();
        if ((int) $tour_category->created_by_partner_id !== (int) auth()->id()) {
    return redirect()
        ->route('partner.tour-categories.index')
        ->with('error', 'Kamu tidak punya akses untuk update kategori ini.');
}

        $request->validate([
            'name' => ['required','string','max:190'],
            'slug' => ['required','string','max:190','unique:tour_categories,slug,' . $tour_category->id],
            'parent_id' => ['nullable','exists:tour_categories,id'],
        ]);

        if ($request->filled('parent_id')) {
            $ok = TourCategory::where('id', $request->parent_id)
                ->where('created_by_partner_id', auth()->id())
                ->exists();
            if (!$ok) {
    return redirect()
        ->back()
        ->withInput()
        ->with('error', 'Parent kategori tidak valid (bukan milik kamu).');
}
        }

        $tour_category->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('partner.tour-categories.index')
            ->with('success', 'Kategori tour berhasil diperbarui.');
    }

    public function destroy(TourCategory $tour_category)
    {
        $this->guardTourPartner();
        if ((int) $tour_category->created_by_partner_id !== (int) auth()->id()) {
    return redirect()
        ->route('partner.tour-categories.index')
        ->with('error', 'Kamu tidak punya akses untuk menghapus kategori ini.');
}

        // kalau dipakai di paket -> block
        $isUsed = $tour_category->packages()->exists() || $tour_category->packagesAsSubcategory()->exists();
        if ($isUsed) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih digunakan paket.');
        }

        // kalau punya sub -> block
        if ($tour_category->children()->exists()) {
            return back()->with('error', 'Hapus subkategori dulu sebelum menghapus parent.');
        }

        $tour_category->delete();

        return redirect()->route('partner.tour-categories.index')
            ->with('success', 'Kategori tour berhasil dihapus.');
    }

    // tetap dipakai dropdown subkategori di form paket tour
   public function subcategories(TourCategory $category)
{
    $allowed = is_null($category->created_by_partner_id) // admin/global
        || (int)$category->created_by_partner_id === (int)auth()->id(); // partner sendiri

    abort_unless($allowed, 403);

    $items = $category->children()->get(['id', 'name']);

    return response()->json([
        'items' => $items,
    ]);
}

}
