<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourPackage;
use App\Jobs\Translate\TourPackageToEn;

class TourTranslateBackfillController extends Controller
{
    public function run(Request $request)
    {
        // Security token wajib
        $token = (string) $request->query('token', '');
        $expected = (string) env('TRANSLATE_TRIGGER_TOKEN', '');

        if ($expected === '' || !hash_equals($expected, $token)) {
            abort(403, 'Forbidden');
        }

        // Batch per request (shared hosting aman kecil)
        $limit = (int) $request->query('limit', 3);
        if ($limit < 1) $limit = 1;
        if ($limit > 10) $limit = 10;

        // Auto-loop via redirect
        $continue = (int) $request->query('continue', 1) === 1;

        // Ambil yang belum punya EN (pakai title_en sebagai penanda)
        $rows = TourPackage::query()
            ->where(function ($q) {
                $q->whereNull('title_en')->orWhere('title_en', '');
            })
            ->orderBy('id')
            ->limit($limit)
            ->get(['id']);

        $processed = 0;

        foreach ($rows as $row) {
            // Shared hosting: paksa jalan sekarang (tanpa queue worker)
            if (config('queue.default') === 'sync') {
    TourPackageToEn::dispatchSync($row->id);
} else {
    TourPackageToEn::dispatch($row->id)->onQueue('translations');
}
            $processed++;
        }

        // Kalau masih ada yang diproses, redirect ke URL yang sama supaya lanjut otomatis
        if ($continue && $processed > 0) {
            return redirect($request->fullUrl());
        }

        return response()->json([
            'ok' => true,
            'processed' => $processed,
            'message' => $processed > 0 ? 'Batch processed. Set continue=1 untuk auto-loop.' : 'Done. No more records to translate.',
        ]);
    }
}
