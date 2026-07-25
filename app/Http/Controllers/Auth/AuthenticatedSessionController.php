<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request)
{
    $request->authenticate();
    $request->session()->regenerate();

    $user = Auth::user();

    // IMPORTANT:
    // intended URL sering nyangkut ke /user/dashboard dari session lama
    // jadi kita bersihin biar redirect role ga ketimpa.
    $request->session()->forget('url.intended');

    // ADMIN / SITE MODERATOR → ADMIN PANEL
    if ($user && ($user->hasRole('admin') || $user->hasRole('site_moderator'))) {
        return redirect()->route('admin.dashboard');
    }

    // PARTNER -> DASHBOARD / INTERCEPT
    if ($user) {
        $partnerReg = \App\Models\PartnerRegistration::where('user_id', $user->id)->first();
        if ($partnerReg && $partnerReg->status === 'pending') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('partner.register.success')->with('registered_user', [
                'name' => $user->name,
                'email' => $user->email,
            ]);
        }
    }

    // PARTNER -> DASHBOARD
    if ($user && $user->hasRole('partner')) {
        return redirect()->route('partner.statistics');
    }

    // USER → DASHBOARD (JANGAN intended)
    return redirect('/akun');
}


    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
