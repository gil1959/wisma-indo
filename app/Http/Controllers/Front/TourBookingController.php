<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use App\Models\Booking;
use App\Models\BookingItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TourBookingController extends Controller
{
    public function store(Request $request, TourPackage $tourPackage)
    {
        // 1. Validasi input
        $data = $request->validate([
            'customer_name'   => ['required', 'string', 'max:255'],
            'customer_email'  => ['required', 'email', 'max:255'],
            'customer_phone'  => ['required', 'string', 'max:30'],
            'pax'             => ['required', 'integer', 'min:1'],
            'audience_type'   => ['required', 'in:domestic,wna'],
            'start_date'      => ['required', 'date'],
            'with_flight'     => ['nullable', 'boolean'],
            'notes'           => ['nullable', 'string'],
        ]);

        $pax          = (int) $data['pax'];
        $audienceType = $data['audience_type'];
        $withFlight   = $request->boolean('with_flight');

        // 2. Cari tier harga sesuai pax
        $tierQuery = $tourPackage->priceTiers()
            ->where('audience_type', $audienceType)
            ->orderBy('min_pax');

        $tier = (clone $tierQuery)
            ->where('min_pax', '<=', $pax)
            ->where('max_pax', '>=', $pax)
            ->first();

        if (! $tier) {
            $tier = $tierQuery->orderByDesc('max_pax')->first();
        }

        if (! $tier) {
            return back()
                ->withInput()
                ->withErrors(['pax' => 'Belum ada harga untuk jumlah peserta ini.']);
        }

        // 3. Hitung harga per pax
        $basePerPax = (float) $tier->price_per_pax;

        if ($withFlight) {

            if (! is_null($tier->price_with_flight_per_pax)) {
                // tier punya harga khusus "dengan tiket"
                $unitPrice = (float) $tier->price_with_flight_per_pax;
            } elseif (! is_null($tourPackage->flight_surcharge_per_pax)) {
                // tidak ada harga khusus per tier, pakai base + surcharge per pax
                $unitPrice = $basePerPax + (float) $tourPackage->flight_surcharge_per_pax;
            } else {
                // fallback: tiket belum pasti -> pakai base dulu, tiket dikonfirmasi admin
                $unitPrice = $basePerPax;
            }
        } else {
            // tanpa tiket pesawat
            $unitPrice = $basePerPax;
        }

        $subtotal = $unitPrice * $pax;
        $total    = $subtotal;
        $discount = 0;

        // 4. Notes (tambahkan flag kalau tiket belum pasti)
        $notes = $data['notes'] ?? null;
        if (
            $withFlight
            && is_null($tier->price_with_flight_per_pax)
            && is_null($tourPackage->flight_surcharge_per_pax)
        ) {
            // supaya nanti view bisa munculin warning "harga tiket via WA"
            $flag  = '[KONFIRMASI HARGA TIKET]';
            $notes = $notes ? $notes . ' ' . $flag : $flag;
        }

        // 5. SIMPAN BOOKING
        $booking = Booking::create([
            'code'            => Str::upper(Str::random(8)),
            'type'            => 'tour',
            'status'          => 'waiting_payment',
            'payment_status'  => 'unpaid',

            'customer_name'   => $data['customer_name'],
            'customer_email'  => $data['customer_email'],
            'customer_phone'  => $data['customer_phone'],

            'total_amount'    => $total,
            'discount_amount' => $discount,
            'final_amount'    => $total - $discount,

            'with_flight'     => $withFlight,
            'notes'           => $notes,
        ]);

        // 6. SIMPAN ITEM BOOKING
        BookingItem::create([
            'booking_id'    => $booking->id,
            'item_type'     => 'tour',
            'item_id'       => $tourPackage->id,
            'audience_type' => $audienceType,
            'qty'           => $pax,
            'unit_price'    => $unitPrice,
            'subtotal'      => $subtotal,
            'start_date'    => $data['start_date'],
            'end_date'      => null,
        ]);

        // 7. Redirect ke halaman pembayaran
        return redirect()
            ->route('booking.show', $booking)
            ->with('success', 'Pesanan berhasil dibuat. Silakan lanjut ke pembayaran.');
    }
}
