<?php

namespace App\Console\Commands\Translate;

use Illuminate\Console\Command;
use App\Models\TourPackage;
use App\Models\RentCarPackage;
use App\Models\ShipPackage;
use App\Models\UmrahPackage;
use App\Models\MicePackage;
use App\Models\Article;
use App\Models\DestinationInspiration;
use App\Models\Setting;
use App\Services\TranslateService;

class BackfillEn extends Command
{
    protected $signature = 'translate:backfill-en {--type=all}';
    protected $description = 'Queue DeepL translation jobs for existing records missing *_en fields';

    public function handle(): int
    {
        $type = strtolower((string)$this->option('type'));

        $this->queueTour($type);
        $this->queueRentCar($type);
        $this->queueShip($type);
        $this->queueUmrah($type);
        $this->queueMice($type);
        $this->queueArticle($type);
        $this->queueInspiration($type);

        $this->info('Queued translation jobs.');
        // BACKFILL: SETTINGS docs_* -> *_en
        try {
            /** @var TranslateService $tx */
            $tx = app(TranslateService::class);

            $keys = [
                'docs_hero_badge',
                'docs_hero_title',
                'docs_hero_desc',
                'docs_tab_photos',
                'docs_tab_videos',
                'docs_stat_photos',
                'docs_stat_videos',
                'docs_hint',
                'docs_ship_hero_badge',
                'docs_ship_hero_title',
                'docs_ship_hero_desc',
                'docs_umrah_hero_badge',
                'docs_umrah_hero_title',
                'docs_umrah_hero_desc',
            ];

            $src = [];
            $map = []; // index -> key

            foreach ($keys as $k) {
                $idVal = Setting::getValue($k, '');
                $enVal = Setting::getValue($k . '_en', '');

                $idVal = is_string($idVal) ? trim($idVal) : '';
                $enVal = is_string($enVal) ? trim($enVal) : '';

                if ($idVal !== '' && $enVal === '') {
                    $map[] = $k;
                    $src[] = $idVal;
                }
            }

            if ($src) {
                $out = $tx->toEnBatch($src, 'text');

                foreach ($map as $i => $k) {
                    $en = $out[$i] ?? '';
                    $en = is_string($en) ? trim($en) : '';
                    if ($en !== '') {
                        Setting::updateOrCreate(['key' => $k . '_en'], ['value' => $en]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // sengaja diam, biar command tetap jalan (sesuai gaya codebase lo)
        }

        return self::SUCCESS;
    }

    private function queueTour(string $type): void
    {
        if ($type !== 'all' && $type !== 'tour') return;

        TourPackage::whereNull('title_en')->orWhere('title_en', '')->orderBy('id')
            ->chunk(100, function ($rows) {
                foreach ($rows as $p) {
                    \App\Jobs\Translate\TourPackageToEn::dispatch($p->id)->onQueue('translations');
                }
            });
    }

    private function queueRentCar(string $type): void
    {
        if ($type !== 'all' && $type !== 'rentcar') return;

        RentCarPackage::whereNull('title_en')->orWhere('title_en', '')->orderBy('id')
            ->chunk(100, function ($rows) {
                foreach ($rows as $p) {
                    \App\Jobs\Translate\RentCarPackageToEn::dispatch($p->id)->onQueue('translations');
                }
            });
    }

    private function queueShip(string $type): void
    {
        if ($type !== 'all' && $type !== 'ship') return;

        ShipPackage::whereNull('title_en')->orWhere('title_en', '')->orderBy('id')
            ->chunk(100, function ($rows) {
                foreach ($rows as $p) {
                    \App\Jobs\Translate\ShipPackageToEn::dispatch($p->id)->onQueue('translations');
                }
            });
    }

    private function queueUmrah(string $type): void
    {
        if ($type !== 'all' && $type !== 'umrah') return;

        UmrahPackage::whereNull('title_en')->orWhere('title_en', '')->orderBy('id')
            ->chunk(100, function ($rows) {
                foreach ($rows as $p) {
                    \App\Jobs\Translate\UmrahPackageToEn::dispatch($p->id)->onQueue('translations');
                }
            });
    }

    private function queueMice(string $type): void
    {
        if ($type !== 'all' && $type !== 'mice') return;

        MicePackage::whereNull('title_en')->orWhere('title_en', '')->orderBy('id')
            ->chunk(100, function ($rows) {
                foreach ($rows as $p) {
                    \App\Jobs\Translate\MicePackageToEn::dispatch($p->id)->onQueue('translations');
                }
            });
    }

    private function queueArticle(string $type): void
    {
        if ($type !== 'all' && $type !== 'article') return;

        Article::whereNull('title_en')->orWhere('title_en', '')->orderBy('id')
            ->chunk(100, function ($rows) {
                foreach ($rows as $p) {
                    \App\Jobs\Translate\ArticleToEn::dispatch($p->id)->onQueue('translations');
                }
            });
    }

    private function queueInspiration(string $type): void
    {
        if ($type !== 'all' && $type !== 'inspiration') return;

        DestinationInspiration::whereNull('title_en')->orWhere('title_en', '')->orderBy('id')
            ->chunk(100, function ($rows) {
                foreach ($rows as $p) {
                    \App\Jobs\Translate\DestinationInspirationToEn::dispatch($p->id)->onQueue('translations');
                }
            });
    }
}
