<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('front.user.profile', compact('user'));
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
            'bio'          => ['nullable', 'string', 'max:500'],
            'password'     => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar'       => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048']
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                @unlink(public_path($user->avatar));
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = '/storage/' . $path;
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Update data profile
        $user->name         = $validated['name'];
        $user->email        = $validated['email'];
        $user->phone        = $validated['phone'] ?? null;
        $user->full_address = $validated['full_address'] ?? null;
        $user->sub_district = $validated['sub_district'] ?? null;
        $user->bio          = $validated['bio'] ?? null;

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($user->wasChanged('email')) {
            $user->sendEmailVerificationNotification();
            auth('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('status', 'Email Anda telah diperbarui. Silakan masuk kembali dan periksa kotak masuk email Anda untuk verifikasi alamat email baru.');
        }

        return back()->with('status', 'Profil berhasil diperbarui.');
    }

    public function sendPasswordResetLink(Request $request)
    {
        // Check if the user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // We use the password broker to send the link
        $status = Password::broker()->sendResetLink(
            ['email' => auth()->user()->email]
        );

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', 'Link reset password berhasil dikirim ke email Anda. Silakan cek kotak masuk atau folder spam.');
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
