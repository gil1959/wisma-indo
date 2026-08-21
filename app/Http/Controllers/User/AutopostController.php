<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\SocialAccount;
use App\Models\ScheduledAutopost;
use App\Models\Listing;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class AutopostController extends Controller
{
    public function authMeta()
    {
        $appId = Setting::getValue('meta_app_id');
        if (!$appId) {
            return redirect()->back()->with('error', 'Meta App ID belum dikonfigurasi oleh Admin.');
        }

        $redirectUri = url('/autopost/callback/meta');
        $scopes = 'pages_show_list,pages_read_engagement,pages_manage_posts,instagram_basic,instagram_content_publish';
        
        $url = "https://www.facebook.com/v19.0/dialog/oauth?client_id={$appId}&redirect_uri={$redirectUri}&scope={$scopes}&response_type=code";
        
        return redirect($url);
    }

    public function callbackMeta(Request $request)
    {
        $code = $request->query('code');
        if (!$code) {
            return redirect()->route('iklan.saya')->with('error', 'Otorisasi dibatalkan atau gagal.');
        }

        $appId = Setting::getValue('meta_app_id');
        $appSecret = Setting::getValue('meta_app_secret');
        $redirectUri = url('/autopost/callback/meta');

        try {
            // Tukar code dengan Access Token (User Token)
            $response = Http::get('https://graph.facebook.com/v19.0/oauth/access_token', [
                'client_id' => $appId,
                'redirect_uri' => $redirectUri,
                'client_secret' => $appSecret,
                'code' => $code,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $userAccessToken = $data['access_token'];

                // Dapatkan Page Access Token & Info Halaman
                $pagesResponse = Http::get('https://graph.facebook.com/v19.0/me/accounts', [
                    'access_token' => $userAccessToken
                ]);

                if ($pagesResponse->successful() && !empty($pagesResponse->json()['data'])) {
                    $pages = $pagesResponse->json()['data'];
                    // Ambil page pertama yang dikembalikan (Atau bisa dikembangkan untuk milih page)
                    $page = $pages[0]; 
                    
                    $pageToken = $page['access_token'];
                    $pageId = $page['id'];
                    $pageName = $page['name'];

                    // Coba dapatkan profil picture page
                    $picResponse = Http::get("https://graph.facebook.com/v19.0/{$pageId}/picture?redirect=0&access_token={$pageToken}");
                    $pagePicture = $picResponse->successful() ? $picResponse->json()['data']['url'] ?? null : null;

                    // Coba cari IG Business Account ID jika ada
                    $igResponse = Http::get("https://graph.facebook.com/v19.0/{$pageId}?fields=instagram_business_account&access_token={$pageToken}");
                    $igBusinessId = $igResponse->successful() ? $igResponse->json()['instagram_business_account']['id'] ?? null : null;

                    SocialAccount::updateOrCreate(
                        ['user_id' => Auth::id(), 'provider' => 'meta'],
                        [
                            'provider_id' => $pageId, // Simpan page ID
                            'access_token' => $pageToken, // Simpan page token
                            'page_name' => $pageName,
                            'page_picture' => $pagePicture,
                        ]
                    );

                    // Simpan IG Business ID terpisah jika ditemukan
                    if ($igBusinessId) {
                        SocialAccount::updateOrCreate(
                            ['user_id' => Auth::id(), 'provider' => 'ig_business'],
                            [
                                'provider_id' => $igBusinessId,
                                'access_token' => $userAccessToken, // IG Graph API sering pakai user token yg pnya izin
                                'page_name' => $pageName . ' (IG)',
                                'page_picture' => $pagePicture,
                            ]
                        );
                    }

                    return redirect()->route('iklan.saya')->with('success', 'Akun Meta (Facebook & Instagram) berhasil dihubungkan!');
                }
                
                return redirect()->route('iklan.saya')->with('error', 'Gagal mendapatkan akses Halaman Facebook (Page). Pastikan Anda memilih Halaman saat otorisasi.');
            }

            return redirect()->route('iklan.saya')->with('error', 'Gagal mendapatkan Access Token dari Meta.');
        } catch (\Exception $e) {
            return redirect()->route('iklan.saya')->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function generateCaption(Request $request)
    {
        $listingId = $request->input('listing_id');
        $listing = Listing::findOrFail($listingId);
        
        $apiKey = Setting::getValue('gemini_api_key');
        if (empty($apiKey)) {
            return response()->json(['success' => false, 'message' => 'Gemini API Key belum dikonfigurasi di Pengaturan.'], 400);
        }

        $title = $listing->title;
        $price = $listing->price;
        $details = strip_tags($listing->description);
        
        // Membatasi panjang detail agar tidak terlalu membengkak
        $details = substr($details, 0, 500); 

        $prompt = "Buatkan caption media sosial (Instagram/Facebook) yang sangat memikat untuk iklan properti berikut. Judul: '{$title}', Harga: Rp {$price}, Spesifikasi Singkat: '{$details}'. Gunakan gaya bahasa marketing persuasif. Boleh gunakan emoji secukupnya. Di bagian akhir, buatkan minimal 15 hashtag viral yang relevan (seperti #PropertiMurah #Investasi dll). Jangan gunakan format markdown (seperti ** atau *), tulis plain text dengan baris baru yang rapi.";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";
        
        try {
            $response = Http::post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $generatedText = $data['candidates'][0]['content']['parts'][0]['text'];
                    return response()->json(['success' => true, 'data' => trim($generatedText)]);
                }
            }
            
            return response()->json(['success' => false, 'message' => 'API Gemini tidak mengembalikan format yang valid.'], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function publish(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'caption' => 'required|string',
            'media_url' => 'required|url',
            'platforms' => 'required|array',
            'is_scheduled' => 'required|boolean',
            'scheduled_at' => 'nullable|date',
        ]);

        $listing = Listing::where('id', $request->listing_id)->where('user_id', Auth::id())->firstOrFail();

        $scheduledAt = $request->is_scheduled ? $request->scheduled_at : now();

        ScheduledAutopost::create([
            'user_id' => Auth::id(),
            'listing_id' => $listing->id,
            'platforms' => $request->platforms,
            'caption' => $request->caption,
            'media_url' => $request->media_url,
            'scheduled_at' => $scheduledAt,
            'status' => 'pending'
        ]);

        $msg = $request->is_scheduled ? 'Autopost berhasil dijadwalkan!' : 'Autopost sedang diproses di latar belakang!';
        return redirect()->back()->with('success', $msg);
    }

    public function logs()
    {
        $logs = ScheduledAutopost::where('user_id', Auth::id())
            ->with('listing')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('front.user.listings.autopost-logs', compact('logs'));
    }
}
