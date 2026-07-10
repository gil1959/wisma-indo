<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;
use App\Mail\OrderVerificationMail;

use Illuminate\Http\Request;


class OrderController extends Controller
{
    private function buildOrdersQuery(Request $request, ?string $status = null)
    {
        // Ambil keyword dari query string ?q=...
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $keyword = isset($validated['q']) ? trim($validated['q']) : null;

        $query = Order::query();

        // Filter status kalau tab approved/rejected
        if ($status) {
            $query->where('order_status', $status);
        }

        // Filter pencarian
        if ($keyword !== null && $keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('customer_name', 'like', "%{$keyword}%")
                    ->orWhere('invoice_number', 'like', "%{$keyword}%");
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $orders = $this->buildOrdersQuery($request)
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString(); // penting: pagination tetap bawa ?q=

        $currentFilter = 'all';

        return view('admin.orders.index', compact('orders', 'currentFilter'));
    }


    public function approved(Request $request)
    {
        $orders = $this->buildOrdersQuery($request, 'approved')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $currentFilter = 'approved';

        return view('admin.orders.index', compact('orders', 'currentFilter'));
    }


    public function rejected(Request $request)
    {
        $orders = $this->buildOrdersQuery($request, 'rejected')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $currentFilter = 'rejected';

        return view('admin.orders.index', compact('orders', 'currentFilter'));
    }


    public function show(Order $order)
    {
        $order->load('payments');

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $payment = $order->payments()->latest()->first();

        // simpan status lama (anti resend kalau admin klik approve berulang)
        $prevPaymentStatus = $order->payment_status;
        $prevOrderStatus   = $order->order_status;

        if ($data['action'] === 'approve') {
    $order->update([
        'payment_status' => 'paid',
        'order_status'   => 'approved',
    ]);

    // ===== affiliate commission status ikut approved =====
    if ($order->affiliate_user_id && $order->affiliate_commission_amount !== null) {
        $order->update([
            'affiliate_commission_status' => 'approved',
        ]);
    }


            if ($payment && $payment->status === 'waiting_verification') {
                $payment->update(['status' => 'paid']);
            }

            // ✅ EMAIL: verified/approved (admin + buyer), hanya kalau status berubah
            if ($prevPaymentStatus !== 'paid' || $prevOrderStatus !== 'approved') {
                try {
                    if (!empty($order->customer_email)) {
                        Mail::to($order->customer_email)->send(new OrderVerificationMail($order, 'approved', false));
                    }

                    $adminEmail = Setting::invoiceAdminEmail();
                    if (!empty($adminEmail)) {
                        Mail::to($adminEmail)->send(new OrderVerificationMail($order, 'approved', true));
                    }

                    $payoutService = app(\App\Services\PartnerPayoutService::class);
                    $partnerId = $payoutService->resolvePartnerIdFromOrder($order);
                    if ($partnerId) {
                        $partner = \App\Models\User::find($partnerId);
                        if ($partner && $partner->email !== $order->customer_email) {
                            Mail::to($partner->email)->send(new OrderVerificationMail($order, 'approved', true));
                        }
                    }
                } catch (\Throwable $e) {
                    Log::error('Email verifikasi (approved) gagal dikirim', [
                        'invoice' => $order->invoice_number,
                        'err' => $e->getMessage(),
                    ]);
                }
            }
        } else {
    $order->update([
        'payment_status' => 'failed',
        'order_status'   => 'rejected',
    ]);

    // ===== affiliate commission status ikut cancelled =====
    if ($order->affiliate_user_id) {
        $order->update([
            'affiliate_commission_status' => 'cancelled',
        ]);
    }


            if ($payment && $payment->status === 'waiting_verification') {
                $payment->update(['status' => 'failed']);
            }

            // ✅ EMAIL: verified/rejected (admin + buyer), hanya kalau status berubah
            if ($prevPaymentStatus !== 'failed' || $prevOrderStatus !== 'rejected') {
                try {
                    if (!empty($order->customer_email)) {
                        Mail::to($order->customer_email)->send(new OrderVerificationMail($order, 'rejected', false));
                    }

                    $adminEmail = Setting::invoiceAdminEmail();
                    if (!empty($adminEmail)) {
                        Mail::to($adminEmail)->send(new OrderVerificationMail($order, 'rejected', true));
                    }

                    $payoutService = app(\App\Services\PartnerPayoutService::class);
                    $partnerId = $payoutService->resolvePartnerIdFromOrder($order);
                    if ($partnerId) {
                        $partner = \App\Models\User::find($partnerId);
                        if ($partner && $partner->email !== $order->customer_email) {
                            Mail::to($partner->email)->send(new OrderVerificationMail($order, 'rejected', true));
                        }
                    }
                } catch (\Throwable $e) {
                    Log::error('Email verifikasi (rejected) gagal dikirim', [
                        'invoice' => $order->invoice_number,
                        'err' => $e->getMessage(),
                    ]);
                }
            }
        }

        return back()->with('success', 'Status pesanan diperbarui.');
    }

    public function destroy(Order $order)
    {
        // Opsi keamanan: jangan hapus order yang sudah dibayar
        if ($order->payment_status === 'paid') {
            return back()->with('error', 'Tidak dapat menghapus order yang sudah dibayar.');
        }

        // Hapus semua payment terkait dulu
        $order->payments()->delete();
        $order->delete();

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Order berhasil dihapus.');
    }

    public function rekap(Request $request)
    {
        $from = $request->query('from');
        $to   = $request->query('to');

        $orders = collect();
        $summary = [
            'total_orders' => 0,
            'total_amount' => 0,
        ];

        if ($from && $to) {
            // inclusive range (full day)
            $orders = Order::query()
                ->whereBetween('created_at', [
                    $from . ' 00:00:00',
                    $to . ' 23:59:59',
                ])
                ->orderBy('created_at', 'asc')
                ->get();

            $summary['total_orders'] = $orders->count();
            $summary['total_amount'] = $orders->sum('final_price');
        }

        return view('admin.orders.rekap', compact('orders', 'from', 'to', 'summary'));
    }

    public function printRekap(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        $from = $request->from;
        $to   = $request->to;

        $orders = Order::query()
            ->whereBetween('created_at', [
                $from . ' 00:00:00',
                $to . ' 23:59:59',
            ])
            ->orderBy('created_at', 'asc')
            ->get();

        $summary = [
            'total_orders' => $orders->count(),
            'total_amount' => $orders->sum('final_price'),
        ];

        return view('admin.orders.print-rekap', compact('orders', 'from', 'to', 'summary'));
    }

    public function printRekapPaid(Request $request)
{
    $request->validate([
        'from' => 'required|date',
        'to'   => 'required|date|after_or_equal:from',
    ]);

    $from = $request->from;
    $to   = $request->to;

    $orders = Order::query()
        ->whereBetween('created_at', [
            $from . ' 00:00:00',
            $to . ' 23:59:59',
        ])
        ->where('payment_status', 'paid')
        ->orderBy('created_at', 'asc')
        ->get();

    $summary = [
        'total_orders' => $orders->count(),
        'total_amount' => $orders->sum('final_price'),
    ];

    return view('admin.orders.print-rekap', compact('orders', 'from', 'to', 'summary'));
}

public function printRekapSelected(Request $request)
{
    $request->validate([
        'from' => 'required|date',
        'to'   => 'required|date|after_or_equal:from',
        'order_ids'   => 'required|array|min:1',
        'order_ids.*' => 'integer|exists:orders,id',
    ]);

    $from = $request->from;
    $to   = $request->to;
    $ids  = $request->order_ids;

    $orders = Order::query()
    ->whereBetween('created_at', [
        $from . ' 00:00:00',
        $to . ' 23:59:59',
    ])
    ->whereIn('id', $ids)
    ->orderBy('created_at', 'asc')
    ->get();


    $summary = [
        'total_orders' => $orders->count(),
        'total_amount' => $orders->sum('final_price'),
    ];

    return view('admin.orders.print-rekap', compact('orders', 'from', 'to', 'summary'));
}


    public function printInvoice(Order $order)
{
    $order->load('payments');

    return view('shared.invoice-print', compact('order'));
}

}
