<?php

namespace App\Jobs\Translate;

use App\Models\Documentation;
use App\Services\TranslateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Client\ConnectionException;

class DocumentationToEn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $tries = 5;
    public int $timeout = 120;
    public bool $failOnTimeout = false;
    public array $backoff = [60, 300, 900, 1800, 3600];
    public function __construct(public int $id) {}

    public function handle(TranslateService $tx): void
    {
        $doc = Documentation::find($this->id);
        if (!$doc) return;

        // kalau sudah ada EN, stop
        $cur = is_string($doc->title_en) ? trim($doc->title_en) : '';
        $src = is_string($doc->title) ? trim($doc->title) : '';

        if ($cur !== '' || $src === '') return;

        try {
            $out = $tx->toEnBatch([$src], 'text')[0] ?? null;
            $out = is_string($out) ? trim($out) : '';

            if ($out === '') return;

            $doc->title_en = $out;
            $doc->save();
        } catch (ConnectionException $e) {
            $this->release(300);
            return;
        }
    }
}
