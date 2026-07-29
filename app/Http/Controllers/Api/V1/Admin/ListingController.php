<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Listing;
use App\Http\Resources\ListingResource;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $query = Listing::with(['category', 'user']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $listings = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return ListingResource::collection($listings)->additional(['success' => true]);
    }

    public function approve(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);
        $listing->status = 'approved';
        $listing->is_active = true;
        $listing->save();

        // Need to decrement quota logic if not yet decremented, but usually handled in store.

        return response()->json([
            'success' => true,
            'message' => 'Listing approved successfully'
        ]);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_note' => 'required|string'
        ]);

        $listing = Listing::findOrFail($id);
        $listing->status = 'rejected';
        $listing->rejection_note = $request->rejection_note;
        $listing->is_active = false;
        $listing->save();

        return response()->json([
            'success' => true,
            'message' => 'Listing rejected successfully'
        ]);
    }
}
