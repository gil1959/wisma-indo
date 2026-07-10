<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\RentCarPackage;
use Illuminate\Http\Request;
use App\Models\RentCarCategory;


class RentCarController extends Controller
{
    /**
     * Menampilkan semua paket rent car
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $categoryId = $request->query('category_id');
        $sort = $request->query('sort', 'latest');

        $query = RentCarPackage::query()
            ->where('is_active', 1)
            ->with('category');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%");
            });
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if ($sort === 'price_asc') {
            $query->orderBy('price_per_12_hours', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price_per_12_hours', 'desc');
        } elseif ($sort === 'title_asc') {
            $query->orderBy('title', 'asc');
        } else {
            $query->latest();
        }

        $packages = $query->get();
        $categories = RentCarCategory::orderBy('name')->get();

        return view('front.rentcar.index', compact('packages', 'categories', 'q', 'categoryId', 'sort'));
    }


    /**
     * Menampilkan detail satu paket berdasarkan slug
     */
    public function show($slug)
    {
        $package = RentCarPackage::where('slug', $slug)->firstOrFail();

        return view('front.rentcar.show', compact('package'));
    }

    /**
     * POST setelah user pilih tanggal (TIDAK ke checkout)
     * cuma validasi & return data ke page lu sendiri.
     */
    public function postBooking(Request $request, $slug)
    {
        $request->validate([
            'pickup_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:pickup_date',
        ]);

        $package = RentCarPackage::where('slug', $slug)->firstOrFail();

        // hitung jam (ceil per jam)
        $start = new \Carbon\Carbon($request->pickup_date);
        $end   = new \Carbon\Carbon($request->return_date);

        $minutes = $start->diffInMinutes($end);
        $hours = max(1, (int) ceil($minutes / 60));
        $pricePerHour = round($package->price_per_12_hours / 12);
        $total = $hours * $pricePerHour;

        // UNTUK SEKARANG:
        // CUMA RETURN VIEW SEMENTARA (belum buat halaman pembayaran)
        return view('front.rentcar.temp-booking', [
            'package' => $package,
            'pickup'  => $request->pickup_date,
            'return'  => $request->return_date,
            'hours'   => $hours,
            'total'   => $total,
        ]);
    }
}
