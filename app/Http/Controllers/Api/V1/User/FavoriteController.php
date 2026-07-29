<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FavoriteListing;
use App\Models\Listing;
use App\Http\Resources\ListingResource;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $favorites = FavoriteListing::with('listing.category')
            ->where('user_id', $user->id)
            ->get()
            ->pluck('listing');
            
        return ListingResource::collection($favorites)->additional([
            'success' => true
        ]);
    }

    public function store(Request $request, $listing_id)
    {
        $listing = Listing::findOrFail($listing_id);
        $user = $request->user();

        FavoriteListing::firstOrCreate([
            'user_id' => $user->id,
            'listing_id' => $listing->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Listing added to favorites'
        ]);
    }

    public function destroy(Request $request, $listing_id)
    {
        $user = $request->user();
        FavoriteListing::where('user_id', $user->id)
            ->where('listing_id', $listing_id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Listing removed from favorites'
        ]);
    }
}
