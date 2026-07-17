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
            ->latest()
            ->take(8)
            ->get();
            
        $goodsListings = \App\Models\Listing::with('listingCategory')
            ->where('status', 'tersedia')
            ->where('type', 'goods')
            ->latest()
            ->take(8)
            ->get();
            
        $servicesListings = \App\Models\Listing::with('listingCategory')
            ->where('status', 'tersedia')
            ->where('type', 'services')
            ->latest()
            ->take(8)
            ->get();

        return view('front.pages.home', compact('banners', 'buttons', 'locations', 'categories', 'propertyListings', 'goodsListings', 'servicesListings'));
    }
}
