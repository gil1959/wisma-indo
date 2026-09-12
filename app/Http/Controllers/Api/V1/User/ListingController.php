<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveListingRequest;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use App\Models\ListingCategory;
use App\Services\ListingWriter;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function __construct()
    {
        // Match the verified email requirement on the web's listing forms.
        $this->middleware('verified')->only(['form', 'store', 'update', 'destroy']);
        $this->middleware(\App\Http\Middleware\EnsureMobilePartnerSubscription::class)->only('index');
    }

    public function form(Request $request)
    {
        $remaining = (int) optional($request->user()->quota)->listing_quota;
        return response()->json(['success' => true, 'data' => [
            'listing_quota' => $remaining,
            'can_create' => $remaining > 0 || $remaining === -1,
            'categories' => ListingCategory::whereIn('type', ['property', 'goods', 'services'])->orderBy('name')->get(['id', 'name', 'type']),
            'phone' => $request->user()->phone,
            'max_gallery_images' => 18,
            'max_image_size_mb' => 20,
        ]]);
    }

    public function index(Request $request)
    {
        return ListingResource::collection(Listing::with(['listingCategory', 'images'])
            ->where('user_id', $request->user()->id)->latest()->paginate(10))->additional(['success' => true]);
    }

    public function show(Request $request, $id)
    {
        return (new ListingResource(Listing::with(['listingCategory', 'images'])
            ->where('user_id', $request->user()->id)->findOrFail($id)))->additional(['success' => true]);
    }

    public function store(SaveListingRequest $request, ListingWriter $writer)
    {
        $listing = $writer->save($request->user()->id, $request->validated(), $request);
        return response()->json(['success' => true, 'message' => 'Iklan berhasil ditambahkan.', 'data' => new ListingResource($listing)], 201);
    }

    public function update(SaveListingRequest $request, ListingWriter $writer, $id)
    {
        $listing = Listing::where('user_id', $request->user()->id)->findOrFail($id);
        $listing = $writer->save($request->user()->id, $request->validated(), $request, $listing);
        return response()->json(['success' => true, 'message' => 'Iklan berhasil diperbarui.', 'data' => new ListingResource($listing)]);
    }

    public function destroy(Request $request, ListingWriter $writer, $id)
    {
        $writer->delete($request->user()->id, (int) $id);
        return response()->json(['success' => true, 'message' => 'Iklan berhasil dihapus.']);
    }
}
