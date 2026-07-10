<?php

namespace App\Jobs\Translate;

use App\Models\ShipPackage;
use App\Services\TranslateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Client\ConnectionException;


class ShipPackageToEn implements ShouldQueue
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
        $p = ShipPackage::with('category')->find($this->id);
        if (!$p) return;
        try {

            $dirty = false;

            // TEXT fields
            $textMap = [
                'title_en'         => [$p->title_en ?? null, $p->title ?? null],
                'label_en'         => [$p->label_en ?? null, $p->label ?? null],
                'duration_text_en' => [$p->duration_text_en ?? null, $p->duration_text ?? null],
                'seo_title_en'     => [$p->seo_title_en ?? null, $p->seo_title ?? null],
                'seo_description_en' => [$p->seo_description_en ?? null, $p->seo_description ?? null],
                'seo_keywords_en'  => [$p->seo_keywords_en ?? null, $p->seo_keywords ?? null],
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
                        $p->{$field} = $val;
                        $dirty = true;
                    }
                }
            }

            // HTML field
            if ((!$p->long_description_en || trim((string)$p->long_description_en) === '')
                && is_string($p->long_description) && trim($p->long_description) !== ''
            ) {
                $val = $tx->toEnBatch([$p->long_description], 'html')[0] ?? null;
                if (is_string($val) && trim($val) !== '') {
                    $p->long_description_en = $val;
                    $dirty = true;
                }
            }

            // Category name_en (optional but makes home cards consistent)
            if (
                $p->category && (!($p->category->name_en ?? null) || trim((string)$p->category->name_en) === '')
                && is_string($p->category->name) && trim($p->category->name) !== ''
            ) {
                $val = $tx->toEnBatch([$p->category->name], 'text')[0] ?? null;
                if (is_string($val) && trim($val) !== '') {
                    $p->category->name_en = $val;
                    $p->category->save();
                }
            }

            if ($dirty) $p->save();
        } catch (ConnectionException $e) {
            $this->release(300);
            return;
        }
    }
}
