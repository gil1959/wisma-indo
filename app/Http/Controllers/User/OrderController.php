<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Setting;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $q = Order::query()
            ->where(function ($sub) use ($authUser) {
                $sub->where('user_id', $authUser->id)
                    ->orWhere(function ($q2) use ($authUser) {
                        $q2->whereNull('user_id')
                            ->where('customer_email', $authUser->email);
                    });
            });


        // Search: invoice / product
        if ($request->filled('search')) {
            $search = trim($request->search);
            $q->where(function ($sub) use ($search) {
                $sub->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('product_name', 'like', "%{$search}%");
            });
        }

        // Filter: type
        if ($request->filled('type')) {
            $q->where('type', $request->type);
        }

        // Filter: order_status
        if ($request->filled('order_status')) {
            $q->where('order_status', $request->order_status);
        }

        // Filter: payment_status
        if ($request->filled('payment_status')) {
            $q->where('payment_status', $request->payment_status);
        }

        $orders = $q->latest()->paginate(10)->withQueryString();

        return view('user.orders', compact('orders'));
    }

    public function show(Order $order)
    {
        // Security: jangan sampai user bisa buka order orang lain
        $authUser = auth()->user();

        $canView =
            ($order->user_id !== null && $order->user_id === $authUser->id)
            || ($order->user_id === null
                && !empty($order->customer_email)
                && $order->customer_email === $authUser->email);

        abort_unless($canView, 403);


        $order->load('payments');

        return view('user.orders.show', compact('order'));
    }
    public function confirmAdmin(Order $order)
    {
        $authUser = auth()->user();

        $canView =
            ($order->user_id !== null && $order->user_id === $authUser->id)
            || ($order->user_id === null
                && !empty($order->customer_email)
                && $order->customer_email === $authUser->email);

        abort_unless($canView, 403);


        $partner = \App\Support\OrderPartnerResolver::resolvePartnerUser($order);

        // target default: admin
        $targetName = 'Admin';
        $targetWa = null;

        if ($partner && $partner->phone) {
            $targetName = $partner->name ?: 'Partner';
            $targetWa = \App\Support\OrderPartnerResolver::normalizeWhatsapp($partner->phone);
        }

        // kalau tidak ada partner/phone, fallback ke admin WA setting
        if (!$targetWa) {
            $rawWa = (string) Setting::where('key', 'footer_whatsapp')->value('value');
            $targetWa = \App\Support\OrderPartnerResolver::normalizeWhatsapp($rawWa);
            $targetName = 'Admin';
        }

        $isEn = app()->getLocale() === 'en';

        abort_if(
            empty($targetWa),
            404,
            $isEn
                ? 'Destination WhatsApp number is not configured (partner/admin).'
                : 'Nomor WhatsApp tujuan belum diset (partner/admin).'
        );

        $total = $order->payable_amount ?? $order->final_price;

        if ($isEn) {
            $msg =
                "Hello {$targetName},\n"
                . "I'd like to confirm an order:\n\n"
                . "Invoice: {$order->invoice_number}\n"
                . "Name: {$order->customer_name}\n"
                . "Email: {$order->customer_email}\n"
                . "Customer WhatsApp: {$order->customer_phone}\n"
                . "Product: {$order->product_name}\n"
                . "Total: Rp " . number_format((int)$total, 0, ',', '.') . "\n\n"
                . "Thank you.";
        } else {
            $msg =
                "Halo {$targetName},\n"
                . "Saya ingin konfirmasi order:\n\n"
                . "Invoice: {$order->invoice_number}\n"
                . "Nama: {$order->customer_name}\n"
                . "Email: {$order->customer_email}\n"
                . "WA Customer: {$order->customer_phone}\n"
                . "Produk: {$order->product_name}\n"
                . "Total: Rp " . number_format((int)$total, 0, ',', '.') . "\n\n"
                . "Terima kasih.";
        }

        return redirect()->away(\App\Support\OrderPartnerResolver::buildWaLink($targetWa, $msg));
    }

    public function printInvoice(Order $order)
    {
        // Security: jangan sampai user bisa print invoice orang lain
        $authUser = auth()->user();

        $canView =
            ($order->user_id !== null && $order->user_id === $authUser->id)
            || ($order->user_id === null
                && !empty($order->customer_email)
                && $order->customer_email === $authUser->email);

        abort_unless($canView, 403);

        $order->load('payments');

        return view('shared.invoice-print', compact('order'));
    }
}
