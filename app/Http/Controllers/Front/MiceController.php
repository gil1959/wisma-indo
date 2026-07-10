<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\MiceCategory;
use App\Models\MicePackage;
use Illuminate\Http\Request;

class MiceController extends Controller
{
    public function index(Request $request)
{
    $query = MicePackage::query()
        ->where('is_active', true)
        ->with(['category', 'tiers']); // penting biar min price di view gak N+1

    // FILTER CATEGORY: view ngirim ID, jadi filter pake category_id
    if ($request->filled('category')) {
        $categoryId = (int) $request->input('category');
        $query->where('category_id', $categoryId);
    }

    // SEARCH
    if ($request->filled('q')) {
        $q = trim((string) $request->input('q'));
        $query->where(function ($qq) use ($q) {
            $qq->where('title', 'like', "%{$q}%")
               ->orWhere('destination', 'like', "%{$q}%");
        });
    }

    // SORT (sesuai dropdown di view)
    $sort = (string) $request->input('sort', '');

    if ($sort === 'newest' || $sort === '') {
        $query->latest();
    } elseif ($sort === 'price_low' || $sort === 'price_high') {
        // ambil MIN harga domestic untuk sorting
        $query->withMin(['tiers as min_domestic_price' => function ($t) {
            $t->where('type', 'domestic');
        }], 'price');

        $query->orderBy('min_domestic_price', $sort === 'price_low' ? 'asc' : 'desc');
    } else {
        $query->latest();
    }

    $packages = $query->paginate(12)->withQueryString();
    $categories = MiceCategory::orderBy('name')->get();

    return view('front.mice.index', compact('packages', 'categories'));
}


    public function show(MicePackage $micePackage)
    {
        $package = $micePackage->load(['category', 'tiers', 'photos', 'reviews']);
        return view('front.mice.show', compact('package'));
    }
}
