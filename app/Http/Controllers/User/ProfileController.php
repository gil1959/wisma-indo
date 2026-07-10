<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('user.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'        => ['nullable', 'string', 'max:30'],
            'full_address' => ['nullable', 'string'],
            'sub_district' => ['nullable', 'string', 'max:120'],

            // password opsional
            'password'     => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Update data profile
        $user->name         = $validated['name'];
        $user->email        = $validated['email'];
        $user->phone        = $validated['phone'] ?? null;
        $user->full_address = $validated['full_address'] ?? null;
        $user->sub_district = $validated['sub_district'] ?? null;

        // Update password kalau diisi
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $isEn = app()->getLocale() === 'en';
        return back()->with('status', $isEn ? 'Profile updated.' : 'Profil berhasil diperbarui.');
    }
}
