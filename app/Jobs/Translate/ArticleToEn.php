<?php

namespace App\Jobs\Translate;

use App\Models\Article;
use App\Services\TranslateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Client\ConnectionException;

class ArticleToEn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $tries = 5;

    // penting: cegah "run too long" di shared hosting
    public int $timeout = 120;

    // jangan bikin langsung jadi failed kalau timeout sesaat
    public bool $failOnTimeout = false;

    // backoff bertahap biar gak spam tiap menit saat API down
    public array $backoff = [60, 300, 900, 1800, 3600];

    public function __construct(public int $id) {}

    public function handle(TranslateService $tx): void
    {
        $a = Article::find($this->id);
        if (!$a) return;
        try {
            $dirty = false;

            $textMap = [
                'title_en'   => [$a->title_en ?? null, $a->title ?? null],
                'excerpt_en' => [$a->excerpt_en ?? null, $a->excerpt ?? null],
                'seo_title_en' => [$a->seo_title_en ?? null, $a->seo_title ?? null],
                'seo_description_en' => [$a->seo_description_en ?? null, $a->seo_description ?? null],
            ];

            $keys = [];
            $inputs = [];
            foreach ($textMap as $enField => [$cur, $src]) {
                if ((!$cur || trim((string)$cur) === '') && is_string($src) && trim($src) !== '') {
                    $keys[] = $enField;
                    $inputs[] = $src;
                }
            }

            if ($inputs) {
                $out = $tx->toEnBatch($inputs, 'text');
                foreach ($keys as $i => $field) {
                    $val = $out[$i] ?? null;
                    if (is_string($val) && trim($val) !== '') {
                        $a->{$field} = $val;
                        $dirty = true;
                    }
                }
            }

            // content as HTML (quill)
            if ((!$a->content_en || trim((string)$a->content_en) === '')
                && is_string($a->content) && trim($a->content) !== ''
            ) {
                $val = $tx->toEnBatch([$a->content], 'html')[0] ?? null;
                if (is_string($val) && trim($val) !== '') {
                    $a->content_en = $val;
                    $dirty = true;
                }
            }

            if ($dirty) $a->save();
        } catch (ConnectionException $e) {
            $this->release(300);
            return;
        }
    }
}
