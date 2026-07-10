<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ShipPackage;
use App\Models\ShipCategory;
use Illuminate\Http\Request;

class ShipController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $categoryId = $request->query('category_id');
        $sort = $request->query('sort', 'latest');

        $query = ShipPackage::query()
            ->with(['category', 'tiers'])
            ->where('is_active', 1);

        if ($q !== '') {
            $isEn = app()->getLocale() === 'en';

            $query->where(function ($qq) use ($q, $isEn) {
                $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('label', 'like', "%{$q}%");

                if ($isEn) {
                    $qq->orWhere('title_en', 'like', "%{$q}%")
                        ->orWhere('label_en', 'like', "%{$q}%");
                }
            });
        }


        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        // Sorting (karena ship tiers banyak, kita gak sort by price)
        switch ($sort) {
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $packages = $query->paginate(12)->appends($request->query());

        $categories = ShipCategory::orderBy('name')->get();

        return view('front.ships.index', compact('packages', 'categories', 'q', 'categoryId', 'sort'));
    }

    public function show($slug)
    {
        $package = ShipPackage::with('tiers')
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->firstOrFail();

        return view('front.ships.show', compact('package'));
    }
}
