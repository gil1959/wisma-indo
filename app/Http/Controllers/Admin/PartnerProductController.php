<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PartnerProductStatusMail;
use App\Models\TourPackage;
use App\Models\RentCarPackage;
use App\Models\ShipPackage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PartnerProductController extends Controller
{
    private function model(string $type)
    {
        return match ($type) {
            'tour' => TourPackage::class,
            'rentcar' => RentCarPackage::class,
            'ship' => ShipPackage::class,
            default => abort(404),
        };
    }

    private function editRoute(string $type, int $id): string
    {
        return match ($type) {
            'tour' => route('admin.tour-packages.edit', $id),
            'rentcar' => route('admin.rent-car-packages.edit', $id),
            'ship' => route('admin.ship-packages.edit', $id),
            default => abort(404),
        };
    }

    public function index(Request $request)
    {
        $type = $request->get('type', 'tour');
        $status = $request->get('status', 'pending');
        $q = trim((string)$request->get('q', ''));

        $M = $this->model($type);

        $items = $M::query()
            ->whereNotNull('created_by_partner_id')
            ->when($status !== 'all', fn($qq) => $qq->where('partner_review_status', $status))
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%");
            })
            ->latest()
            ->paginate(20);

        return view('admin.partners.products.index', compact('items','type','status','q'));
    }

    public function approve(Request $request, string $type, int $id)
    {
        $request->validate(['note' => ['nullable','string']]);

        $M = $this->model($type);
        $item = $M::findOrFail($id);

        $item->update([
            'is_active' => true,
            'partner_review_status' => 'approved',
            'partner_review_note' => $request->note,
            'partner_reviewed_by' => auth()->id(),
            'partner_reviewed_at' => now(),
            'partner_disabled_note' => null,
            'partner_disabled_by' => null,
            'partner_disabled_at' => null,
        ]);

        $this->mailPartner($item, $type, 'APPROVED', $request->note);

        return redirect($this->editRoute($type, $id))->with('success', 'Produk partner approved.');
    }

    public function reject(Request $request, string $type, int $id)
    {
        $request->validate(['note' => ['required','string']]);

        $M = $this->model($type);
        $item = $M::findOrFail($id);

        $item->update([
            'is_active' => false,
            'partner_review_status' => 'rejected',
            'partner_review_note' => $request->note,
            'partner_reviewed_by' => auth()->id(),
            'partner_reviewed_at' => now(),
        ]);

        $this->mailPartner($item, $type, 'REJECTED', $request->note);

        return redirect($this->editRoute($type, $id))->with('success', 'Produk partner rejected.');
    }

    public function disable(Request $request, string $type, int $id)
    {
        $request->validate(['note' => ['required','string']]);

        $M = $this->model($type);
        $item = $M::findOrFail($id);

        $item->update([
            'is_active' => false,
            'partner_disabled_note' => $request->note,
            'partner_disabled_by' => auth()->id(),
            'partner_disabled_at' => now(),
        ]);

        $this->mailPartner($item, $type, 'DISABLED', $request->note);

        return redirect($this->editRoute($type, $id))->with('success', 'Produk partner dinonaktifkan.');
    }

    private function mailPartner($item, string $type, string $status, ?string $note): void
    {
        $partner = User::find($item->created_by_partner_id);
        if (!$partner) return;

        Mail::to($partner->email)->send(new PartnerProductStatusMail(
            partnerName: $partner->name,
            productType: $type,
            productTitle: $item->title ?? '-',
            status: $status,
            note: $note
        ));
    }
}
