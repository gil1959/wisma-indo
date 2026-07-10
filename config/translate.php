<?php

return [
    'driver' => env('TRANSLATE_DRIVER', 'deepl'),

    'deepl' => [
        'key' => env('DEEPL_API_KEY'),
        'endpoint' => env('DEEPL_ENDPOINT', 'https://api-free.deepl.com/v2/translate'),
    ],

    'libretranslate' => [
        // contoh: https://translate.dailyplanner.cloud
        'url' => env('LIBRETRANSLATE_URL', 'http://localhost:5000'),
        // optional (kalau server lu pakai key)
        'key' => env('LIBRETRANSLATE_API_KEY'),

        // request timeouts
        'timeout' => (int) env('LIBRETRANSLATE_TIMEOUT', 25),
        'connect_timeout' => (int) env('LIBRETRANSLATE_CONNECT_TIMEOUT', 5),

        // retry khusus error jaringan (bukan error 4xx/5xx)
        'retry' => (int) env('LIBRETRANSLATE_RETRY', 2),
        'retry_sleep_ms' => (int) env('LIBRETRANSLATE_RETRY_SLEEP_MS', 500),
    ],

];
