<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;
class NativeGoogleIndexingController extends Controller
{
    private function client()
    {
        $id=Setting::getValue('google_client_id');$secret=Setting::getValue('google_client_secret');
        abort_unless($id && $secret,422,'Isi Google Client ID dan Secret pada Pengaturan Umum terlebih dahulu.');
        $client=new \Google\Client();$client->setClientId($id);$client->setClientSecret($secret);
        $client->setRedirectUri(url('/api/v1/auth/indexing-callback'));
        $client->addScope(\Google\Service\Indexing::INDEXING);$client->setAccessType('offline');$client->setPrompt('consent');
        return $client;
    }
    public function connect(Request $request)
    {
        abort_unless($request->user()->hasAnyRole(['admin','site_moderator']),403);
        $client=$this->client();$state=\Illuminate\Support\Str::random(64);
        Cache::put('native-indexing:'.hash('sha256',$state),$request->user()->id,now()->addMinutes(10));
        $client->setState($state);
        return response()->json(['success'=>true,'data'=>['authorization_url'=>$client->createAuthUrl()]]);
    }
    public function callback(Request $request)
    {
        $request->validate(['state'=>'required|string|max:128']);
        $id=Cache::pull('native-indexing:'.hash('sha256',$request->state));
        $user=$id?\App\Models\User::find($id):null;
        abort_unless($user && $user->hasAnyRole(['admin','site_moderator']),403,'Sesi koneksi tidak valid atau kedaluwarsa.');
        if($request->filled('error')) return $this->landing('Koneksi Google dibatalkan.');
        $request->validate(['code'=>'required|string']);
        try {
            $token=$this->client()->fetchAccessTokenWithAuthCode($request->code);
            if(isset($token['error'])) return $this->landing('Google belum dapat dihubungkan. Periksa konfigurasi lalu coba kembali.');
            Setting::setValue('g_index_access_token',json_encode($token));Setting::setValue('g_index_auth_mode','oauth');
            return $this->landing('Google Indexing berhasil dihubungkan.');
        } catch(\Throwable $e) { return $this->landing('Google belum dapat dihubungkan. Coba kembali.'); }
    }
    private function landing(string $message)
    {
        return response('<!doctype html><html lang="id"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Google Indexing</title><body style="font-family:sans-serif;padding:24px"><p>'.e($message).'</p><a href="wismaindo://indexing-linked">Kembali ke WismaIndo</a></body></html>')->header('Cache-Control','no-store');
    }
}
