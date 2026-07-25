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

        // TOTAL REVENUE (Success)
        $totalRevenue = TopupTransaction::where('status', 'success')->sum('price');

        // VISITOR ANALYTICS
        $todayVisitors = \App\Models\Visitor::whereDate('date', today())->sum('hits');
        $weekVisitors = \App\Models\Visitor::whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->sum('hits');
        $totalVisitors = \App\Models\Visitor::sum('hits');
        
        // VISITOR CHART DATA (Last 7 Days)
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->translatedFormat('d M');
            $chartData[] = \App\Models\Visitor::whereDate('date', $date->format('Y-m-d'))->sum('hits');
        }

        // LATEST USERS (For Impersonate from Dashboard)
        $latestUsers = User::orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact(
            'totalListings',
            'activeListings',
            'totalUsers',
            'totalRevenue',
            'todayVisitors',
            'weekVisitors',
            'totalVisitors',
            'chartLabels',
            'chartData',
            'latestUsers'
        ));
    }
}
