<?php

namespace App\Services;

use App\Models\PushSubscription;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    protected function client(): WebPush
    {
        $auth = [
            'VAPID' => [
                'subject' => env('VAPID_SUBJECT'),
                'publicKey' => env('VAPID_PUBLIC_KEY'),
                'privateKey' => env('VAPID_PRIVATE_KEY'),
            ],
        ];

        return new WebPush($auth);
    }

    public function sendToUserId(int $userId, array $payload): void
    {
        $subs = PushSubscription::where('user_id', $userId)->get();
        if ($subs->isEmpty()) return;

        $webPush = $this->client();

        foreach ($subs as $s) {
            $subscription = Subscription::create([
                'endpoint' => $s->endpoint,
                'publicKey' => $s->public_key,
                'authToken' => $s->auth_token,
                'contentEncoding' => $s->content_encoding ?: 'aesgcm',
            ]);

            $webPush->queueNotification($subscription, json_encode($payload));
        }

        // Flush (send)
        foreach ($webPush->flush() as $report) {
            // kalau mau logging, taruh di sini.
            // contoh: if (!$report->isSuccess()) { ... }
        }
    }
}
