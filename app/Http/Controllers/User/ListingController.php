<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\ListingCategory;
use App\Models\ListingImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ListingController extends Controller
{
    public function index()
    {
        $listings = Listing::where('user_id', Auth::id())->latest()->get();
        return view('front.user.listings.index', compact('listings'));
    }

    public function create(Request $request)
    {
        $quota = Auth::user()->quota;
        if (!$quota || ($quota->listing_quota <= 0 && (int) $quota->listing_quota !== -1)) {
            return redirect()->route('topup')->with('error', 'Klaim diskon anda sekarang. Manfaatkan Voucher Promo Spesial di bawah untuk top up dengan harga lebih hemat!');
        }

        $kategori = $request->query('kategori', 'properti');
        $categories = ListingCategory::where('type', $kategori == 'properti' ? 'property' : ($kategori == 'barang' ? 'goods' : 'services'))->get();
        return view('front.user.listings.create', compact('kategori', 'categories'));
    }

    public function store(Request $request)
    {
        $quota = Auth::user()->quota;
        if (!$quota || ($quota->listing_quota <= 0 && (int) $quota->listing_quota !== -1)) {
            return redirect()->route('topup')->with('error', 'Klaim diskon anda sekarang. Manfaatkan Voucher Promo Spesial di bawah untuk top up dengan harga lebih hemat!');
        }

        $validated = $request->validate([
            'listing_category_id' => 'required|exists:listing_categories,id',
            'transaction_type' => 'nullable|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'currency' => 'nullable|string',
            'rental_period' => 'nullable|string',
            'min_rental' => 'nullable|string',
            'price_type' => 'nullable|string',
            'co_broke' => 'nullable|boolean',
            'negotiable' => 'nullable|boolean',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'maps_url' => 'nullable|string',
            'property_type' => 'nullable|string',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'land_area' => 'nullable|integer',
            'building_area' => 'nullable|integer',
            'floors' => 'nullable|integer',
            'certificate' => 'nullable|string',
            'imb' => 'nullable|boolean',
            'pbb' => 'nullable|boolean',
            'latitude' => 'nullable|required_with:longitude|numeric|between:-90,90',
            'longitude' => 'nullable|required_with:latitude|numeric|between:-180,180',
            'electricity' => 'nullable|integer',
            'maid_bedrooms' => 'nullable|integer',
            'maid_bathrooms' => 'nullable|integer',
            'car_access' => 'nullable|string',
            'water_source' => 'nullable|string',
            'facing_direction' => 'nullable|string',
            'build_year' => 'nullable|string',
            'carport' => 'nullable|integer',
            'garage' => 'nullable|integer',
            'furnished_status' => 'nullable|string',
            'facilities' => 'nullable|array',
            'surroundings' => 'nullable|array',
            'condition' => 'nullable|string',
            'brand' => 'nullable|string',
            'service_area' => 'nullable|string',
            'phone' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'youtube_url' => 'nullable|string',
            'cover_image' => 'nullable|image|max:20480',
            'images' => 'nullable|array|max:18',
            'images.*' => 'nullable|image|max:20480',
            'delete_images' => 'nullable|array|max:18',
            'delete_images.*' => 'integer',
        ]);

        app(\App\Services\ListingWriter::class)->save(Auth::id(), $validated, $request);

        return redirect()->route('iklan.saya')->with('success', 'Iklan berhasil ditambahkan!');
    }

    public function edit(Listing $listing)
    {
        if ($listing->user_id != Auth::id()) abort(403);
        $kategori = $listing->type == 'property' ? 'properti' : ($listing->type == 'goods' ? 'barang' : 'jasa');
        $categories = ListingCategory::where('type', $listing->type)->get();
        return view('front.user.listings.edit', compact('listing', 'categories', 'kategori'));
    }

    public function update(Request $request, Listing $listing)
    {
        if ($listing->user_id != Auth::id()) abort(403);

        $validated = $request->validate([
            'listing_category_id' => 'required|exists:listing_categories,id',
            'transaction_type' => 'nullable|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'currency' => 'nullable|string',
            'rental_period' => 'nullable|string',
            'min_rental' => 'nullable|string',
            'price_type' => 'nullable|string',
            'co_broke' => 'nullable|boolean',
            'negotiable' => 'nullable|boolean',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'maps_url' => 'nullable|string',
            'property_type' => 'nullable|string',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'land_area' => 'nullable|integer',
            'building_area' => 'nullable|integer',
            'floors' => 'nullable|integer',
            'certificate' => 'nullable|string',
            'imb' => 'nullable|boolean',
            'pbb' => 'nullable|boolean',
            'latitude' => 'nullable|required_with:longitude|numeric|between:-90,90',
            'longitude' => 'nullable|required_with:latitude|numeric|between:-180,180',
            'electricity' => 'nullable|integer',
            'maid_bedrooms' => 'nullable|integer',
            'maid_bathrooms' => 'nullable|integer',
            'car_access' => 'nullable|string',
            'water_source' => 'nullable|string',
            'facing_direction' => 'nullable|string',
            'build_year' => 'nullable|string',
            'carport' => 'nullable|integer',
            'garage' => 'nullable|integer',
            'furnished_status' => 'nullable|string',
            'facilities' => 'nullable|array',
            'surroundings' => 'nullable|array',
            'condition' => 'nullable|string',
            'brand' => 'nullable|string',
            'service_area' => 'nullable|string',
            'phone' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'youtube_url' => 'nullable|string',
            'cover_image' => 'nullable|image|max:20480',
            'images' => 'nullable|array|max:18',
            'images.*' => 'nullable|image|max:20480',
            'delete_images' => 'nullable|array|max:18',
            'delete_images.*' => 'integer',
        ]);

        foreach (['co_broke', 'negotiable', 'imb', 'pbb'] as $key) $validated[$key] = $request->boolean($key);
        app(\App\Services\ListingWriter::class)->save(Auth::id(), $validated, $request, $listing);

        return redirect()->route('iklan.saya')->with('success', 'Iklan berhasil diperbarui!');
    }

    public function destroy(Listing $listing)
    {
        if ($listing->user_id != Auth::id()) abort(403);
        
        app(\App\Services\ListingWriter::class)->delete(Auth::id(), $listing->id);
        return redirect()->route('iklan.saya')->with('success', 'Iklan berhasil dihapus!');
    }

}
