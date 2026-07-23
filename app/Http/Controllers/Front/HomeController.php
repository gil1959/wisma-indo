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
        $categories = \App\Models\ListingCategory::all()->groupBy('type');
        
        $propertyListings = \App\Models\Listing::with('listingCategory')
            ->where('status', 'tersedia')
            ->where('type', 'property')
            ->orderBy('is_premium', 'desc')
            ->orderBy('bump_count', 'desc')
            ->orderBy('bumped_at', 'desc')
            ->latest()
            ->take(18)
            ->get();

        $testimonials = \App\Models\Testimonial::where('is_active', true)->orderBy('order')->get();
        $bankPartners = \App\Models\BankPartner::where('is_active', true)->orderBy('order')->get();

        return view('front.pages.home', compact('banners', 'buttons', 'categories', 'propertyListings', 'testimonials', 'bankPartners'));
    }
}
