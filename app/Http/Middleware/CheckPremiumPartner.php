<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPremiumPartner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $user->hasRole('partner')) {
            // Cek apakah punya langganan yang sedang aktif dan bukan paket free (is_free = 0)
            $hasPremium = \App\Models\PartnerSubscription::where('user_id', $user->id)
                            ->whereHas('package', function($query) {
                                $query->where('is_free', 0);
                            })
                            ->where('status', 'active')
                            ->where('ends_at', '>', now())
                            ->exists();
                            
            if (!$hasPremium) {
                // Redirect jika tidak punya akses premium
                return redirect()->route('partner.billing')->with('error', 'Silakan berlangganan Paket Partner untuk membuka fitur ini.');
            }
        }

        return $next($request);
    }
}
