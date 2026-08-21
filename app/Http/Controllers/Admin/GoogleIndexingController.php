<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\GoogleIndexingLog;
use Google\Client as GoogleClient;
use Google\Service\Indexing as GoogleIndexing;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GoogleIndexingController extends Controller
{
    public function uploadJson(Request $request)
    {
        $request->validate([
            'json_file' => 'required|file|mimetypes:application/json,text/plain|max:2048'
        ]);

        $file = $request->file('json_file');
        
        $file->storeAs('google', 'service-account.json', 'local');
        
        Setting::setValue('g_index_auth_mode', 'json');

        return redirect()->back()->with('success', 'File JSON Service Account berhasil diupload dan diaktifkan.');
    }

    public function oauthRedirect()
    {
        $client = $this->getGoogleClient();
        
        if (!$client) {
            return redirect()->back()->withErrors(['Kredensial Google Client ID dan Secret belum dikonfigurasi di tab Integrasi.']);
        }

        $authUrl = $client->createAuthUrl();
        return redirect($authUrl);
    }

    public function oauthCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('admin.settings.general')->withErrors(['Gagal login: ' . $request->error]);
        }

        if (!$request->has('code')) {
            return redirect()->route('admin.settings.general')->withErrors(['Kode autentikasi tidak ditemukan.']);
        }

        $client = $this->getGoogleClient();
        
        try {
            $token = $client->fetchAccessTokenWithAuthCode($request->code);
            
            if (isset($token['error'])) {
                return redirect()->route('admin.settings.general')->withErrors(['Error token: ' . $token['error_description']]);
            }

            Setting::setValue('g_index_access_token', json_encode($token));
            Setting::setValue('g_index_auth_mode', 'oauth');

            return redirect()->route('admin.settings.general')->with('success', 'Berhasil terhubung ke Google Search Console (OAuth).');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.general')->withErrors(['Gagal mengambil token: ' . $e->getMessage()]);
        }
    }

    public function submitUrls(Request $request)
    {
        $request->validate([
            'urls' => 'required|string',
            'action_type' => 'required|in:URL_UPDATED,URL_DELETED'
        ]);

        $quotaUsedToday = GoogleIndexingLog::whereDate('created_at', today())->count();
        if ($quotaUsedToday >= 200) {
            return response()->json([
                'success' => false,
                'message' => 'Kuota harian Google Indexing API (200 requests) telah habis untuk hari ini.'
            ]);
        }

        $mode = Setting::getValue('g_index_auth_mode', 'json');
        
        $client = new GoogleClient();
        
        if ($mode === 'json') {
            $jsonPath = storage_path('app/google/service-account.json');
            if (!file_exists($jsonPath)) {
                $jsonPath = base_path('wismaindo-504816-f6014a87c478.json');
            }

            if (!file_exists($jsonPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File JSON Service Account tidak ditemukan. Silakan upload terlebih dahulu.'
                ]);
            }
            $client->setAuthConfig($jsonPath);
            $client->addScope(GoogleIndexing::INDEXING);
        } else {
            $client = $this->getGoogleClient();
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google Client tidak terkonfigurasi dengan benar.'
                ]);
            }
            $tokenJson = Setting::getValue('g_index_access_token');
            if (!$tokenJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum login via OAuth.'
                ]);
            }
            
            $token = json_decode($tokenJson, true);
            $client->setAccessToken($token);
            
            if ($client->isAccessTokenExpired()) {
                if ($client->getRefreshToken()) {
                    $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                    Setting::setValue('g_index_access_token', json_encode($client->getAccessToken()));
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token OAuth expired dan Refresh Token tidak ditemukan. Silakan Login ulang.'
                    ]);
                }
            }
        }

        $indexingService = new GoogleIndexing($client);
        $client->setUseBatch(true);
        $batch = $indexingService->createBatch();

        $urlsRaw = explode("\n", str_replace("\r", "", $request->urls));
        $urls = array_filter(array_map('trim', $urlsRaw));
        
        if (count($urls) === 0) {
             return response()->json([
                'success' => false,
                'message' => 'Tidak ada URL valid yang diberikan.'
            ]);
        }

        if (count($urls) > 100) {
            $urls = array_slice($urls, 0, 100);
        }

        foreach ($urls as $index => $url) {
            $urlNotification = new \Google\Service\Indexing\UrlNotification();
            $urlNotification->setUrl($url);
            $urlNotification->setType($request->action_type);
            
            $requestObj = $indexingService->urlNotifications->publish($urlNotification);
            $batch->add($requestObj, 'req_' . $index);
        }

        try {
            $results = $batch->execute();
            
            $successCount = 0;
            $failCount = 0;
            
            foreach ($urls as $index => $url) {
                $responseId = 'response-req_' . $index;
                $res = $results[$responseId] ?? null;
                
                $status = 500;
                $msg = 'Unknown error';
                
                if ($res instanceof \Google\Service\Exception) {
                    $status = $res->getCode();
                    $msg = $res->getMessage();
                    $failCount++;
                } elseif ($res instanceof \Google\Service\Indexing\PublishUrlNotificationResponse) {
                    $status = 200;
                    $msg = 'Success';
                    $successCount++;
                }
                
                GoogleIndexingLog::create([
                    'url' => $url,
                    'action_type' => $request->action_type,
                    'status_code' => $status,
                    'response_message' => $msg
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Proses submit selesai. $successCount sukses, $failCount gagal."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    private function getGoogleClient()
    {
        $clientId = Setting::getValue('google_client_id');
        $clientSecret = Setting::getValue('google_client_secret');
        
        if (!$clientId || !$clientSecret) {
            return null;
        }

        $client = new GoogleClient();
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri(url('/auth/google-indexing/callback'));
        $client->addScope(GoogleIndexing::INDEXING);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        return $client;
    }
}
