<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\TourPackage;
use App\Models\RentCarPackage;
use App\Models\ShipPackage;
use App\Models\RestoranPackage;
use App\Models\HotelPackage;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private function partnerOrdersBaseQuery(int $partnerId)
    {
        $tourIds = TourPackage::where('created_by_partner_id', $partnerId)->pluck('id');
        $rentIds = RentCarPackage::where('created_by_partner_id', $partnerId)->pluck('id');
        $shipIds = ShipPackage::where('created_by_partner_id', $partnerId)->pluck('id');
        $restoIds = RestoranPackage::where('created_by_partner_id', $partnerId)->pluck('id');
        $hotelIds = HotelPackage::where('created_by_partner_id', $partnerId)->pluck('id');

        return Order::query()
            ->where(function ($q) use ($tourIds, $rentIds, $shipIds, $restoIds, $hotelIds) {
                $q->where(function ($w) use ($tourIds) {
                    $w->where('type', 'tour')->whereIn('product_id', $tourIds);
                })
                ->orWhere(function ($w) use ($rentIds) {
                    $w->where('type', 'rent_car')->whereIn('product_id', $rentIds);
                })
                ->orWhere(function ($w) use ($shipIds) {
                    $w->where('type', 'ship')->whereIn('product_id', $shipIds);
                })
                ->orWhere(function ($w) use ($restoIds) {
                    $w->where('type', 'restoran')->whereIn('product_id', $restoIds);
                })
                ->orWhere(function ($w) use ($hotelIds) {
                    $w->where('type', 'hotel')->whereIn('product_id', $hotelIds);
                });
            });
    }

    private function buildOrdersQuery(Request $request, ?string $status = null)
    {
        $partnerId = auth()->id();

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'in:tour,rent_car,ship'],
        ]);

        $keyword = isset($validated['q']) ? trim($validated['q']) : null;

        $query = $this->partnerOrdersBaseQuery($partnerId);

        if ($status) {
            $query->where('order_status', $status);
        }

        if (!empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('customer_name', 'like', "%{$keyword}%")
                  ->orWhere('invoice_number', 'like', "%{$keyword}%")
                  ->orWhere('product_name', 'like', "%{$keyword}%");
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $orders = $this->buildOrdersQuery($request)
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $currentFilter = 'all';
        return view('partner.orders.index', compact('orders', 'currentFilter'));
    }

    public function approved(Request $request)
    {
        $orders = $this->buildOrdersQuery($request, 'approved')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $currentFilter = 'approved';
        return view('partner.orders.index', compact('orders', 'currentFilter'));
    }

    public function rejected(Request $request)
    {
        $orders = $this->buildOrdersQuery($request, 'rejected')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $currentFilter = 'rejected';
        return view('partner.orders.index', compact('orders', 'currentFilter'));
    }

    private function assertOwner(Order $order): void
    {
        $partnerId = auth()->id();

        $isMine = false;

        if ($order->type === 'tour') {
            $isMine = TourPackage::where('id', $order->product_id)
                ->where('created_by_partner_id', $partnerId)->exists();
        } elseif ($order->type === 'rent_car') {
            $isMine = RentCarPackage::where('id', $order->product_id)
                ->where('created_by_partner_id', $partnerId)->exists();
        } elseif ($order->type === 'ship') {
            $isMine = ShipPackage::where('id', $order->product_id)
                ->where('created_by_partner_id', $partnerId)->exists();
        } elseif ($order->type === 'restoran') {
            $isMine = RestoranPackage::where('id', $order->product_id)
                ->where('created_by_partner_id', $partnerId)->exists();
        } elseif ($order->type === 'hotel') {
            $isMine = HotelPackage::where('id', $order->product_id)
                ->where('created_by_partner_id', $partnerId)->exists();
        }

        abort_unless($isMine, 403);
    }

    public function show(Order $order)
    {
        $this->assertOwner($order);
        $order->load('payments');

        return view('partner.orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        $this->assertOwner($order);

        // tegas: hapus order paid itu bahaya (audit & laporan pendapatan rusak)
        if ($order->payment_status === 'paid') {
            return back()->with('error', 'Tidak dapat menghapus order yang sudah dibayar.');
        }

        $order->payments()->delete();
        $order->delete();

        return redirect()->route('partner.orders.index')->with('success', 'Order berhasil dihapus.');
    }
}
