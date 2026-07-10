<?php

namespace App\Jobs\Translate;

use App\Models\UmrahPackage;
use App\Services\TranslateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Client\ConnectionException;

class UmrahPackageToEn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $tries = 5;
    public int $timeout = 120;
    public bool $failOnTimeout = false;
    public array $backoff = [60, 300, 900, 1800, 3600];
    public function __construct(public int $id) {}

    public function handle(TranslateService $tx): void
    {
        $p = UmrahPackage::with('category')->find($this->id);
        if (!$p) return;
        try {
            $dirty = false;

            // 1) Batch TEXT fields
            $textMap = [
                'title_en'           => [$p->title_en ?? null, $p->title ?? null],
                'label_en'           => [$p->label_en ?? null, $p->label ?? null],
                'duration_text_en'   => [$p->duration_text_en ?? null, $p->duration_text ?? null],
                'seo_title_en'       => [$p->seo_title_en ?? null, $p->seo_title ?? null],
                'seo_description_en' => [$p->seo_description_en ?? null, $p->seo_description ?? null],
                'seo_keywords_en'    => [$p->seo_keywords_en ?? null, $p->seo_keywords ?? null],
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
                foreach ($keys as $i => $k) {
                    $p->{$k} = $out[$i] ?? null;
                    $dirty = true;
                }
            }

            // 2) Batch HTML fields (WYSIWYG)
            $htmlKeys = [];
            $htmlInputs = [];

            if ((!$p->long_description_en || trim((string)$p->long_description_en) === '') && is_string($p->long_description) && trim($p->long_description) !== '') {
                $htmlKeys[] = 'long_description_en';
                $htmlInputs[] = $p->long_description;
            }
            if ((!$p->itinerary_en || trim((string)$p->itinerary_en) === '') && is_string($p->itinerary) && trim($p->itinerary) !== '') {
                $htmlKeys[] = 'itinerary_en';
                $htmlInputs[] = $p->itinerary;
            }
            if ((!$p->include_text_en || trim((string)$p->include_text_en) === '') && is_string($p->include_text) && trim($p->include_text) !== '') {
                $htmlKeys[] = 'include_text_en';
                $htmlInputs[] = $p->include_text;
            }
            if ((!$p->exclude_text_en || trim((string)$p->exclude_text_en) === '') && is_string($p->exclude_text) && trim($p->exclude_text) !== '') {
                $htmlKeys[] = 'exclude_text_en';
                $htmlInputs[] = $p->exclude_text;
            }

            if ($htmlInputs) {
                $out = $tx->toEnBatch($htmlInputs, 'html');
                foreach ($htmlKeys as $i => $k) {
                    $p->{$k} = $out[$i] ?? null;
                    $dirty = true;
                }
            }

            if ($dirty) $p->save();
        } catch (ConnectionException $e) {
            $this->release(300);
            return;
        }
    }
}
