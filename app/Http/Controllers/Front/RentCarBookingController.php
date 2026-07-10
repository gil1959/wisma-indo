<?php


namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RentCarPackage;
use App\Models\Order;
use App\Models\Promo;
use Carbon\Carbon;


class RentCarOrderController extends Controller
{


    public function draft(Request $request, $slug)
    {

        $package = RentCarPackage::where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'name'   => 'required|string|max:120',
            'email'  => 'required|email',
            'phone'  => 'required|string|max:50',
            'pickup' => 'required|date',
            'return' => 'required|date|after_or_equal:pickup',
            'promo_id' => 'nullable|integer',
        ]);

        // ===================== HITUNG DURASI ======================
        // ===================== HITUNG DURASI (PER JAM) ======================
        $start = Carbon::parse($data['pickup']);
        $end   = Carbon::parse($data['return']);

        $minutes = $start->diffInMinutes($end);
        $hours = max(1, (int) ceil($minutes / 60));

        $subtotal = $hours * $package->price_per_hour;

        // ===================== PROMO ======================
        $discount = 0;
        if ($data['promo_id']) {
            $promo = Promo::find($data['promo_id']);
            if ($promo && $promo->is_valid_for($subtotal)) {
                $discount = $promo->calculate_discount($subtotal);
            }
        }

        $final = $subtotal - $discount;

        // ===================== SIMPAN ORDER ======================
        $order = Order::create([
            'invoice_number' => 'INV-' . date('YmdHis') . rand(1000, 9999),
            'type'           => 'rent_car',
            'product_id'     => $package->id,
            'product_name'   => $package->title,
            'user_id'        => auth()->check() ? auth()->id() : null,
            'customer_name'  => $data['name'],
            'customer_email' => $data['email'],
            'customer_phone' => $data['phone'],
            'pickup_date'  => $data['pickup'],
            'return_date'  => $data['return'],

            'departure_date' => null,
            'participants'   => null,

            'total_days'     => null,
            'total_hours'    => $hours,

            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'final_price'    => $final,

            'payment_status' => 'waiting_payment',
            'order_status'   => 'pending',
        ]);

        return response()->json([
            'redirect' => route('checkout', $order->id)
        ]);
    }
}
