<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
{
    $user = Auth::user();

    if ($user->is_suspended) {
        abort(403, 'Akun kamu sedang disuspend. Hubungi admin.');
    }

    $partnerId = $user->id;

    $tourIds = \App\Models\TourPackage::where('created_by_partner_id', $partnerId)->pluck('id');
    $rentIds = \App\Models\RentCarPackage::where('created_by_partner_id', $partnerId)->pluck('id');
    $shipIds = \App\Models\ShipPackage::where('created_by_partner_id', $partnerId)->pluck('id');

    $ordersQuery = \App\Models\Order::query()
        ->where(function ($q) use ($tourIds, $rentIds, $shipIds) {
            $q->where(fn($w) => $w->where('type','tour')->whereIn('product_id', $tourIds))
              ->orWhere(fn($w) => $w->where('type','rent_car')->whereIn('product_id', $rentIds))
              ->orWhere(fn($w) => $w->where('type','ship')->whereIn('product_id', $shipIds));
        });

    $totalOrders = (clone $ordersQuery)->count();
    $pendingOrders = (clone $ordersQuery)->where('order_status', 'pending')->count();

    // revenue hanya yang paid + approved (biar gak bohong)
    $totalRevenue = (clone $ordersQuery)
        ->where('payment_status', 'paid')
        ->where('order_status', 'approved')
        ->sum('final_price');

    $monthStart = now()->startOfMonth();
    $monthRevenue = (clone $ordersQuery)
        ->where('payment_status', 'paid')
        ->where('order_status', 'approved')
        ->where('created_at', '>=', $monthStart)
        ->sum('final_price');

    // active packages
    $activeTour = \App\Models\TourPackage::where('created_by_partner_id', $partnerId)->where('is_active', 1)->count();
    $activeRent = \App\Models\RentCarPackage::where('created_by_partner_id', $partnerId)->where('is_active', 1)->count();
    $activeShip = \App\Models\ShipPackage::where('created_by_partner_id', $partnerId)->where('is_active', 1)->count();
    $activePackages = $activeTour + $activeRent + $activeShip;

    $recentOrders = (clone $ordersQuery)->latest()->limit(8)->get();

    // ===== Wallet balances (net, sudah potong pajak) =====
$availableBalance = (int) ($user->partner_balance_available ?? 0);
$pendingBalance   = (int) ($user->partner_balance_pending ?? 0);
$withdrawnBalance = (int) ($user->partner_balance_withdrawn ?? 0);
$taxPercent       = (float) ($user->partner_tax_percent ?? 0);

// ===== Withdraw requests summary (kalau fitur sudah lu tambah) =====
$pendingWithdrawRequests = 0;
$recentWithdrawRequests = collect();

if (class_exists(\App\Models\PartnerWithdrawalRequest::class)) {
    $pendingWithdrawRequests = \App\Models\PartnerWithdrawalRequest::query()
        ->where('partner_id', $partnerId)
        ->where('status', 'pending')
        ->count();

    $recentWithdrawRequests = \App\Models\PartnerWithdrawalRequest::query()
        ->where('partner_id', $partnerId)
        ->latest()
        ->limit(5)
        ->get();
}

return view('partner.dashboard', compact(
    'user',
    'totalOrders',
    'pendingOrders',
    'totalRevenue',
    'monthRevenue',
    'activePackages',
    'activeTour',
    'activeRent',
    'activeShip',
    'recentOrders',

    // new
    'availableBalance',
    'pendingBalance',
    'withdrawnBalance',
    'taxPercent',
    'pendingWithdrawRequests',
    'recentWithdrawRequests'
));

}

}
