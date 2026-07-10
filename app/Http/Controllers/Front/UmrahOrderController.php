<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UmrahPackage;
use App\Models\Order;
use App\Models\Promo;

use App\Models\User;
use App\Models\AffiliateLink;
class UmrahOrderController extends Controller
{
    public function draft(Request $request, $slug)
    {
        $package = UmrahPackage::with('tiers')->where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email',
            'phone' => 'required|string|max:50',
            'booking_date' => 'required|date|after_or_equal:today',

            'tier_id' => 'required|integer',
            'participants' => 'required|integer|min:1|max:999',

            'promo_id' => 'nullable|integer',
        ]);

        $tier = $package->tiers->firstWhere('id', (int)$data['tier_id']);
        if (!$tier) {
            return response()->json(['error' => 'Harga tidak valid.'], 422);
        }

        $subtotal = (int)$tier->price * (int)$data['participants'];

        // Promo
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

        $final = $subtotal - $discount;
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
    'type'           => 'umrah',
    'product_id'     => $package->id,
    'product_name'   => $package->title,
'user_id' => $userId,

    'promo_id'       => $promoUsed?->id,
    'promo_code'     => $promoUsed?->code,
'affiliate_user_id' => $affUserId,
'affiliate_link_id' => $affLinkId,
'affiliate_ref' => $affRef,
'affiliate_commission_type' => $affType,
'affiliate_commission_value' => $affValue,
'affiliate_commission_amount' => $affAmount,
'affiliate_commission_status' => $affStatus,
    'customer_name'  => $data['name'],
    'customer_email' => $data['email'],
    'customer_phone' => $data['phone'],

    // Umrah disimpan pakai field "departure_date" biar konsisten sama schema orders
    'departure_date' => $data['booking_date'],
    'participants'   => (int) $data['participants'],

    // field rentcar nullable
    'pickup_date'    => null,
    'return_date'    => null,
    'total_days'     => null,

    'subtotal'       => $subtotal,
    'discount'       => $discount,

    // PENTING: di tabel kolomnya "final_price", bukan "total"
    'final_price'    => $final,

    // PENTING: kolom ini wajib (enum non-null) di migration orders
    'payment_status' => 'waiting_payment',
    'order_status'   => 'pending',
]);
if ($affLinkId) {
    AffiliateLink::where('id', $affLinkId)->increment('conversions');
}


       return response()->json([
    'ok' => true,
    'redirect' => route('checkout.show', $order->id),
]);


    }
}
