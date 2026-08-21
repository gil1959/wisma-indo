<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user())
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'sometimes|required|string|max:20|unique:users,phone,' . $user->id,
            'whatsapp_template' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
            'address' => 'nullable|string',
            'full_address' => 'nullable|string',
            'sub_district' => 'nullable|string',
            'bio' => 'nullable|string',
        ]);

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        $emailChanged = false;
        if ($request->has('email') && $request->email !== $user->email) {
            $user->email = $request->email;
            $user->email_verified_at = null;
            $emailChanged = true;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        
        if ($request->has('whatsapp_template')) {
            $user->whatsapp_template = $request->whatsapp_template;
        }
        if ($request->has('address')) {
            $user->address = $request->address;
        }
        if ($request->has('full_address')) {
            $user->full_address = $request->full_address;
        }
        if ($request->has('sub_district')) {
            $user->sub_district = $request->sub_district;
        }
        if ($request->has('bio')) {
            $user->bio = $request->bio;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
            if ($user->currentAccessToken()) {
                $user->currentAccessToken()->delete();
            }
            return response()->json([
                'success' => true,
                'email_changed' => true,
                'message' => 'Email Anda telah diperbarui. Silakan masuk kembali dan periksa kotak masuk email Anda untuk verifikasi alamat email baru.',
                'data' => new UserResource($user)
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => new UserResource($user)
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Avatar updated successfully',
            'data' => new UserResource($user)
        ]);
    }
}
