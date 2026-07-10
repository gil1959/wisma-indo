<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\TourPackage;
use App\Models\ShipPackage;
use App\Models\UmrahPackage;
use App\Models\MicePackage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\TranslateService;

class HomePromoToursController extends Controller
{
    public function edit()
    {
        $settings = Setting::pluck('value', 'key');

        // ===== TOUR =====
        $promoCandidates = TourPackage::query()
            ->where('is_active', true)
            ->where('label', 'PROMO')
            ->orderBy('title')
            ->get(['id', 'title']);

        $selectedIds = json_decode($settings['home_promo_custom_ids'] ?? '[]', true);
        $selectedIds = is_array($selectedIds) ? array_values(array_unique(array_map('intval', $selectedIds))) : [];

        // ===== SHIP =====
        $promoShipCandidates = ShipPackage::query()
            ->where('is_active', true)
            ->where('label', 'PROMO')
            ->orderBy('title')
            ->get(['id', 'title']);

        $selectedShipIds = json_decode($settings['home_ship_promo_custom_ids'] ?? '[]', true);
        $selectedShipIds = is_array($selectedShipIds) ? array_values(array_unique(array_map('intval', $selectedShipIds))) : [];

        // ===== UMRAH (NEW) =====
        $promoUmrahCandidates = UmrahPackage::query()
            ->where('is_active', true)
            ->where('label', 'PROMO')
            ->orderBy('title')
            ->get(['id', 'title']);

        $selectedUmrahIds = json_decode($settings['home_umrah_promo_custom_ids'] ?? '[]', true);
        $selectedUmrahIds = is_array($selectedUmrahIds) ? array_values(array_unique(array_map('intval', $selectedUmrahIds))) : [];

        // ===== MICE (NEW) =====
        $promoMiceCandidates = MicePackage::query()
            ->where('is_active', true)
            ->where('label', 'PROMO')
            ->orderBy('title')
            ->get(['id', 'title']);

        $selectedMiceIds = json_decode($settings['home_mice_promo_custom_ids'] ?? '[]', true);
        $selectedMiceIds = is_array($selectedMiceIds) ? array_values(array_unique(array_map('intval', $selectedMiceIds))) : [];

        return view('admin.home-sections.promo-tours', compact(
            'settings',
            'promoCandidates',
            'selectedIds',
            'promoShipCandidates',
            'selectedShipIds',
            'promoUmrahCandidates',
            'selectedUmrahIds',
            'promoMiceCandidates',
            'selectedMiceIds'
        ));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            // ===== TOUR =====
            'home_promo_enabled' => ['nullable', 'boolean'],
            'home_promo_badge' => ['nullable', 'string', 'max:30'],
            'home_promo_title' => ['nullable', 'string', 'max:140'],
            'home_promo_desc' => ['nullable', 'string', 'max:240'],
            'home_promo_mode' => ['nullable', Rule::in(['auto', 'custom'])],
            'home_promo_custom_ids' => ['nullable', 'array'],
            'home_promo_custom_ids.*' => ['integer', 'exists:tour_packages,id'],

            // ===== SHIP =====
            'home_ship_promo_enabled' => ['nullable', 'boolean'],
            'home_ship_promo_badge' => ['nullable', 'string', 'max:30'],
            'home_ship_promo_title' => ['nullable', 'string', 'max:140'],
            'home_ship_promo_desc' => ['nullable', 'string', 'max:240'],
            'home_ship_promo_mode' => ['nullable', Rule::in(['auto', 'custom'])],
            'home_ship_promo_custom_ids' => ['nullable', 'array'],
            'home_ship_promo_custom_ids.*' => ['integer', 'exists:ship_packages,id'],

            // ===== UMRAH (NEW) =====
            'home_umrah_promo_enabled' => ['nullable', 'boolean'],
            'home_umrah_promo_badge' => ['nullable', 'string', 'max:30'],
            'home_umrah_promo_title' => ['nullable', 'string', 'max:140'],
            'home_umrah_promo_desc' => ['nullable', 'string', 'max:240'],
            'home_umrah_promo_mode' => ['nullable', Rule::in(['auto', 'custom'])],
            'home_umrah_promo_custom_ids' => ['nullable', 'array'],
            'home_umrah_promo_custom_ids.*' => ['integer', 'exists:umrah_packages,id'],

            // ===== MICE (NEW) =====
            'home_mice_promo_enabled' => ['nullable', 'boolean'],
            'home_mice_promo_badge' => ['nullable', 'string', 'max:30'],
            'home_mice_promo_title' => ['nullable', 'string', 'max:140'],
            'home_mice_promo_desc' => ['nullable', 'string', 'max:240'],
            'home_mice_promo_mode' => ['nullable', Rule::in(['auto', 'custom'])],
            'home_mice_promo_custom_ids' => ['nullable', 'array'],
            'home_mice_promo_custom_ids.*' => ['integer', 'exists:mice_packages,id'],
        ]);

        // helper
        $set = function (string $key, string $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        };

        // ===== TOUR =====
        $set('home_promo_enabled', $request->boolean('home_promo_enabled') ? '1' : '0');
        $set('home_promo_badge', $data['home_promo_badge'] ?? 'PROMO');
        $set('home_promo_title', $data['home_promo_title'] ?? 'Paket Tour Promo');
        $set('home_promo_desc', $data['home_promo_desc'] ?? '');
        $set('home_promo_mode', $data['home_promo_mode'] ?? 'auto');
        $tourIds = array_values(array_unique(array_map('intval', $data['home_promo_custom_ids'] ?? [])));
        $set('home_promo_custom_ids', json_encode($tourIds));

        // ===== SHIP =====
        $set('home_ship_promo_enabled', $request->boolean('home_ship_promo_enabled') ? '1' : '0');
        $set('home_ship_promo_badge', $data['home_ship_promo_badge'] ?? 'PROMO KAPAL');
        $set('home_ship_promo_title', $data['home_ship_promo_title'] ?? 'Paket Sewa Kapal Promo');
        $set('home_ship_promo_desc', $data['home_ship_promo_desc'] ?? '');
        $set('home_ship_promo_mode', $data['home_ship_promo_mode'] ?? 'auto');
        $shipIds = array_values(array_unique(array_map('intval', $data['home_ship_promo_custom_ids'] ?? [])));
        $set('home_ship_promo_custom_ids', json_encode($shipIds));

        // ===== UMRAH (NEW) =====
        $set('home_umrah_promo_enabled', $request->boolean('home_umrah_promo_enabled') ? '1' : '0');
        $set('home_umrah_promo_badge', $data['home_umrah_promo_badge'] ?? 'PROMO UMRAH');
        $set('home_umrah_promo_title', $data['home_umrah_promo_title'] ?? 'Paket Umrah Promo');
        $set('home_umrah_promo_desc', $data['home_umrah_promo_desc'] ?? '');
        $set('home_umrah_promo_mode', $data['home_umrah_promo_mode'] ?? 'auto');
        $umrahIds = array_values(array_unique(array_map('intval', $data['home_umrah_promo_custom_ids'] ?? [])));
        $set('home_umrah_promo_custom_ids', json_encode($umrahIds));

        // ===== MICE (NEW) =====
        $set('home_mice_promo_enabled', $request->boolean('home_mice_promo_enabled') ? '1' : '0');
        $set('home_mice_promo_badge', $data['home_mice_promo_badge'] ?? 'PROMO MICE');
        $set('home_mice_promo_title', $data['home_mice_promo_title'] ?? 'Paket MICE Promo');
        $set('home_mice_promo_desc', $data['home_mice_promo_desc'] ?? '');
        $set('home_mice_promo_mode', $data['home_mice_promo_mode'] ?? 'auto');
        $miceIds = array_values(array_unique(array_map('intval', $data['home_mice_promo_custom_ids'] ?? [])));
        $set('home_mice_promo_custom_ids', json_encode($miceIds));

        // AUTO TRANSLATE (DEEPL) -> promo label/title/desc *_en (admin input 1x)
        try {
            /** @var TranslateService $tx */
            $tx = app(TranslateService::class);

            $src = [
                // TOUR
                'home_promo_badge' => (string)($data['home_promo_badge'] ?? 'PROMO'),
                'home_promo_title' => (string)($data['home_promo_title'] ?? 'Paket Tour Promo'),
                'home_promo_desc'  => (string)($data['home_promo_desc'] ?? ''),

                // SHIP
                'home_ship_promo_badge' => (string)($data['home_ship_promo_badge'] ?? 'PROMO KAPAL'),
                'home_ship_promo_title' => (string)($data['home_ship_promo_title'] ?? 'Paket Sewa Kapal Promo'),
                'home_ship_promo_desc'  => (string)($data['home_ship_promo_desc'] ?? ''),

                // UMRAH
                'home_umrah_promo_badge' => (string)($data['home_umrah_promo_badge'] ?? 'PROMO UMRAH'),
                'home_umrah_promo_title' => (string)($data['home_umrah_promo_title'] ?? 'Paket Umrah Promo'),
                'home_umrah_promo_desc'  => (string)($data['home_umrah_promo_desc'] ?? ''),

                // MICE
                'home_mice_promo_badge' => (string)($data['home_mice_promo_badge'] ?? 'PROMO MICE'),
                'home_mice_promo_title' => (string)($data['home_mice_promo_title'] ?? 'Paket MICE Promo'),
                'home_mice_promo_desc'  => (string)($data['home_mice_promo_desc'] ?? ''),
            ];

            // buang yang kosong biar ga buang quota
            $src = array_filter($src, fn($v) => trim((string)$v) !== '');

            if (count($src) > 0) {
                $keys = array_keys($src);
                $vals = array_values($src);

                $out = $tx->toEnBatch($vals, 'text');

                foreach ($keys as $i => $k) {
                    $enVal = $out[$i] ?? null;
                    if (is_string($enVal) && trim($enVal) !== '') {
                        Setting::updateOrCreate(['key' => $k . '_en'], ['value' => $enVal]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // jangan bikin save gagal
        }

        return redirect()
            ->route('admin.home-sections.promo-tours.edit')
            ->with('success', 'Pengaturan section Promo berhasil disimpan.');
    }
}
