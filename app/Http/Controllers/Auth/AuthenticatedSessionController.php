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
        return redirect('/bw-admin');
    }

    // PARTNER → PARTNER DASHBOARD (JANGAN intended)
    if ($user && $user->hasRole('partner')) {
        return redirect('/partner/dashboard');
    }

    // USER → USER DASHBOARD (JANGAN intended)
    return redirect('/user/dashboard');
}


    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
