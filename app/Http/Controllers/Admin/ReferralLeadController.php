<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReferralLeadController extends Controller
{
    public function index()
    {
        $leads = \App\Models\BuyerLead::where('source', 'referral_admin')
                    ->with(['partner', 'listing'])
                    ->latest()
                    ->paginate(15);
                    
        return view('admin.referrals.index', compact('leads'));
    }

    public function create()
    {
        $partners = \App\Models\User::role('partner')->get();
        $listings = \App\Models\Listing::select('id', 'title', 'price', 'property_type')->latest()->get();
        return view('admin.referrals.create', compact('partners', 'listings'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'partner_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:25',
            'email' => 'nullable|email|max:255',
            'message' => 'nullable|string',
            'listing_id' => 'nullable|exists:listings,id'
        ]);

        $lead = \App\Models\BuyerLead::create([
            'partner_id' => $request->partner_id,
            'listing_id' => $request->listing_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'message' => $request->message,
            'source' => 'referral_admin',
            'status' => 'Lead Baru'
        ]);

        \App\Models\LeadActivity::create([
            'buyer_lead_id' => $lead->id,
            'status' => 'Lead Baru',
            'note' => 'Lead direkomendasikan oleh Admin WismaIndo.'
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($lead->partner->email)->send(new \App\Mail\ReferralLeadAssigned($lead));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send referral email: ' . $e->getMessage());
        }

        return redirect()->route('admin.referrals.index')->with('success', 'Referral Lead berhasil dibuat dan dikirim ke Partner!');
    }
}
