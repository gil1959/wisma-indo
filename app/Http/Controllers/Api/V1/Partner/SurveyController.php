<?php

namespace App\Http\Controllers\Api\V1\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SurveySchedule;
use App\Http\Resources\SurveyScheduleResource;

class SurveyController extends Controller
{
    public function index(Request $request)
    {
        $partner = $request->user();
        
        $surveys = SurveySchedule::with(['listing.category'])
            ->where('partner_id', $partner->id)
            ->orderBy('survey_date', 'asc')
            ->paginate(15);
            
        return SurveyScheduleResource::collection($surveys)->additional([
            'success' => true
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        $partner = $request->user();
        $survey = SurveySchedule::where('partner_id', $partner->id)->findOrFail($id);

        $survey->status = $request->status;
        if ($request->has('notes')) {
            $survey->notes = $request->notes;
        }
        $survey->save();

        return response()->json([
            'success' => true,
            'message' => 'Survey status updated successfully',
            'data' => new SurveyScheduleResource($survey)
        ]);
    }
}
