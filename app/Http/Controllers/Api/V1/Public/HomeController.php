<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomeBanner;
use App\Models\HomeLocation;
use App\Models\Testimonial;
use App\Models\BankPartner;
use App\Models\ListingCategory;

class HomeController extends Controller
{
    public function index()
    {
        $banners = HomeBanner::orderBy('order', 'asc')->get();
        $locations = HomeLocation::orderBy('order', 'asc')->get();
        $testimonials = Testimonial::orderBy('order', 'asc')->get();
        $bankPartners = BankPartner::orderBy('order', 'asc')->get();
        $categories = ListingCategory::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'banners' => $banners,
                'locations' => $locations,
                'testimonials' => $testimonials,
                'bank_partners' => $bankPartners,
                'categories' => $categories,
            ]
        ]);
    }
}
