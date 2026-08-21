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
        $banners = HomeBanner::orderBy('order', 'asc')->get()->map(function($banner) {
            $banner->image = $banner->image ? url($banner->image) : null;
            return $banner;
        });

        $locations = HomeLocation::orderBy('order', 'asc')->get()->map(function($loc) {
            $loc->image = $loc->image ? url($loc->image) : null;
            return $loc;
        });

        $testimonials = Testimonial::orderBy('order', 'asc')->get()->map(function($t) {
            $t->avatar = $t->avatar ? url($t->avatar) : null;
            return $t;
        });

        $bankPartners = BankPartner::orderBy('order', 'asc')->get()->map(function($b) {
            $b->logo = $b->logo ? url($b->logo) : null;
            return $b;
        });

        $allCategories = ListingCategory::get()->map(function($c) {
            $c->photo = $c->photo ? url($c->photo) : null;
            return $c;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'banners' => $banners,
                'locations' => $locations,
                'testimonials' => $testimonials,
                'bank_partners' => $bankPartners,
                'categories' => [
                    'property' => $allCategories->where('type', 'property')->values(),
                    'goods' => $allCategories->where('type', 'goods')->values(),
                    'services' => $allCategories->where('type', 'services')->values(),
                ],
            ]
        ]);
    }
}
