<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use App\Models\PromoUserUsage;
use Illuminate\Http\Request;

class PromoValidatorController extends Controller
{
    public function validatePromo(Request $request)
    {
        $request->validate([
            'code'  => 'required|string',
            'price' => 'required|numeric|min:0',
            'email' => 'nullable|email',
        ]);


        $promo = Promo::where('code', strtoupper($request->code))
            ->where('is_active', true)
            ->first();

        $isEn = app()->getLocale() === 'en';

        if (!$promo) {
            return response()->json([
                'valid' => false,
                'message' => $isEn ? 'Promo code not found.' : 'Kode promo tidak ditemukan.'
            ]);
        }

        // Check usage (once per user)
        if ($request->filled('email')) {
            $alreadyUsed = \App\Models\Order::where('customer_email', $request->email)
                ->where('promo_id', $promo->id)
                ->exists();


            if ($alreadyUsed) {
                return response()->json([
                    'valid' => false,
                    'message' => $isEn ? 'This promo code has already been used for this email.' : 'Kode promo sudah pernah digunakan untuk email ini.'
                ]);
            }
        }


        // hitung diskon
        $basePrice = $request->price;
        $discount = 0;

        if ($promo->type === 'nominal') {
            $discount = $promo->value;
        } else {
            $discount = ($basePrice * $promo->value / 100);
        }

        $final = max(0, $basePrice - $discount);

        return response()->json([
            'valid' => true,
            'promo_id' => $promo->id,
            'original_price' => $basePrice,
            'discount' => $discount,
            'final_price' => $final,
        ]);
    }
}
