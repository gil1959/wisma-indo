<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Listing;
use App\Models\TopupTransaction;
use App\Models\PartnerRegistration;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => User::count(),
                'total_listings' => Listing::count(),
                'pending_listings' => Listing::where('status', 'pending')->count(),
                'pending_topups' => TopupTransaction::where('status', 'waiting_verification')->count(),
                'pending_partner_registrations' => PartnerRegistration::where('status', 'pending')->count(),
            ]
        ]);
    }
}
