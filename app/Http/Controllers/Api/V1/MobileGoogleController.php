<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\{User,Setting,PartnerRegistration};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Cache,DB,Hash};
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
class MobileGoogleController extends Controller {
    private function enabled() { abort_unless(Setting::getValue('google_login_active','0')==='1',403,'Login Google tidak aktif.'); }
    public function options() { return response()->json(['success'=>true,'data'=>['google_enabled'=>Setting::getValue('google_login_active','0')==='1']]); }
    public function start(Request $request) {
        $this->enabled(); $request->validate(['challenge'=>'required|regex:/^[a-f0-9]{64}$/']);
        $state='mobile_'.Str::random(64); Cache::put('google-mobile:'.hash('sha256',$state),$request->challenge,now()->addMinutes(10));
        return response()->json(['success'=>true,'data'=>['url'=>Socialite::driver('google')->stateless()->with(['state'=>$state])->redirect()->getTargetUrl()]]);
    }
    public function callback(Request $request) {
        try {
            $this->enabled();
            $challenge=Cache::pull('google-mobile:'.hash('sha256',(string)$request->state)); abort_unless($challenge,403);
            $google=Socialite::driver('google')->stateless()->user();
            abort_unless($google->getEmail() && (($google->user['email_verified']??$google->user['verified_email']??false) === true),403);
            $user=DB::transaction(function()use($google){
                $user=User::where('email',$google->getEmail())->lockForUpdate()->first();
                if(!$user) {
                    $user=User::create(['name'=>$google->getName()?:'Pengguna','email'=>$google->getEmail(),'google_id'=>$google->getId(),'password'=>Hash::make(Str::random(40)),'email_verified_at'=>now(),'avatar'=>$google->getAvatar()]);
                    $user->assignRole('user'); $free=Setting::getValue('free_quota_register_enabled','1')==='1';
                    $user->quota()->create(['listing_quota'=>$free?1:0,'has_free_quota'=>$free]);
                }
                return $user;
            });
            $this->eligible($user);
            $code=Str::random(64); Cache::put('google-exchange:'.hash('sha256',$code),['id'=>$user->id,'challenge'=>$challenge],now()->addMinutes(2));
            return redirect()->away('wismaindo://google-login?code='.$code);
        } catch (\Throwable $e) { return redirect()->away('wismaindo://google-login?error=Login%20Google%20belum%20berhasil.%20Periksa%20status%20akun%20atau%20coba%20lagi.'); }
    }
    private function eligible(User $user) {
        abort_if(($user->is_suspended&&!$user->suspended_until)||($user->suspended_until&&$user->suspended_until->isFuture()),403,'Akun ditangguhkan.');
        if(!$user->hasAnyRole(['admin','site_moderator'])) abort_if(PartnerRegistration::where('user_id',$user->id)->where('status','!=','approved')->exists(),403,'Pendaftaran partner belum disetujui.');
    }
    public function exchange(Request $request) {
        $this->enabled(); $request->validate(['code'=>'required|string|max:100','verifier'=>'required|string|min:32|max:128']);
        $key='google-exchange:'.hash('sha256',$request->code);
        return Cache::lock($key.':lock',10)->block(3,function()use($request,$key){
            $data=Cache::get($key); abort_unless($data&&hash_equals($data['challenge'],hash('sha256',$request->verifier)),403,'Sesi Google tidak valid atau kedaluwarsa.');
            Cache::forget($key); $user=User::findOrFail($data['id']); $this->eligible($user);
            if(!$user->hasVerifiedEmail()) $user->forceFill(['email_verified_at'=>now()])->save();
            return response()->json(['success'=>true,'data'=>['access_token'=>$user->createToken('android')->plainTextToken,'user'=>new \App\Http\Resources\UserResource($user),'token_type'=>'Bearer']]);
        });
    }
}
