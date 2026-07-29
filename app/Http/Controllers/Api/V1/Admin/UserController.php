<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'quota']);

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $users = $query->paginate(15);
        return UserResource::collection($users)->additional(['success' => true]);
    }

    public function show($id)
    {
        $user = User::with(['roles', 'quota'])->findOrFail($id);
        return (new UserResource($user))->additional(['success' => true]);
    }

    public function toggleQuota(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $quota = $user->quota;
        if ($quota) {
            $quota->has_free_quota = !$quota->has_free_quota;
            $quota->save();
        } else {
            $user->quota()->create([
                'listing_quota' => 2,
                'has_free_quota' => true
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User free quota status toggled successfully'
        ]);
    }
}
