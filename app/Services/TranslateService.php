<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;


class TranslateService
{
    public function toEnText(?string $text): ?string
    {
        $text = is_string($text) ? trim($text) : '';
        if ($text === '') return null;

        $driver = config('translate.driver', 'deepl');

        return match ($driver) {
            'libretranslate' => $this->libreTranslate($text, 'text'),
            'deepl'          => $this->deeplTranslate($text, 'text'),
            default          => $text, // fallback aman
        };
    }

    public function toEnHtml(?string $html): ?string
    {
        $html = is_string($html) ? trim($html) : '';
        if ($html === '') return null;

        $driver = config('translate.driver', 'deepl');

        // konsisten: normalize dulu sebelum translate HTML
        $html = $this->normalizeQuillHtml($html);

        return match ($driver) {
            'libretranslate' => $this->libreTranslate($html, 'html'),
            'deepl'          => $this->deeplTranslate($html, 'html'),
            default          => $html,
        };
    }

    /**
     * Translate array string (includes/excludes, dll).
     * Return array yang sudah ditranslate, item kosong dibuang.
     */
    public function toEnArray($items): array
    {
        if (!is_array($items)) return [];

        $items = array_values(array_filter(array_map(function ($v) {
            $s = is_string($v) ? trim($v) : '';
            return $s !== '' ? $s : null;
        }, $items)));

        if (!count($items)) return [];

        // pakai batch biar hemat request dan cepat
        $out = $this->toEnBatch($items, 'text');

        // bersihin null/empty
        return array_values(array_filter(array_map(function ($v) {
            $s = is_string($v) ? trim($v) : '';
            return $s !== '' ? $s : null;
        }, $out)));
    }

    /**
     * Translate batch items.
     * Output array ukurannya SAMA dengan input.
     * - item kosong/null => output null di index yang sama
     */
    public function toEnBatch(array $items, string $mode = 'text'): array
    {
        $driver = config('translate.driver', 'deepl');

        return match ($driver) {
            'libretranslate' => $this->libreTranslateBatch($items, $mode),
            'deepl'          => $this->deeplBatch($items, $mode),
            default          => $items,
        };
    }

    /**
     * DeepL batch translate (legacy).
     */
    private function deeplBatch(array $items, string $mode = 'text'): array
    {
        $key = config('translate.deepl.key');
        if (!$key) throw new \RuntimeException('DEEPL_API_KEY belum diset.');

        $endpoint = config('translate.deepl.endpoint');
        if (!$endpoint) throw new \RuntimeException('DEEPL_ENDPOINT belum diset.');

        $texts = [];
        $map = []; // original index => compact index in $texts

        foreach ($items as $i => $v) {
            $s = is_string($v) ? trim($v) : '';
            if ($s === '') {
                $map[$i] = null;
                continue;
            }
            $map[$i] = count($texts);
            $texts[] = $s;
        }

        if (!count($texts)) {
            return array_map(fn() => null, $items);
        }

        // Build body manual biar jadi: text=...&text=... (tanpa text[0])
        $parts = [];
        $parts[] = 'source_lang=ID';
        $parts[] = 'target_lang=EN';

        if ($mode === 'html') {
            $parts[] = 'tag_handling=html';
        }

        foreach ($texts as $t) {
            if ($mode === 'html') {
                $t = $this->normalizeQuillHtml($t);
            }
            $parts[] = 'text=' . rawurlencode($t);
        }

        $body = implode('&', $parts);

        $resp = Http::timeout(25)
            ->withHeaders([
                'Authorization' => 'DeepL-Auth-Key ' . $key,
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ])
            ->withBody($body, 'application/x-www-form-urlencoded')
            ->post($endpoint);

        if (!$resp->ok()) {
            throw new \RuntimeException('DeepL error: ' . $resp->status() . ' ' . $resp->body());
        }

        $data = $resp->json();
        $trs = $data['translations'] ?? null;

        if (!is_array($trs) || !count($trs)) {
            throw new \RuntimeException('DeepL response kosong.');
        }

        $outTexts = [];
        foreach ($trs as $t) {
            $outTexts[] = is_array($t) ? ($t['text'] ?? null) : null;
        }

        // rebuild output sesuai index asli
        $result = [];
        foreach ($items as $i => $_) {
            if ($map[$i] === null) {
                $result[$i] = null;
            } else {
                $result[$i] = $outTexts[$map[$i]] ?? null;
            }
        }

        return $result;
    }

    private function normalizeQuillHtml(string $html): string
    {
        // Align classes -> inline style (lebih stabil setelah translate)
        $replacements = [
            'ql-align-center'  => 'text-align:center;',
            'ql-align-right'   => 'text-align:right;',
            'ql-align-justify' => 'text-align:justify;',
        ];

        foreach ($replacements as $class => $style) {
            $html = preg_replace_callback(
                '/<(p|div|h1|h2|h3|li)([^>]*\bclass="[^"]*\b' . preg_quote($class, '/') . '\b[^"]*"[^>]*)>/i',
                function ($m) use ($style) {
                    $tag = $m[1];
                    $attrs = $m[2];

                    if (preg_match('/\bstyle="([^"]*)"/i', $attrs, $sm)) {
                        $newStyle = rtrim($sm[1], ';') . ';' . $style;
                        $attrs = preg_replace('/\bstyle="[^"]*"/i', 'style="' . $newStyle . '"', $attrs);
                    } else {
                        $attrs .= ' style="' . $style . '"';
                    }
                    return '<' . $tag . $attrs . '>';
                },
                $html
            );
        }

        // Indent classes -> padding-left
        $html = preg_replace_callback(
            '/<(p|div|li)([^>]*\bclass="[^"]*\bql-indent-(\d+)\b[^"]*"[^>]*)>/i',
            function ($m) {
                $tag = $m[1];
                $attrs = $m[2];
                $level = (int)$m[3];
                $pad = (2 * $level) . 'em';

                if (preg_match('/\bstyle="([^"]*)"/i', $attrs, $sm)) {
                    $newStyle = rtrim($sm[1], ';') . ';padding-left:' . $pad . ';';
                    $attrs = preg_replace('/\bstyle="[^"]*"/i', 'style="' . $newStyle . '"', $attrs);
                } else {
                    $attrs .= ' style="padding-left:' . $pad . ';"';
                }
                return '<' . $tag . $attrs . '>';
            },
            $html
        );

        return $html;
    }

    private function deeplTranslate(string $text, string $mode): string
    {
        $key = config('translate.deepl.key');
        if (!$key) throw new \RuntimeException('DEEPL_API_KEY belum diset.');

        $endpoint = config('translate.deepl.endpoint');
        if (!$endpoint) throw new \RuntimeException('DEEPL_ENDPOINT belum diset.');

        $payload = [
            'text'        => $text,
            'source_lang' => 'ID',
            'target_lang' => 'EN',
        ];

        if ($mode === 'html') {
            $payload['tag_handling'] = 'html';
        }

        $resp = Http::timeout(25)
            ->withHeaders([
                'Authorization' => 'DeepL-Auth-Key ' . $key,
            ])
            ->asForm()
            ->post($endpoint, $payload);

        if (!$resp->ok()) {
            throw new \RuntimeException('DeepL error: ' . $resp->status() . ' ' . $resp->body());
        }

        $data = $resp->json();
        $out = $data['translations'][0]['text'] ?? null;

        if (!is_string($out) || trim($out) === '') {
            throw new \RuntimeException('DeepL response kosong.');
        }

        return $out;
    }

    private function libreTranslate(string $text, string $format = 'text'): string
    {
        $baseUrl = rtrim((string)config('translate.libretranslate.url'), '/');
        if ($baseUrl === '') throw new \RuntimeException('LIBRETRANSLATE_URL belum diset.');

        $endpoint = $baseUrl . '/translate';
        $apiKey = (string)config('translate.libretranslate.key');

        $payload = [
            'q'      => $text,
            'source' => 'id',
            'target' => 'en',
            'format' => $format === 'html' ? 'html' : 'text',
        ];

        // api_key opsional (self-hosted sering kosong)
        if (trim($apiKey) !== '') {
            $payload['api_key'] = $apiKey;
        }

        $timeout = (int) config('translate.libretranslate.timeout', 25);
        $connectTimeout = (int) config('translate.libretranslate.connect_timeout', 5);
        $retry = (int) config('translate.libretranslate.retry', 2);
        $retrySleep = (int) config('translate.libretranslate.retry_sleep_ms', 500);

        $resp = Http::connectTimeout($connectTimeout)
            ->timeout($timeout)
            ->retry($retry, $retrySleep, function ($exception) {
                return $exception instanceof ConnectionException;
            })
            ->asJson()
            ->post($endpoint, $payload);


        if (!$resp->ok()) {
            throw new \RuntimeException('LibreTranslate error: ' . $resp->status() . ' ' . $resp->body());
        }

        $data = $resp->json();
        $out = $data['translatedText'] ?? null;

        if (!is_string($out) || trim($out) === '') {
            throw new \RuntimeException('LibreTranslate response kosong.');
        }

        return $out;
    }

    private function libreTranslateBatch(array $items, string $mode = 'text'): array
    {
        $baseUrl = rtrim((string)config('translate.libretranslate.url'), '/');
        if ($baseUrl === '') throw new \RuntimeException('LIBRETRANSLATE_URL belum diset.');

        $endpoint = $baseUrl . '/translate';
        $apiKey = (string)config('translate.libretranslate.key');

        $texts = [];
        $map = []; // original index => compact index in $texts

        foreach ($items as $i => $v) {
            $s = is_string($v) ? trim($v) : '';
            if ($s === '') {
                $map[$i] = null;
                continue;
            }
            if ($mode === 'html') {
                $s = $this->normalizeQuillHtml($s);
            }
            $map[$i] = count($texts);
            $texts[] = $s;
        }

        if (!count($texts)) {
            return array_map(fn() => null, $items);
        }

        // LibreTranslate dukung q sebagai array dan translatedText balik sebagai array. :contentReference[oaicite:2]{index=2}
        $payload = [
            'q'      => $texts,
            'source' => 'id',
            'target' => 'en',
            'format' => $mode === 'html' ? 'html' : 'text',
        ];

        if (trim($apiKey) !== '') {
            $payload['api_key'] = $apiKey;
        }

        $timeout = (int) config('translate.libretranslate.timeout', 25);
        $connectTimeout = (int) config('translate.libretranslate.connect_timeout', 5);
        $retry = (int) config('translate.libretranslate.retry', 2);
        $retrySleep = (int) config('translate.libretranslate.retry_sleep_ms', 500);

        $resp = Http::withOptions([
            'connect_timeout' => $connectTimeout,
        ])
            ->timeout($timeout)
            ->retry($retry, $retrySleep, function ($exception) {
                return $exception instanceof ConnectionException;
            })
            ->asJson()
            ->post($endpoint, $payload);



        if (!$resp->ok()) {
            throw new \RuntimeException('LibreTranslate error: ' . $resp->status() . ' ' . $resp->body());
        }

        $data = $resp->json();
        $translated = $data['translatedText'] ?? null;

        if (!is_array($translated) || !count($translated)) {
            throw new \RuntimeException('LibreTranslate response kosong.');
        }

        // rebuild output sesuai index asli
        $result = [];
        foreach ($items as $i => $_) {
            if ($map[$i] === null) {
                $result[$i] = null;
            } else {
                $result[$i] = $translated[$map[$i]] ?? null;
            }
        }

        return $result;
    }
}
