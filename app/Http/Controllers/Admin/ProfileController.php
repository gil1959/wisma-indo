<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('admin.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar'   => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048']
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                @unlink(public_path($user->avatar));
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = '/storage/' . $path;
        }

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        // kalau email berubah, biasanya verifikasi email direset
        if ($data['email'] !== $user->email) {
            $user->email_verified_at = null;
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
