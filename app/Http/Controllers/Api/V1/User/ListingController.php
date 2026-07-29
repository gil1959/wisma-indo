<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Listing;
use App\Http\Resources\ListingResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $listings = Listing::with('category')->where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(10);
        
        return ListingResource::collection($listings)->additional([
            'success' => true
        ]);
    }

    public function show(Request $request, $id)
    {
        $listing = Listing::with(['category', 'images'])->where('user_id', $request->user()->id)->findOrFail($id);
        
        return (new ListingResource($listing))->additional([
            'success' => true
        ]);
    }

    public function store(Request $request)
    {
        // Request validation logic can be moved to a Form Request
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:listing_categories,id',
            'listing_type' => 'required|in:dijual,disewakan',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'address' => 'required|string',
        ]);

        $user = $request->user();

        DB::beginTransaction();
        try {
            $listing = new Listing();
            $listing->user_id = $user->id;
            $listing->category_id = $request->category_id;
            $listing->title = $request->title;
            $listing->slug = Str::slug($request->title) . '-' . uniqid();
            $listing->type = 'properti'; // Hardcoded or dynamic
            $listing->listing_type = $request->listing_type;
            $listing->price = $request->price;
            $listing->description = $request->description;
            
            // Specifications
            $listing->land_area = $request->land_area;
            $listing->building_area = $request->building_area;
            $listing->bedrooms = $request->bedrooms;
            $listing->bathrooms = $request->bathrooms;
            $listing->certificate = $request->certificate;
            
            // Location
            $listing->city = $request->city;
            $listing->district = $request->district;
            $listing->address = $request->address;
            $listing->latitude = $request->latitude;
            $listing->longitude = $request->longitude;
            $listing->maps_url = $request->maps_url;
            
            $listing->status = 'pending';
            $listing->is_active = true;
            $listing->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Listing created successfully and pending approval.',
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
        
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $listing->update($request->only([
            'title', 'price', 'description', 'land_area', 'building_area',
            'bedrooms', 'bathrooms', 'certificate', 'city', 'district', 'address'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Listing updated successfully',
            'data' => new ListingResource($listing)
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $listing = Listing::where('user_id', $request->user()->id)->findOrFail($id);
        $listing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Listing deleted successfully'
        ]);
    }
}
