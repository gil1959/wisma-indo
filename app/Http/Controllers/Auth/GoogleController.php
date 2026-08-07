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

                // Berikan 1 free listing quota untuk user baru (sama seperti register manual)
                \App\Models\UserQuota::create([
                    'user_id' => $newUser->id,
                    'listing_quota' => 1
                ]);

                Auth::login($newUser);
            }
            $loggedInUser = Auth::user();

            if ($loggedInUser && ($loggedInUser->hasRole('admin') || $loggedInUser->hasRole('site_moderator'))) {
                return redirect()->route('admin.dashboard');
            }

            if ($loggedInUser) {
                $partnerReg = \App\Models\PartnerRegistration::where('user_id', $loggedInUser->id)->first();
                if ($partnerReg && $partnerReg->status === 'pending') {
                    Auth::logout();
                    request()->session()->invalidate();
                    request()->session()->regenerateToken();

                    return redirect()->route('partner.register.success')->with('registered_user', [
                        'name' => $loggedInUser->name,
                        'email' => $loggedInUser->email,
                    ]);
                }
            }

            if ($loggedInUser && $loggedInUser->hasRole('partner')) {
                return redirect()->route('partner.statistics');
            }

            return redirect('/akun');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Terjadi kesalahan saat login dengan Google: ' . $e->getMessage());
        }
    }
}
