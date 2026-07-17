<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            $user = $request->user();
            if ($user && ($user->hasRole('admin') || $user->hasRole('site_moderator'))) {
                return redirect('/admin?verified=1');
            }
            if ($user && $user->hasRole('partner')) {
                return redirect('/partner/dashboard?verified=1');
            }
            return redirect('/akun?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        $user = $request->user();

        if ($user && ($user->hasRole('admin') || $user->hasRole('site_moderator'))) {
            return redirect('/admin?verified=1');
        }

        if ($user && $user->hasRole('partner')) {
            return redirect('/partner/dashboard?verified=1');
        }

        return redirect('/akun?verified=1');
    }
}
