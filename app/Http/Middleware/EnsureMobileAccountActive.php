<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class EnsureMobileAccountActive
{
    public function handle(Request $request,Closure $next)
    {
        $user=$request->user();
        if($user && (($user->is_suspended && !$user->suspended_until) || ($user->suspended_until && $user->suspended_until->isFuture()))) {
            return response()->json(['success'=>false,'message'=>'Akun sedang ditangguhkan. Hubungi admin.'],403);
        }
        return $next($request);
    }
}
