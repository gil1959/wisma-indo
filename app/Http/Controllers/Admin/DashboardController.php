<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\User;
use App\Models\TopupTransaction;

class DashboardController extends Controller
{
    public function index()
    {
        // TOTAL LISTINGS
        $totalListings = Listing::count();

        // TOTAL ACTIVE LISTINGS
        $activeListings = Listing::where('status', 'active')->count();

        // TOTAL USERS
        $totalUsers = User::count();

        // TOTAL TOPUP REVENUE (Success)
        $totalRevenue = TopupTransaction::where('status', 'success')->sum('price');

        return view('admin.dashboard', compact(
            'totalListings',
            'activeListings',
            'totalUsers',
            'totalRevenue'
        ));
    }
}
