<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Listing;
use App\Models\ListingCategory;
use App\Models\ListingImage;
use App\Http\Resources\ListingResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $listings = Listing::with('listingCategory')->where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(10);
        
        return ListingResource::collection($listings)->additional([
            'success' => true
        ]);
    }

    public function show(Request $request, $id)
    {
        $listing = Listing::with(['listingCategory', 'images'])->where('user_id', $request->user()->id)->findOrFail($id);
        
        return (new ListingResource($listing))->additional([
            'success' => true
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $quota = $user->quota;
        if (!$quota || $quota->listing_quota <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kuota iklan habis. Silakan topup terlebih dahulu.'
            ], 403);
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
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
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
        ]);

        $validated['user_id'] = $user->id;
        $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        $validated['co_broke'] = $request->has('co_broke');
        $validated['negotiable'] = $request->has('negotiable');
        $validated['imb'] = $request->has('imb');
        $validated['pbb'] = $request->has('pbb');
        
        $category = ListingCategory::find($validated['listing_category_id']);
        $validated['category'] = $category->type;
        $validated['type'] = $category->type;
        $validated['status'] = 'tersedia';

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('listings', 'public');
            $validated['cover_image'] = Storage::url($path);
        }

        DB::beginTransaction();
        try {
            $listing = Listing::create($validated);
            $quota->decrement('listing_quota', 1);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Listing created successfully.',
                'data' => new ListingResource($listing)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create listing: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $listing = Listing::where('user_id', $request->user()->id)->findOrFail($id);
        
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
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
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
        ]);

        $validated['co_broke'] = $request->has('co_broke');
        $validated['negotiable'] = $request->has('negotiable');
        $validated['imb'] = $request->has('imb');
        $validated['pbb'] = $request->has('pbb');
        
        $category = ListingCategory::find($validated['listing_category_id']);
        $validated['category'] = $category->type;
        $validated['type'] = $category->type;

        if ($listing->status === 'rejected') {
            $validated['status'] = 'tersedia';
        }

        if ($request->hasFile('cover_image')) {
            if ($listing->cover_image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $listing->cover_image));
            }
            $path = $request->file('cover_image')->store('listings', 'public');
            $validated['cover_image'] = Storage::url($path);
        }

        $listing->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Listing updated successfully',
            'data' => new ListingResource($listing)
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $listing = Listing::where('user_id', $request->user()->id)->findOrFail($id);
        
        if ($listing->cover_image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $listing->cover_image));
        }
        foreach($listing->images as $img) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $img->image_path));
        }
        
        $listing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Listing deleted successfully'
        ]);
    }
}
