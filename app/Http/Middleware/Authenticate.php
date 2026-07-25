<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    public function handle($request, Closure $next, ...$guards)
    {
        $response = parent::handle($request, $next, ...$guards);

        if (Auth::check()) {
            $user = Auth::user();
            if ($user->is_suspended && $user->suspended_until && $user->suspended_until > now()) {
                $allowedRoutes = ['partner.statistics', 'akun', 'logout'];
                if (!in_array($request->route()->getName(), $allowedRoutes)) {
                    $route = $user->hasRole('partner') ? 'partner.statistics' : 'akun';
                    return redirect()->route($route)->with('error', 'Akun Anda sedang disuspend hingga ' . $user->suspended_until->format('d M Y H:i'). '. Anda tidak dapat menggunakan fitur lainnya.');
                }
            }
        }

        return $response;
    }
}
