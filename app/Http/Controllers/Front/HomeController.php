<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $banners = \App\Models\HomeBanner::where('is_active', true)->orderBy('order')->get();
        $buttons = \App\Models\HomeButton::where('is_active', true)->orderBy('order')->get();
        $locations = \App\Models\HomeLocation::where('is_active', true)->orderBy('order')->get();
        $categories = \App\Models\ListingCategory::all()->groupBy('type');
        
        $propertyListings = \App\Models\Listing::with('listingCategory')
            ->where('status', 'tersedia')
            ->where('type', 'property')
            ->orderBy('is_premium', 'desc')
            ->orderBy('bump_count', 'desc')
            ->orderBy('bumped_at', 'desc')
            ->latest()
            ->take(8)
            ->get();
            
        $goodsListings = \App\Models\Listing::with('listingCategory')
            ->where('status', 'tersedia')
            ->where('type', 'goods')
            ->orderBy('is_premium', 'desc')
            ->orderBy('bump_count', 'desc')
            ->orderBy('bumped_at', 'desc')
            ->latest()
            ->take(8)
            ->get();
            
        $servicesListings = \App\Models\Listing::with('listingCategory')
            ->where('status', 'tersedia')
            ->where('type', 'services')
            ->orderBy('is_premium', 'desc')
            ->orderBy('bump_count', 'desc')
            ->orderBy('bumped_at', 'desc')
            ->latest()
            ->take(8)
            ->get();

        return view('front.pages.home', compact('banners', 'buttons', 'locations', 'categories', 'propertyListings', 'goodsListings', 'servicesListings'));
    }
}
