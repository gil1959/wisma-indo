<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // default role untuk registrasi
        $user->assignRole('user');

        // default free quota
        \App\Models\UserQuota::create([
            'user_id' => $user->id,
            'listing_quota' => 1
        ]);
        event(new Registered($user));
        
        $adminEmail = \App\Models\Setting::getValue('admin_notification_email');
        if (!empty($adminEmail)) {
            try {
                \Illuminate\Support\Facades\Mail::raw("Ada pengguna baru mendaftar:\nNama: {$user->name}\nEmail: {$user->email}\nWaktu: " . now()->format('Y-m-d H:i:s'), function ($message) use ($adminEmail) {
                    $message->to($adminEmail)->subject('Notifikasi Pendaftaran Baru');
                });
            } catch (\Exception $e) { }
        }

        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
