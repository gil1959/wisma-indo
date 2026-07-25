<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BuyerLead;
use App\Models\SurveySchedule;
use App\Models\PartnerSubscription;
use App\Models\Listing;
use Illuminate\Support\Facades\Auth;

class PartnerController extends Controller
{
    public function leads()
    {
        $leads = BuyerLead::where('partner_id', Auth::id())->with('listing')->latest()->paginate(15);
        return view('user.partner.leads', compact('leads'));
    }

    public function showLead($id)
    {
        $lead = BuyerLead::where('partner_id', Auth::id())->with(['listing', 'activities'])->findOrFail($id);
        return view('user.partner.lead_detail', compact('lead'));
    }

    public function addLeadActivity(Request $request, $id)
    {
        $lead = BuyerLead::where('partner_id', Auth::id())->findOrFail($id);
        $request->validate([
            'status' => 'required|string',
            'note' => 'nullable|string'
        ]);

        $lead->status = $request->status;
        $lead->save();

        \App\Models\LeadActivity::create([
            'buyer_lead_id' => $lead->id,
            'status' => $request->status,
            'note' => $request->note
        ]);

        return back()->with('success', 'Aktivitas lead berhasil ditambahkan dan status diperbarui.');
    }

    public function surveys()
    {
        $surveys = SurveySchedule::where('partner_id', Auth::id())->with('listing')->latest()->paginate(15);
        return view('user.partner.surveys', compact('surveys'));
    }

    public function updateSurveyStatus(Request $request, $id)
    {
        $survey = SurveySchedule::where('partner_id', Auth::id())->findOrFail($id);
        $request->validate(['status' => 'required|in:pending,confirmed,completed,cancelled']);
        $survey->status = $request->status;
        $survey->save();
        return back()->with('success', 'Status jadwal survey berhasil diupdate.');
    }

    public function statistics()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        $totalListings = \App\Models\Listing::where('user_id', $user->id)->count();
        $totalViews = \App\Models\Listing::where('user_id', $user->id)->sum('views');
        
        $totalLeads = \App\Models\BuyerLead::where('partner_id', $user->id)->count();
        $closedLeads = \App\Models\BuyerLead::where('partner_id', $user->id)->where('status', 'Closing')->count();
        
        $topListings = \App\Models\Listing::where('user_id', $user->id)
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();
            
        // Generate chart data for this month (by week)
        $now = \Carbon\Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $leadsThisMonth = \App\Models\BuyerLead::where('partner_id', $user->id)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();
            
        $surveysThisMonth = \App\Models\SurveySchedule::where('partner_id', $user->id)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();

        $chartData = [
            'leads' => [0, 0, 0, 0],
            'surveys' => [0, 0, 0, 0],
        ];

        foreach ($leadsThisMonth as $lead) {
            $week = ceil($lead->created_at->day / 7) - 1;
            if ($week > 3) $week = 3;
            $chartData['leads'][$week]++;
        }

        foreach ($surveysThisMonth as $survey) {
            $week = ceil($survey->created_at->day / 7) - 1;
            if ($week > 3) $week = 3;
            $chartData['surveys'][$week]++;
        }
            
        return view('user.partner.statistics', compact('totalListings', 'totalViews', 'totalLeads', 'closedLeads', 'topListings', 'chartData'));
    }

    public function billing()
    {
        $subscriptions = PartnerSubscription::where('user_id', Auth::id())->with('package')->latest()->paginate(10);
        $currentSubscription = PartnerSubscription::where('user_id', Auth::id())->where('status', 'active')->latest()->first();
        $availablePackages = \App\Models\PartnerPackage::where('is_free', false)->orderBy('price')->get();
        return view('user.partner.billing', compact('subscriptions', 'currentSubscription', 'availablePackages'));
    }

    public function whatsapp()
    {
        $user = Auth::user();
        return view('user.partner.whatsapp', compact('user'));
    }

    public function updateWhatsapp(Request $request)
    {
        $request->validate([
            'whatsapp_template' => 'nullable|string'
        ]);

        $user = Auth::user();
        $user->whatsapp_template = $request->whatsapp_template;
        $user->save();

        return back()->with('success', 'Template WhatsApp berhasil disimpan.');
    }
}
