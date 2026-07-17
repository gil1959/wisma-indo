<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = \App\Models\FavoriteListing::with('listing')
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->latest()
            ->paginate(12);

        return view('front.user.favorites.index', compact('favorites'));
    }

    public function store(\App\Models\Listing $listing)
    {
        \App\Models\FavoriteListing::firstOrCreate([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'listing_id' => $listing->id
        ]);

        return response()->json(['success' => true, 'message' => 'Iklan ditambahkan ke favorit']);
    }

    public function destroy(\App\Models\Listing $listing)
    {
        \App\Models\FavoriteListing::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->where('listing_id', $listing->id)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Iklan dihapus dari favorit']);
    }
}
