<?php

namespace App\Jobs\Translate;

use App\Models\TourPackage;
use App\Services\TranslateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Client\ConnectionException;

class TourPackageToEn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $tries = 5;
    public int $timeout = 120;
    public bool $failOnTimeout = false;
    public array $backoff = [60, 300, 900, 1800, 3600];
    public function __construct(public int $id) {}

    public function handle(TranslateService $tx): void
    {
        $p = TourPackage::with('itineraries')->find($this->id);
        if (!$p) return;
        try {
            $dirty = false;

            // 1) Batch TEXT fields dalam 1 request (sebisa mungkin)
            $textMap = [
                'title_en'          => [$p->title_en, $p->title],
                'label_en'          => [$p->label_en, $p->label],
                'destination_en'    => [$p->destination_en, $p->destination],
                'duration_text_en'  => [$p->duration_text_en, $p->duration_text],
                'seo_title_en'      => [$p->seo_title_en, $p->seo_title],
                'seo_description_en' => [$p->seo_description_en, $p->seo_description],
                'seo_keywords_en'   => [$p->seo_keywords_en, $p->seo_keywords],
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

            // 2) Batch HTML fields (gabung 2 field jadi 1 request html)
            $htmlKeys = [];
            $htmlInputs = [];

            if (!$p->long_description_en && is_string($p->long_description) && trim($p->long_description) !== '') {
                $htmlKeys[] = 'long_description_en';
                $htmlInputs[] = $p->long_description;
            }
            if (!$p->flight_info_en && is_string($p->flight_info) && trim($p->flight_info) !== '') {
                $htmlKeys[] = 'flight_info_en';
                $htmlInputs[] = $p->flight_info;
            }

            if (count($htmlInputs)) {
                $htmlOut = $tx->toEnBatch($htmlInputs, 'html');
                foreach ($htmlKeys as $i => $field) {
                    $val = $htmlOut[$i] ?? null;
                    if (is_string($val) && trim($val) !== '') {
                        $p->{$field} = $val;
                        $dirty = true;
                    }
                }
            }

            // 3) includes/excludes sudah batch di service, aman
            if (!$p->includes_en && is_array($p->includes) && count($p->includes)) {
                $p->includes_en = $tx->toEnArray($p->includes);
                $dirty = true;
            }
            if (!$p->excludes_en && is_array($p->excludes) && count($p->excludes)) {
                $p->excludes_en = $tx->toEnArray($p->excludes);
                $dirty = true;
            }

            if ($dirty) {
                $p->save();
            }

            // 4) Itineraries: batch translate semua title yang belum punya EN
            $needIts = [];
            $needItsIds = [];

            foreach ($p->itineraries as $it) {
                if (!$it->title_en && is_string($it->title) && trim($it->title) !== '') {
                    $needItsIds[] = $it->id;
                    $needIts[] = $it->title;
                }
            }

            if (count($needIts)) {
                $itOut = $tx->toEnBatch($needIts, 'text');
                foreach ($needItsIds as $i => $id) {
                    $val = $itOut[$i] ?? null;
                    if (is_string($val) && trim($val) !== '') {
                        $p->itineraries()->where('id', $id)->update(['title_en' => $val]);
                    }
                }
            }
        } catch (ConnectionException $e) {
            $this->release(300);
            return;
        }
    }
}
