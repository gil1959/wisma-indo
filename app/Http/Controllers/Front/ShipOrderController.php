<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShipPackage;
use App\Models\Order;
use App\Models\Promo;
use App\Models\Setting;
use App\Mail\OrderInvoiceMail;
use App\Mail\PartnerOrderInvoiceMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\PartnerPayoutService;
use App\Models\User;
use App\Models\AffiliateLink;

class ShipOrderController extends Controller
{
    public function draft(Request $request, $slug)
    {
        $package = ShipPackage::with('tiers')->where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email',
            'phone' => 'required|string|max:50',
            'departure_date' => 'required|date|after_or_equal:today',

            // user pilih tier dari sidebar
            'tier_id' => 'required|integer',
            // quantity optional (kalau lu mau 1 aja, bisa hardcode di frontend)
            'qty' => 'nullable|integer|min:1|max:999',

            'promo_id' => 'nullable|integer',
        ]);

        $tier = $package->tiers->firstWhere('id', (int)$data['tier_id']);
        if (!$tier) {
            return response()->json(['error' => 'Harga tidak valid.'], 422);
        }

        $qty = (int)($data['qty'] ?? 1);
        $subtotal = (int)$tier->price * $qty;

        // promo (copy pola tour/rentcar)
        $discount = 0;
        $promoUsed = null;

        if (!empty($data['promo_id'])) {
            $promo = Promo::find($data['promo_id']);

            if ($promo && $promo->is_valid_for($subtotal)) {
                $alreadyUsed = Order::where('customer_email', $data['email'])
                    ->where('promo_id', $promo->id)
                    ->exists();

                if ($alreadyUsed) {
                    return response()->json([
                        'error' => 'Kode promo ini sudah pernah digunakan untuk email ini.'
                    ], 422);
                }

                $discount = $promo->calculate_discount($subtotal);
                $promoUsed = $promo;
            }
        }

        $final = max(0, $subtotal - $discount);
$affUserId = session('affiliate_user_id');
$affLinkId = session('affiliate_link_id');
$affRef    = session('affiliate_ref');

$affType   = null;
$affValue  = null;
$affAmount = null;
$affStatus = null;

if ($affUserId && $affLinkId && $affRef) {
    $affUser = User::find($affUserId);

    if ($affUser && $affUser->is_affiliate) {
        $affType  = $affUser->affiliate_commission_type ?: 'percent';
        $affValue = (float) ($affUser->affiliate_commission_value ?: 0);

        if ($affType === 'percent') {
            $affAmount = (int) round(($final * $affValue) / 100);
        } else {
            $affAmount = (int) round($affValue);
        }

        $affStatus = 'pending';
    } else {
        $affUserId = null;
        $affLinkId = null;
        $affRef = null;
    }
}
$userId = auth()->id() ?: User::where('email', $data['email'])->value('id');

        $order = Order::create([
            'invoice_number' => 'INV-' . date('YmdHis') . rand(1000, 9999),
            'type' => 'ship',
            'product_id' => $package->id,
            'product_name' => $package->title,
'user_id' => $userId,

            'promo_id' => $promoUsed?->id,
            'promo_code' => $promoUsed?->code,

            'customer_name' => $data['name'],
            'customer_email' => $data['email'],
            'customer_phone' => $data['phone'],

            // ship pakai field tour-style
            'departure_date' => $data['departure_date'],
            'participants' => $qty, // reuse kolom participants sebagai qty
'affiliate_user_id' => $affUserId,
'affiliate_link_id' => $affLinkId,
'affiliate_ref' => $affRef,
'affiliate_commission_type' => $affType,
'affiliate_commission_value' => $affValue,
'affiliate_commission_amount' => $affAmount,
'affiliate_commission_status' => $affStatus,
            'pickup_date' => null,
            'return_date' => null,
            'total_days' => null,

            'subtotal' => $subtotal,
            'discount' => $discount,
            'final_price' => $final,

            'payment_status' => 'waiting_payment',
            'order_status' => 'pending',
        ]);
if ($affLinkId) {
    AffiliateLink::where('id', $affLinkId)->increment('conversions');
}
        try {
            if (!empty($order->customer_email)) {
                Mail::to($order->customer_email)->send(new OrderInvoiceMail($order, false));
            }

            $adminEmail = Setting::invoiceAdminEmail();
            if (!empty($adminEmail) && $adminEmail !== $order->customer_email) {
                Mail::to($adminEmail)->send(new OrderInvoiceMail($order, true));
            }

            // Notifikasi ke Partner
            $payoutService = app(PartnerPayoutService::class);
            $partnerId = $payoutService->resolvePartnerIdFromOrder($order);
            if ($partnerId) {
                $partner = User::find($partnerId);
                if ($partner && $partner->email !== $order->customer_email) {
                    Mail::to($partner->email)->send(new PartnerOrderInvoiceMail($order, $partner));
                }
            }
        } catch (\Throwable $e) {
            Log::error('Invoice email gagal dikirim', [
                'invoice' => $order->invoice_number,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'redirect' => route('checkout.show', $order->id)
        ]);
    }
}
