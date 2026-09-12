<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class EnsureMobilePartnerSubscription
{
    public function handle(Request $request,Closure $next)
    {
        if($request->user() && $request->user()->hasRole('partner')) {
            $active=\App\Models\PartnerSubscription::where('user_id',$request->user()->id)->where('status','active')->where('ends_at','>',now())->whereHas('package',function($q){$q->where('is_free',false);})->exists();
            if(!$active) return response()->json(['success'=>false,'message'=>'Silakan berlangganan Paket Partner untuk membuka fitur ini.','code'=>'partner_subscription_required'],403);
        }
        return $next($request);
    }
}
