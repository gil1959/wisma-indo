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
        $query = Listing::with(['listingCategory', 'user', 'images'])
            ->where('is_active', true)
            ->where('status', 'tersedia');

        // Filters
        if ($request->has('category_slug')) {
            $query->whereHas('listingCategory', function($q) use ($request) {
                $q->where('slug', $request->category_slug);
            });
        }
        // Jenis Transaksi
        if ($request->has('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }
        
        // Lokasi / Daerah
        if ($request->has('location')) {
            $query->where(function($q) use ($request) {
                $q->where('location', 'like', '%' . $request->location . '%')
                  ->orWhere('address', 'like', '%' . $request->location . '%');
            });
        }
        
        // Rentang Harga
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        if ($request->has('type')) {
            if ($request->type === 'barang_jasa') {
                $query->whereIn('type', ['goods', 'services']);
            } else {
                $query->where('type', $request->type);
            }
        }

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');
        
        // Prioritize sundul/premium
        $query->orderByRaw('is_premium DESC, bump_count DESC, bumped_at DESC');
        $query->orderBy($sort, $order);

        $listings = $query->paginate(12);

        return ListingResource::collection($listings)->additional([
            'success' => true
        ]);
    }

    public function show($slug)
    {
        $listing = Listing::with(['listingCategory', 'user', 'images'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('status', 'tersedia')
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
