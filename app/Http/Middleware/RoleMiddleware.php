<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles)
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        $allowed = array_map('trim', explode('|', $roles));

        if (!in_array($user->role ?? null, $allowed, true)) {
            abort(403);
        }

        return $next($request);
    }
}
