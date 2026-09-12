<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{PartnerPackage,PartnerSubscription,OfflinePaymentMethod};

class NativePartnerBillingController extends Controller
{
    public function index(Request $request)
    {
        $subscriptions=PartnerSubscription::where('user_id',$request->user()->id)->with('package')->latest()->paginate(10);
        $availablePackages=PartnerPackage::where('is_free',false)->where('is_active',true)->get();
        $currentSubscription=PartnerSubscription::where('user_id',$request->user()->id)->where('status','active')->where('ends_at','>',now())->with('package')->latest()->first();
        return view('user.partner.billing',compact('subscriptions','availablePackages','currentSubscription'));
    }
    public function purchase(Request $request)
    {
        $request->validate(['package_id'=>'required|integer|exists:partner_packages,id','payment_method'=>'required|string']);
        $package=PartnerPackage::where('is_free',false)->where('is_active',true)->findOrFail($request->package_id);
        $available=self::paymentOptions();
        abort_unless(in_array($request->payment_method,array_column($available,'value')),422,'Metode pembayaran tidak tersedia.');
        return app(\App\Http\Controllers\User\PartnerSubscriptionController::class)->process($request,$package->id);
    }
    public function proof(Request $request,PartnerSubscription $subscription)
    {
        abort_unless($subscription->user_id===$request->user()->id,403);
        abort_unless($subscription->status==='pending' && $subscription->payment_method==='offline',422,'Transaksi ini tidak menerima unggahan bukti.');
        $request->validate(['payment_proof'=>'required|image|mimes:jpg,jpeg,png|max:2048']);
        return app(\App\Http\Controllers\User\PartnerSubscriptionController::class)->storeProof($request,$subscription);
    }
    public static function paymentOptions(): array
    {
        $options=OfflinePaymentMethod::where('is_active',true)->get()->map(function($m){return ['value'=>'offline|'.$m->id,'label'=>$m->name.' '.$m->account_number.' a.n. '.$m->account_name];})->all();
        $package=PartnerPackage::where('is_free',false)->where('is_active',true)->first();
        if($package) {
            $channels=\Illuminate\Support\Facades\Cache::remember('native-partner-payment-channels',60,function()use($package){
                return app(\App\Http\Controllers\User\PartnerSubscriptionController::class)->checkout($package->id)->getData()['pgChannels']??[];
            });
            foreach($channels as $channel) $options[]=['value'=>'pg|'.$channel['code'],'label'=>$channel['name']];
        }
        return $options;
    }
}
