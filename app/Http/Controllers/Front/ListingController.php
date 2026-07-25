<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Listing;

class ListingController extends Controller
{
    public function dijual(Request $request)
    {
        $query = Listing::where('status', 'tersedia')
                        ->where('type', 'property')
                        ->where('transaction_type', 'dijual');

        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('tipe')) {
            $query->where('property_type', $request->tipe);
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->filled('lokasi')) {
            $query->where('location', 'like', '%' . $request->lokasi . '%')->orWhere('address', 'like', '%' . $request->lokasi . '%');
        }
        
        $listings = $query->orderBy('is_premium', 'desc')->orderBy('bump_count', 'desc')->orderBy('bumped_at', 'desc')->latest()->paginate(12);

        return view('front.listings.index', ['type' => 'dijual', 'listings' => $listings]);
    }

    public function disewakan(Request $request)
    {
        $query = Listing::where('status', 'tersedia')
                        ->where('type', 'property')
                        ->where('transaction_type', 'disewa');

        if ($request->filled('q')) $query->where('title', 'like', '%' . $request->q . '%');
        if ($request->filled('tipe')) $query->where('property_type', $request->tipe);
        if ($request->filled('min_price')) $query->where('price', '>=', $request->min_price);
        if ($request->filled('max_price')) $query->where('price', '<=', $request->max_price);
        if ($request->filled('lokasi')) $query->where(function($q) use($request){ $q->where('location', 'like', '%'.$request->lokasi.'%')->orWhere('address', 'like', '%'.$request->lokasi.'%'); });
        
        $listings = $query->orderBy('is_premium', 'desc')->orderBy('bump_count', 'desc')->orderBy('bumped_at', 'desc')->latest()->paginate(12);

        return view('front.listings.index', ['type' => 'disewakan', 'listings' => $listings]);
    }

    public function properti(Request $request)
    {
        $query = Listing::where('status', 'tersedia')->where('type', 'property');

        if ($request->filled('q')) $query->where('title', 'like', '%' . $request->q . '%');
        if ($request->filled('transaksi')) $query->where('transaction_type', $request->transaksi);
        if ($request->filled('tipe')) $query->where('property_type', $request->tipe);
        if ($request->filled('min_price')) $query->where('price', '>=', $request->min_price);
        if ($request->filled('max_price')) $query->where('price', '<=', $request->max_price);
        if ($request->filled('lokasi')) $query->where(function($q) use($request){ $q->where('location', 'like', '%'.$request->lokasi.'%')->orWhere('address', 'like', '%'.$request->lokasi.'%'); });
        
        $listings = $query->orderBy('is_premium', 'desc')->orderBy('bump_count', 'desc')->orderBy('bumped_at', 'desc')->latest()->paginate(12);

        return view('front.listings.index', ['type' => 'properti', 'listings' => $listings]);
    }

    public function barangJasa(Request $request)
    {
        $kategori = $request->query('kategori', 'barang');
        $query = Listing::where('status', 'tersedia')
                        ->where('type', $kategori == 'barang' ? 'goods' : 'services');

        if ($request->filled('q')) $query->where('title', 'like', '%' . $request->q . '%');
        if ($request->filled('transaksi')) $query->where('transaction_type', $request->transaksi);
        if ($request->filled('min_price')) $query->where('price', '>=', $request->min_price);
        if ($request->filled('max_price')) $query->where('price', '<=', $request->max_price);
        if ($request->filled('lokasi')) $query->where(function($q) use($request){ $q->where('location', 'like', '%'.$request->lokasi.'%')->orWhere('address', 'like', '%'.$request->lokasi.'%'); });
        
        $listings = $query->orderBy('is_premium', 'desc')->orderBy('bump_count', 'desc')->orderBy('bumped_at', 'desc')->latest()->paginate(12);

        return view('front.listings.index', ['type' => 'barang-jasa', 'listings' => $listings]);
    }

    public function show($slug)
    {
        $listing = Listing::where('slug', $slug)->firstOrFail();
        
        // Increment views
        $listing->increment('views');
        
        // Cek status, kalau tidak tersedia dan bukan punya yang login atau bukan admin, 404
        if ($listing->status !== 'tersedia' && (!auth()->check() || (auth()->user()->id !== $listing->user_id && auth()->user()->role !== 'admin'))) {
            abort(404);
        }

        // Ambil listing terkait dari kategori yang sama
        $relatedListings = Listing::where('listing_category_id', $listing->listing_category_id)
            ->where('id', '!=', $listing->id)
            ->where('status', 'tersedia')
            ->orderBy('is_premium', 'desc')
            ->orderBy('bump_count', 'desc')
            ->orderBy('bumped_at', 'desc')
            ->latest()
            ->take(4)
            ->get();

        // Ambil iklan lain dari pembuat yang sama
        $userListings = Listing::where('user_id', $listing->user_id)
            ->where('id', '!=', $listing->id)
            ->where('status', 'tersedia')
            ->orderBy('is_premium', 'desc')
            ->orderBy('bump_count', 'desc')
            ->orderBy('bumped_at', 'desc')
            ->latest()
            ->take(4)
            ->get();

        // Rekomendasi Iklan
        $recommendedListings = Listing::where('id', '!=', $listing->id)
            ->where('status', 'tersedia')
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('front.listings.show', compact('listing', 'relatedListings', 'userListings', 'recommendedListings'));
    }

    public function userListings($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $listings = Listing::where('user_id', $id)
            ->where('status', 'tersedia')
            ->latest()
            ->paginate(12);
        
        return view('front.listings.user', compact('user', 'listings'));
    }
    public function storeLead(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'message' => 'required|string',
        ]);

        \App\Models\BuyerLead::create([
            'partner_id' => $listing->user_id,
            'listing_id' => $listing->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'message' => $request->message,
            'status' => 'new'
        ]);

        return back()->with('success', 'Pesan Anda berhasil dikirim ke Penjual.');
    }

    public function storeSurvey(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'survey_date' => 'required|date',
            'survey_time' => 'required',
            'message' => 'nullable|string',
        ]);

        \App\Models\SurveySchedule::create([
            'partner_id' => $listing->user_id,
            'listing_id' => $listing->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'survey_date' => $request->survey_date,
            'survey_time' => $request->survey_time,
            'message' => $request->message,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Jadwal survey berhasil diajukan.');
    }
}
