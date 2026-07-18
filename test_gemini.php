<?php
$apiKey = \App\Models\Setting::where('key', 'gemini_api_key')->first()->value ?? null;
$response = \Illuminate\Support\Facades\Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key=" . $apiKey, [
    "contents" => [["parts" => [["text" => "Buat deskripsi rumah mewah minimal 4 paragraf"]]]],
    "generationConfig" => ["maxOutputTokens" => 4096]
]);
echo "Status: " . $response->status() . "\n";
if ($response->successful()) {
    echo $response->json()["candidates"][0]["content"]["parts"][0]["text"] ?? "Error parsing";
} else {
    echo json_encode($response->json(), JSON_PRETTY_PRINT);
}
