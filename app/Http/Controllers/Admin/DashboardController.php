<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\TourPackage;
use App\Models\RentCarPackage;

class DashboardController extends Controller
{
    public function index()
    {
        // TOTAL PENDAPATAN
        $totalRevenue = Order::where('payment_status', 'paid')
            ->where('order_status', 'approved')
            ->sum('final_price');

        // TOTAL PESANAN (paid saja)
        $totalOrders = Order::where('payment_status', 'paid')->count();

        // PAKET AKTIF
        $activeTours = TourPackage::where('is_active', 1)->count();
        $activeRentCars = RentCarPackage::where('is_active', 1)->count();

        $totalPackages = $activeTours + $activeRentCars;

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalPackages'
        ));
    }
}
