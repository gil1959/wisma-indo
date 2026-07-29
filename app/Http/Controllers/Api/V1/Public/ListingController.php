<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Listing;
use App\Models\ListingCategory;
use App\Http\Resources\ListingResource;
use App\Http\Resources\ListingCategoryResource;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $query = Listing::with(['category', 'user', 'images'])
            ->where('is_active', true)
            ->where('status', 'approved');

        // Filters
        if ($request->has('category_slug')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category_slug);
            });
        }
        
        if ($request->has('listing_type')) {
            $query->where('listing_type', $request->listing_type); // dijual / disewakan
        }
        
        if ($request->has('type')) {
            $query->where('type', $request->type); // properti / barang_jasa
        }

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');
        
        // Prioritize sundul/premium
        $query->orderByRaw('is_sundul DESC, sundul_at DESC, is_premium DESC, premium_until DESC');
        $query->orderBy($sort, $order);

        $listings = $query->paginate(12);

        return ListingResource::collection($listings)->additional([
            'success' => true
        ]);
    }

    public function show($slug)
    {
        $listing = Listing::with(['category', 'user', 'images'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('status', 'approved')
            ->first();

        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'Listing not found'
            ], 404);
        }

        $listing->increment('views');

        return (new ListingResource($listing))->additional([
            'success' => true
        ]);
    }

    public function categories()
    {
        $categories = ListingCategory::where('is_active', true)->get();
        return ListingCategoryResource::collection($categories)->additional([
            'success' => true
        ]);
    }
}
