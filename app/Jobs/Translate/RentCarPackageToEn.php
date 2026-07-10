<?php

namespace App\Jobs\Translate;

use App\Models\RentCarPackage;
use App\Services\TranslateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Client\ConnectionException;


class RentCarPackageToEn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $tries = 5;
    public int $timeout = 120;
    public bool $failOnTimeout = false;
    public array $backoff = [60, 300, 900, 1800, 3600];

    public function __construct(public int $id) {}

    public function handle(TranslateService $tx): void
    {
        $p = RentCarPackage::find($this->id);
        if (!$p) return;
        try {
            $dirty = false;

            // 1) Batch TEXT fields
            $textMap = [
                'title_en'           => [$p->title_en, $p->title],
                'label_en'           => [$p->label_en, $p->label],
                'seo_title_en'       => [$p->seo_title_en, $p->seo_title],
                'seo_description_en' => [$p->seo_description_en, $p->seo_description],
                'seo_keywords_en'    => [$p->seo_keywords_en, $p->seo_keywords],
            ];

            $textKeys = [];
            $textInputs = [];

            foreach ($textMap as $enField => [$currentEn, $src]) {
                if (!$currentEn && is_string($src) && trim($src) !== '') {
                    $textKeys[] = $enField;
                    $textInputs[] = $src;
                }
            }

            if (count($textInputs)) {
                $textOut = $tx->toEnBatch($textInputs, 'text');
                foreach ($textKeys as $i => $field) {
                    $val = $textOut[$i] ?? null;
                    if (is_string($val) && trim($val) !== '') {
                        $p->{$field} = $val;
                        $dirty = true;
                    }
                }
            }

            // 2) HTML fields
            if (!$p->long_description_en && is_string($p->long_description) && trim($p->long_description) !== '') {
                $htmlOut = $tx->toEnBatch([$p->long_description], 'html');
                $val = $htmlOut[0] ?? null;
                if (is_string($val) && trim($val) !== '') {
                    $p->long_description_en = $val;
                    $dirty = true;
                }
            }

            // 3) FEATURES: array of objects [{name, available}]
            if (!$p->features_en && is_array($p->features) && count($p->features)) {
                $names = [];
                $idxMap = []; // original feature index => names index

                foreach ($p->features as $i => $feat) {
                    $name = is_array($feat) ? ($feat['name'] ?? '') : '';
                    $name = is_string($name) ? trim($name) : '';
                    if ($name === '') {
                        $idxMap[$i] = null;
                        continue;
                    }
                    $idxMap[$i] = count($names);
                    $names[] = $name;
                }

                if (count($names)) {
                    $out = $tx->toEnBatch($names, 'text');

                    $new = [];
                    foreach ($p->features as $i => $feat) {
                        $avail = is_array($feat) ? (bool)($feat['available'] ?? false) : false;

                        $name = is_array($feat) ? ($feat['name'] ?? '') : '';
                        $name = is_string($name) ? trim($name) : '';

                        if ($idxMap[$i] !== null) {
                            $tr = $out[$idxMap[$i]] ?? null;
                            $tr = is_string($tr) ? trim($tr) : '';
                            if ($tr !== '') $name = $tr;
                        }

                        $new[] = [
                            'name' => $name,
                            'available' => $avail,
                        ];
                    }

                    $p->features_en = $new;
                    $dirty = true;
                }
            }

            if ($dirty) {
                $p->save();
            }
        } catch (ConnectionException $e) {
            $this->release(300);
            return;
        }
    }
}
