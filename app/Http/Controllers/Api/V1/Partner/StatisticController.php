<?php

namespace App\Http\Controllers\Api\V1\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BuyerLead;
use App\Models\SurveySchedule;

class StatisticController extends Controller
{
    public function index(Request $request)
    {
        $partner = $request->user();
        
        $totalLeads = BuyerLead::where('partner_id', $partner->id)->count();
        $totalSurveys = SurveySchedule::where('partner_id', $partner->id)->count();
        $pendingSurveys = SurveySchedule::where('partner_id', $partner->id)
                            ->where('status', 'pending')->count();
                            
        // Recent activities could also be fetched here

        return response()->json([
            'success' => true,
            'data' => [
                'total_leads' => $totalLeads,
                'total_surveys' => $totalSurveys,
                'pending_surveys' => $pendingSurveys,
            ]
        ]);
    }
}
