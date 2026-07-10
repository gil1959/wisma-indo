<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateLink;
use App\Models\Order;
use Illuminate\Http\Request;

class AffiliateOrderController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $status = trim((string) $request->get('status'));

        $orders = Order::query()
            ->whereNotNull('affiliate_user_id')
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('invoice_number', 'like', "%{$q}%")
                      ->orWhere('customer_name', 'like', "%{$q}%")
                      ->orWhere('customer_email', 'like', "%{$q}%")
                      ->orWhere('affiliate_ref', 'like', "%{$q}%");
                });
            })
            ->when(in_array($status, ['pending','approved','paid','cancelled'], true), function ($qq) use ($status) {
                $qq->where('affiliate_commission_status', $status);
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.affiliate.orders.index', compact('orders', 'q', 'status'));
    }

    public function show(Order $order)
    {
        abort_if(!$order->affiliate_user_id, 404);

        $link = null;
        if ($order->affiliate_link_id) {
            $link = AffiliateLink::find($order->affiliate_link_id);
        }

        return view('admin.affiliate.orders.show', compact('order', 'link'));
    }

    public function setCommission(Request $request, Order $order)
{
    abort_if(!$order->affiliate_user_id, 404);

    $data = $request->validate([
        'affiliate_commission_type' => ['required', 'in:fixed,percent'],
        'affiliate_commission_value' => ['required', 'numeric', 'min:0'],
        'affiliate_commission_status' => ['required', 'in:pending,approved,paid,cancelled'],
    ]);

    // validasi tambahan khusus percent biar gak ngaco
    if ($data['affiliate_commission_type'] === 'percent' && (float)$data['affiliate_commission_value'] > 100) {
        return back()
            ->withInput()
            ->with('error', 'Percent tidak boleh lebih dari 100.');
    }

    try {
        $type  = $data['affiliate_commission_type'];
        $value = (float) $data['affiliate_commission_value'];

        // hitung amount
        if ($type === 'percent') {
            $base = (float) ($order->final_price ?? 0);
            $amount = round($base * $value / 100, 2);
        } else {
            // fixed: value dianggap nominal komisi
            $amount = round($value, 2);
        }

        $order->affiliate_commission_type = $type;
        $order->affiliate_commission_value = $value;
        $order->affiliate_commission_amount = $amount;

        $order->affiliate_commission_status = $data['affiliate_commission_status'];
        $order->affiliate_commission_set_by = auth()->id();
        $order->affiliate_commission_set_at = now();

        $order->save();
return redirect()
    ->route('admin.affiliate.orders.index')
    ->with('success', 'Commission berhasil disimpan.');
    } catch (\Throwable $e) {
        return back()
            ->withInput()
            ->with('error', 'Gagal menyimpan commission: ' . $e->getMessage());
    }
}

}
