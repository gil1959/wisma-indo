<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DestinationInspiration;
use App\Models\TourCategory;
use Illuminate\Http\Request;

class DestinationInspirationController extends Controller
{
    public function index()
    {
        $items = DestinationInspiration::with('tourCategory')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();


        return view('admin.destination_inspirations.index', compact('items'));
    }

    public function create()
    {
        $categories = TourCategory::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.destination_inspirations.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'image' => ['required', 'image', 'max:2048'],
            'tour_category_id' => ['nullable', 'exists:tour_categories,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'tour_subcategory_id' => [
  'nullable',
  \Illuminate\Validation\Rule::exists('tour_categories','id')
    ->where(fn($q) => $q->where('parent_id', $request->tour_category_id)),
],
        ]);

        $data['is_active'] = (bool)($request->input('is_active', false));
        $data['sort_order'] = (int)($request->input('sort_order', 0));

        $path = $request->file('image')->store('inspirations', 'public');
        $data['image_path'] = $path;

        \App\Models\DestinationInspiration::create($data);

        return redirect()->route('admin.destination-inspirations.index')
            ->with('success', 'Inspirasi destinasi berhasil ditambahkan.');
    }

    public function edit(DestinationInspiration $destinationInspiration)
{
    $categories = TourCategory::whereNull('parent_id')->orderBy('name')->get();

    return view('admin.destination_inspirations.edit', [
        'item' => $destinationInspiration,
        'categories' => $categories,
    ]);
}


    public function update(Request $request, \App\Models\DestinationInspiration $destinationInspiration)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'image' => ['nullable', 'image', 'max:2048'],
            'tour_category_id' => ['nullable', 'exists:tour_categories,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'tour_subcategory_id' => [
  'nullable',
  \Illuminate\Validation\Rule::exists('tour_categories','id')
    ->where(fn($q) => $q->where('parent_id', $request->tour_category_id)),
],
        ]);

        $data['is_active'] = (bool)($request->input('is_active', false));
        $data['sort_order'] = (int)($request->input('sort_order', 0));

        if ($request->hasFile('image')) {
            if (!empty($destinationInspiration->image_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($destinationInspiration->image_path);
            }
            $data['image_path'] = $request->file('image')->store('inspirations', 'public');
        }

        $destinationInspiration->update($data);

        return redirect()->route('admin.destination-inspirations.index')
            ->with('success', 'Inspirasi destinasi berhasil diupdate.');
    }


    public function destroy(DestinationInspiration $destinationInspiration)
    {
        $destinationInspiration->delete();

        return redirect()->route('admin.destination-inspirations.index')
            ->with('success', 'Inspirasi destinasi berhasil dihapus.');
    }
}
