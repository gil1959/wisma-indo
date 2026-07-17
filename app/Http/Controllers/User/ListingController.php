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
        if (!$quota || $quota->listing_quota <= 0) {
            return redirect()->route('topup')->with('error', 'Kuota iklan Anda habis. Silakan beli paket Top Up untuk memasang iklan baru.');
        }

        $kategori = $request->query('kategori', 'properti');
        $categories = ListingCategory::where('type', $kategori == 'properti' ? 'property' : ($kategori == 'barang' ? 'goods' : 'services'))->get();
        return view('front.user.listings.create', compact('kategori', 'categories'));
    }

    public function store(Request $request)
    {
        $quota = Auth::user()->quota;
        if (!$quota || $quota->listing_quota <= 0) {
            return redirect()->route('topup')->with('error', 'Kuota iklan Anda habis. Silakan beli paket Top Up untuk memasang iklan baru.');
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
            'cover_image' => 'nullable|image|max:2048',
            'images.*' => 'nullable|image|max:2048',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        $validated['co_broke'] = $request->has('co_broke');
        $validated['negotiable'] = $request->has('negotiable');
        $validated['imb'] = $request->has('imb');
        $validated['pbb'] = $request->has('pbb');
        
        $category = ListingCategory::find($validated['listing_category_id']);
        $validated['category'] = $category->type;
        $validated['type'] = $category->type;
        $validated['status'] = 'pending';

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('public/listings');
            $validated['cover_image'] = Storage::url($path);
        }

        $listing = Listing::create($validated);

        // Deduct Quota
        $quota->decrement('listing_quota', 1);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                if ($index >= 12) break; // Max 12 images
                $path = $image->store('public/listings');
                ListingImage::create([
                    'listing_id' => $listing->id,
                    'image_path' => Storage::url($path),
                    'is_primary' => $index === 0,
                ]);
            }
        }

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
            'cover_image' => 'nullable|image|max:2048',
            'images.*' => 'nullable|image|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        $validated['co_broke'] = $request->has('co_broke');
        $validated['negotiable'] = $request->has('negotiable');
        $validated['imb'] = $request->has('imb');
        $validated['pbb'] = $request->has('pbb');
        
        $category = ListingCategory::find($validated['listing_category_id']);
        $validated['category'] = $category->type;
        $validated['type'] = $category->type;

        if ($listing->status === 'rejected') {
            $validated['status'] = 'pending';
        }

        if ($request->hasFile('cover_image')) {
            if ($listing->cover_image) {
                Storage::delete(str_replace('/storage/', 'public/', $listing->cover_image));
            }
            $path = $request->file('cover_image')->store('public/listings');
            $validated['cover_image'] = Storage::url($path);
        }

        $listing->update($validated);

        if ($request->has('delete_images')) {
            $imagesToDelete = \App\Models\ListingImage::whereIn('id', $request->delete_images)
                                ->where('listing_id', $listing->id)
                                ->get();
            foreach ($imagesToDelete as $img) {
                \Illuminate\Support\Facades\Storage::delete(str_replace('/storage/', 'public/', $img->image_path));
                $img->delete();
            }
        }

        if ($request->hasFile('images')) {
            // Option to replace all or add to existing. Let's just add new ones for now up to limit
            $currentImages = $listing->images()->count();
            foreach ($request->file('images') as $image) {
                if ($currentImages >= 12) break;
                $path = $image->store('public/listings');
                ListingImage::create([
                    'listing_id' => $listing->id,
                    'image_path' => Storage::url($path),
                    'is_primary' => $currentImages === 0,
                ]);
                $currentImages++;
            }
        }

        return redirect()->route('iklan.saya')->with('success', 'Iklan berhasil diperbarui!');
    }

    public function destroy(Listing $listing)
    {
        if ($listing->user_id != Auth::id()) abort(403);
        
        if ($listing->cover_image) {
            Storage::delete(str_replace('/storage/', 'public/', $listing->cover_image));
        }
        foreach($listing->images as $img) {
            Storage::delete(str_replace('/storage/', 'public/', $img->image_path));
        }
        
        $listing->delete();
        return redirect()->route('iklan.saya')->with('success', 'Iklan berhasil dihapus!');
    }
}
