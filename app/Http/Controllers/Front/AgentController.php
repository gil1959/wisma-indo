<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function show($slug)
    {
        $user = \App\Models\User::where('slug', $slug)->firstOrFail();
        
        // Include user's listings
        $listings = \App\Models\Listing::where('user_id', $user->id)
            ->where('status', 'tersedia')
            ->latest()
            ->paginate(12);
        
        return view('front.agent.show', compact('user', 'listings'));
    }
}
