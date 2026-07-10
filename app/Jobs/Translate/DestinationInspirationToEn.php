<?php

namespace App\Jobs\Translate;

use App\Models\DestinationInspiration;
use App\Services\TranslateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Client\ConnectionException;

class DestinationInspirationToEn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $tries = 5;
    public int $timeout = 120;
    public bool $failOnTimeout = false;
    public array $backoff = [60, 300, 900, 1800, 3600];
    public function __construct(public int $id) {}

    public function handle(TranslateService $tx): void
    {
        $d = DestinationInspiration::find($this->id);
        if (!$d) return;

        if ($d->title_en && trim((string)$d->title_en) !== '') return;
        if (!is_string($d->title) || trim($d->title) === '') return;

        try {
            $val = $tx->toEnBatch([$d->title], 'text')[0] ?? null;
            if (is_string($val) && trim($val) !== '') {
                $d->title_en = $val;
                $d->save();
            }
        } catch (ConnectionException $e) {
            $this->release(300);
            return;
        }
    }
}
