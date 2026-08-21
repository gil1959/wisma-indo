<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ScheduledAutopost;
use App\Models\SocialAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessAutopostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $posts = ScheduledAutopost::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($posts as $post) {
            $post->update(['status' => 'processing']);
            $platforms = $post->platforms;
            $successCount = 0;
            $errors = [];

            foreach ($platforms as $platform) {
                if ($platform === 'fb_page') {
                    $account = SocialAccount::where('user_id', $post->user_id)->where('provider', 'meta')->first();
                    if ($account) {
                        $response = Http::post("https://graph.facebook.com/v19.0/{$account->provider_id}/photos", [
                            'url' => $post->media_url,
                            'message' => $post->caption,
                            'access_token' => $account->access_token
                        ]);

                        if (!$response->successful()) {
                            $errors[] = "FB Page Error: " . $response->body();
                        } else {
                            $successCount++;
                        }
                    } else {
                        $errors[] = "FB Page Account not found.";
                    }
                } elseif ($platform === 'ig_business') {
                    $account = SocialAccount::where('user_id', $post->user_id)->where('provider', 'ig_business')->first();
                    if ($account) {
                        // IG API uses two-step upload
                        $uploadResponse = Http::post("https://graph.facebook.com/v19.0/{$account->provider_id}/media", [
                            'image_url' => $post->media_url,
                            'caption' => $post->caption,
                            'access_token' => $account->access_token
                        ]);

                        if ($uploadResponse->successful()) {
                            $creationId = $uploadResponse->json()['id'];
                            $publishResponse = Http::post("https://graph.facebook.com/v19.0/{$account->provider_id}/media_publish", [
                                'creation_id' => $creationId,
                                'access_token' => $account->access_token
                            ]);
                            if (!$publishResponse->successful()) {
                                $errors[] = "IG Publish Error: " . $publishResponse->body();
                            } else {
                                $successCount++;
                            }
                        } else {
                            $errors[] = "IG Upload Error: " . $uploadResponse->body();
                        }
                    } else {
                        $errors[] = "IG Business Account not found.";
                    }
                } elseif ($platform === 'threads') {
                    // Threads integration placeholder since official API is restricted/in-dev
                    $errors[] = "Threads integration is currently pending API access.";
                }
            }

            if (empty($errors)) {
                $post->update(['status' => 'success', 'error_message' => null]);
            } else {
                $post->update([
                    'status' => $successCount > 0 ? 'success' : 'failed', // Partial success or full failure
                    'error_message' => implode(' | ', $errors)
                ]);
                Log::error('Autopost failed for post ID ' . $post->id . ': ' . implode(' | ', $errors));
            }
        }
    }
}
