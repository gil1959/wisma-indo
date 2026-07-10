<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\HotelPackage;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $sort = $request->query('sort', 'latest');

        $query = HotelPackage::query()
            ->where('is_active', 1);

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%");
            });
        }

        if ($sort === 'price_asc') {
            $query->orderBy('price_per_night', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price_per_night', 'desc');
        } elseif ($sort === 'title_asc') {
            $query->orderBy('title', 'asc');
        } else {
            $query->latest();
        }

        $packages = $query->get();

        return view('front.hotel.index', compact('packages', 'q', 'sort'));
    }

    public function show($slug)
    {
        $package = HotelPackage::where('slug', $slug)
            ->where('is_active', 1)
            ->firstOrFail();

        return view('front.hotel.show', compact('package'));
    }
}
