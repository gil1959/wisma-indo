<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BankAccount;

class BookingController extends Controller
{
    public function show(Booking $booking)
    {
        // load relasi item
        $booking->load('items');

        $bankAccounts = BankAccount::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('front.booking.payment', [
            'booking'      => $booking,
            'items'        => $booking->items,
            'bankAccounts' => $bankAccounts,
        ]);
    }
}
