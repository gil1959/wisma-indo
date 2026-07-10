<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use App\Models\RentCarPackage;
use App\Models\ShipPackage;
use App\Models\RestoranPackage;
use App\Models\HotelPackage;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PartnerProductReviewController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type');   // tour|rentcar|ship|null
        $status = $request->get('status'); // pending|approved|rejected|disabled|null
        $q = trim((string)$request->get('q'));

        // helper filter
        $applyFilters = function ($query) use ($status, $q) {
            if (!empty($status)) {
                $query->where('partner_review_status', $status);
            }
            if (!empty($q)) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('title', 'like', "%{$q}%")
                       ->orWhere('slug', 'like', "%{$q}%");
                });
            }
            return $query;
        };

        // ambil sesuai type, atau gabung semua
        $items = collect();

        if ($type === 'tour' || empty($type)) {
            $tour = $applyFilters(
                TourPackage::query()->whereNotNull('created_by_partner_id')
            )
                ->latest()
                ->take(300)
                ->get()
                ->map(fn($p) => $this->toItem($p, 'tour'));
            $items = $items->concat($tour);
        }

        if ($type === 'rentcar' || empty($type)) {
            $rentcar = $applyFilters(
                RentCarPackage::query()->whereNotNull('created_by_partner_id')
            )
                ->latest()
                ->take(300)
                ->get()
                ->map(fn($p) => $this->toItem($p, 'rentcar'));
            $items = $items->concat($rentcar);
        }

        if ($type === 'ship' || empty($type)) {
            $ship = $applyFilters(
                ShipPackage::query()->whereNotNull('created_by_partner_id')
            )
                ->latest()
                ->take(300)
                ->get()
                ->map(fn($p) => $this->toItem($p, 'ship'));
            $items = $items->concat($ship);
        }

        if ($type === 'restoran' || empty($type)) {
            $restoran = $applyFilters(
                RestoranPackage::query()->whereNotNull('created_by_partner_id')
            )
                ->latest()
                ->take(300)
                ->get()
                ->map(fn($p) => $this->toItem($p, 'restoran'));
            $items = $items->concat($restoran);
        }

        if ($type === 'hotel' || empty($type)) {
            $hotel = $applyFilters(
                HotelPackage::query()->whereNotNull('created_by_partner_id')
            )
                ->latest()
                ->take(300)
                ->get()
                ->map(fn($p) => $this->toItem($p, 'hotel'));
            $items = $items->concat($hotel);
        }

        // sort gabungan by created_at desc
        $items = $items->sortByDesc('created_at')->values();

        return view('admin.partners.products.index', [
            'items' => $items,
            'filters' => [
                'type' => $type,
                'status' => $status,
                'q' => $q,
            ],
        ]);
    }

    public function approve(Request $request, string $type, int $id)
    {
        $package = $this->findByType($type, $id);

        $note = $request->input('note');

        $package->partner_review_status = 'approved';
        $package->partner_review_note = $note;
        $package->partner_reviewed_by = auth()->id();
        $package->partner_reviewed_at = Carbon::now();
        $package->is_active = true;
        $package->save();

        return back()->with('success', 'Produk berhasil di-approve.');
    }

    public function reject(Request $request, string $type, int $id)
    {
        $request->validate([
            'note' => ['required', 'string', 'min:3'],
        ]);

        $package = $this->findByType($type, $id);

        $package->partner_review_status = 'rejected';
        $package->partner_review_note = $request->note;
        $package->partner_reviewed_by = auth()->id();
        $package->partner_reviewed_at = Carbon::now();
        $package->is_active = false;
        $package->save();

        return back()->with('success', 'Produk berhasil di-reject.');
    }

    public function disable(Request $request, string $type, int $id)
    {
        $request->validate([
            'note' => ['required', 'string', 'min:3'],
        ]);

        $package = $this->findByType($type, $id);

        $package->partner_review_status = 'disabled';
        $package->partner_review_note = $request->note;
        $package->partner_reviewed_by = auth()->id();
        $package->partner_reviewed_at = Carbon::now();
        $package->is_active = false;
        $package->save();

        return back()->with('success', 'Produk berhasil dinonaktifkan.');
    }

    private function findByType(string $type, int $id)
    {
        return match ($type) {
            'tour' => TourPackage::query()->whereNotNull('created_by_partner_id')->findOrFail($id),
            'rentcar' => RentCarPackage::query()->whereNotNull('created_by_partner_id')->findOrFail($id),
            'ship' => ShipPackage::query()->whereNotNull('created_by_partner_id')->findOrFail($id),
            'restoran' => RestoranPackage::query()->whereNotNull('created_by_partner_id')->findOrFail($id),
            'hotel' => HotelPackage::query()->whereNotNull('created_by_partner_id')->findOrFail($id),
            default => abort(404, 'Unknown type'),
        };
    }

    private function toItem($p, string $type): array
    {
        return [
            'type' => $type,
            'id' => $p->id,
            'title' => $p->title ?? '-',
            'slug' => $p->slug ?? '-',
            'is_active' => (bool)($p->is_active ?? false),
            'status' => $p->partner_review_status ?? null,
            'note' => $p->partner_review_note ?? null,
            'created_at' => $p->created_at,
            'edit_url' => match ($type) {
                'tour' => route('admin.tour-packages.edit', $p->id),
                'rentcar' => route('admin.rent-car-packages.edit', $p->id),
                'ship' => route('admin.ship-packages.edit', $p->id),
                'restoran' => route('admin.restoran-packages.edit', $p->id),
                'hotel' => route('admin.hotel-packages.edit', $p->id),
                default => '#'
            },
        ];
    }
}
