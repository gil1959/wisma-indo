<?php

namespace App\Http\Controllers\Api\V1\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BuyerLead;
use App\Models\LeadActivity;
use App\Http\Resources\BuyerLeadResource;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $partner = $request->user();
        
        $leads = BuyerLead::with(['listing.category'])
            ->where('partner_id', $partner->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return BuyerLeadResource::collection($leads)->additional([
            'success' => true
        ]);
    }

    public function show(Request $request, $id)
    {
        $partner = $request->user();
        $lead = BuyerLead::with(['listing.category', 'activities'])
            ->where('partner_id', $partner->id)
            ->findOrFail($id);
            
        return (new BuyerLeadResource($lead))->additional([
            'success' => true,
            'activities' => $lead->activities
        ]);
    }

    public function addActivity(Request $request, $id)
    {
        $request->validate([
            'activity_type' => 'required|string',
            'notes' => 'required|string'
        ]);

        $partner = $request->user();
        $lead = BuyerLead::where('partner_id', $partner->id)->findOrFail($id);

        $activity = new LeadActivity();
        $activity->buyer_lead_id = $lead->id;
        $activity->activity_type = $request->activity_type; // call, email, whatsapp, meeting
        $activity->notes = $request->notes;
        $activity->save();

        return response()->json([
            'success' => true,
            'message' => 'Activity added successfully',
            'data' => $activity
        ]);
    }
}
