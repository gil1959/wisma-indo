<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;

class AiController extends Controller
{
    public function generate(Request $request)
    {
        $apiKey = Setting::where('key', 'gemini_api_key')->first()->value ?? null;
        if (empty($apiKey)) {
            return response()->json(['success' => false, 'message' => 'Gemini API Key belum dikonfigurasi di Pengaturan.'], 400);
        }

        $type = $request->input('type', 'listing'); // 'listing' or 'article'
        $title = $request->input('title');
        
        if (empty($title)) {
            return response()->json(['success' => false, 'message' => 'Judul harus diisi untuk generate deskripsi.'], 400);
        }

        if ($type === 'article') {
            $category = $request->input('category', '');
            $prompt = "Buat konten artikel blog yang informatif, menarik, sangat detail dan panjang, serta SEO-friendly dalam bahasa Indonesia. Judul artikel: '{$title}'. Kategori: '{$category}'. Tulis langsung isi kontennya dalam format HTML. Setiap paragraf WAJIB dibungkus dengan tag `<p style=\"text-align: justify;\">`. Jangan tambahkan ucapan salam atau kalimat pengantar, langsung ke isi artikel.";
        } else {
            $category = $request->input('category', '');
            $price = $request->input('price', '');
            $transactionType = $request->input('transaction_type', '');
            $location = $request->input('location', '');
            
            // Additional details
            $details = [];
            if ($request->filled('land_area')) $details[] = "Luas Tanah: " . $request->input('land_area') . " m2";
            if ($request->filled('building_area')) $details[] = "Luas Bangunan: " . $request->input('building_area') . " m2";
            if ($request->filled('bedrooms')) $details[] = "Kamar Tidur: " . $request->input('bedrooms');
            if ($request->filled('bathrooms')) $details[] = "Kamar Mandi: " . $request->input('bathrooms');
            if ($request->filled('certificate')) $details[] = "Sertifikat: " . $request->input('certificate');
            if ($request->filled('furnished_status')) $details[] = "Kondisi Perabotan: " . $request->input('furnished_status');
            
            if ($request->filled('facilities') && is_array($request->input('facilities'))) {
                $details[] = "Fasilitas: " . implode(", ", $request->input('facilities'));
            }
            if ($request->filled('surroundings') && is_array($request->input('surroundings'))) {
                $details[] = "Area Sekitar: " . implode(", ", $request->input('surroundings'));
            }
            if ($request->filled('condition')) $details[] = "Kondisi: " . $request->input('condition');
            if ($request->filled('brand')) $details[] = "Merek: " . $request->input('brand');
            if ($request->filled('service_area')) $details[] = "Area Layanan: " . $request->input('service_area');
            
            $detailsText = count($details) > 0 ? "Spesifikasi Detail:\n- " . implode("\n- ", $details) : "";

            $prompt = "Buat deskripsi iklan yang profesional, sangat menarik, memikat pembeli, dan sangat detail (panjang) dalam bahasa Indonesia untuk platform e-commerce/properti. 
Judul iklan: '{$title}'
Kategori: '{$category}'
Harga: '{$price}'
Transaksi: '{$transactionType}'
Lokasi: '{$location}'
{$detailsText}

Tulis langsung isi deskripsinya (tanpa salam/pengantar) dengan gaya bahasa marketing yang persuasif. 
Tulis minimal 4-5 paragraf yang komprehensif, jabarkan seluruh spesifikasi, fasilitas, dan area sekitar di atas menjadi kalimat cerita atau bullet point yang rapi. 
WAJIB tulis dalam format HTML di mana SETIAP paragraf dibungkus dengan tag `<p style=\"text-align: justify;\">`. Jangan hanya 1-2 kalimat pendek.";
        }

        $models = [
            'gemini-3.5-flash', 
            'gemini-3.1-flash-lite', 
            'gemini-3-flash', 
            'gemini-2.5-flash-lite', 
            'gemini-2.5-flash'
        ];
        $generatedText = null;
        $errorMsg = 'Gagal memanggil API AI.';

        foreach ($models as $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
            
            try {
                $response = Http::post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 4096,
                    ]
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                        $generatedText = $data['candidates'][0]['content']['parts'][0]['text'];
                        break; // Berhasil, keluar dari loop fallback
                    }
                } else {
                    $errorMsg = $response->json()['error']['message'] ?? "Error dari model {$model}";
                    \Illuminate\Support\Facades\Log::warning("Gemini AI ($model) failed: " . json_encode($response->json()));
                }
            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();
                \Illuminate\Support\Facades\Log::warning("Gemini AI Exception ($model): " . $e->getMessage());
            }
        }

        if ($generatedText) {
            // Hilangkan markdown block jika ada
            $generatedText = preg_replace('/```(?:html)?\s*(.*?)\s*```/is', '$1', $generatedText);
            
            return response()->json([
                'success' => true,
                'data' => trim($generatedText)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal generate konten dengan AI: ' . $errorMsg], 500);
    }
}
