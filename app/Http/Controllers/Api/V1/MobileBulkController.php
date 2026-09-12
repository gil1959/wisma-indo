<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class MobileBulkController extends Controller {
    private function authorizeAccount(Request $r) {
        abort_unless($r->user()->hasAnyRole(['user','partner'])&&!$r->user()->hasAnyRole(['admin','site_moderator']),403);
        abort_unless($r->user()->hasVerifiedEmail(),403,'Verifikasi email sebelum mengunggah iklan.');
        abort_if(\App\Models\PartnerRegistration::where('user_id',$r->user()->id)->where('status','!=','approved')->exists(),403,'Pendaftaran partner belum disetujui.');
    }
    public function index(Request $r) {
        $this->authorizeAccount($r);
        return response()->json(['success'=>true,'data'=>['quota'=>(int)($r->user()->quota->listing_quota??0),'uploads'=>\App\Models\BulkUpload::where('user_id',$r->user()->id)->select('id','type','status','total_rows','processed_rows','failed_rows','error_log','created_at')->latest()->paginate(10)]]);
    }
    public function template(Request $r,string $type) { $this->authorizeAccount($r); return app(\App\Http\Controllers\User\BulkListingController::class)->downloadTemplate($type); }
    public function store(Request $r) {
        $this->authorizeAccount($r); $r->validate(['file'=>'required|file|max:20480','type'=>'required|in:property,goods,services']);
        $session=new \Illuminate\Session\Store('bulk-api',new \Illuminate\Session\ArraySessionHandler(120));$session->start();$r->setLaravelSession($session);app('redirect')->setSession($session);
        app(\App\Http\Controllers\User\BulkListingController::class)->store($r);
        $errors=$session->get('errors');
        return response()->json(['success'=>!$errors,'message'=>$errors?implode("\n",$errors->all()):$session->get('status','Unggahan diterima.')],$errors?422:200);
    }
}
