<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Cek apakah user dengan email ini sudah ada
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Update google_id kalau belum ada
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
                
                // Login
                Auth::login($user);
            } else {
                // Buat user baru
                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(16)), // Password acak
                    'email_verified_at' => now(), // Anggap email dari Google sudah verified
                    'avatar' => $googleUser->getAvatar(),
                ]);

                // Berikan role user biasa jika menggunakan Spatie Permission
                if (class_exists(\Spatie\Permission\Models\Role::class)) {
                    $newUser->assignRole('user'); // asumsikan role default adalah 'user'
                }

                Auth::login($newUser);
            }

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Terjadi kesalahan saat login dengan Google: ' . $e->getMessage());
        }
    }
}
