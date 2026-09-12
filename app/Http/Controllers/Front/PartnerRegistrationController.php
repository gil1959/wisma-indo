<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PartnerRegistration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PartnerRegistrationController extends Controller
{
    public function create()
    {
        return view('auth.register-partner');
    }

    public function store(Request $request)
    {
        $user = app(\App\Services\PartnerRegistrationService::class)->register($request);

        // Notify Admin
        $adminEmail = \App\Models\Setting::getValue('admin_notification_email');
        if (!empty($adminEmail)) {
            try {
                Mail::raw("Ada pendaftaran Agen Partner baru:\nNama: {$user->name}\nEmail: {$user->email}\nWaktu: " . now()->format('Y-m-d H:i:s'), function ($message) use ($adminEmail) {
                    $message->to($adminEmail)->subject('Pendaftaran Agen Partner Baru');
                });
            } catch (\Exception $e) { }
        }

        // JANGAN LOGIN OTOMATIS
        // Auth::login($user);

        return redirect()->route('partner.register.success')->with('registered_user', [
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function success()
    {
        if (!session()->has('registered_user')) {
            return redirect()->route('partner.register');
        }

        $waNumber = \App\Models\Setting::getValue('footer_whatsapp') ?? '6281234567890';
        $user = session('registered_user');
        
        $waText = "Halo admin Wismaindo,\n\nSaya baru saja mendaftar sebagai Partner.\n\nNama: {$user['name']}\nEmail: {$user['email']}\n\nMohon bantuannya untuk proses verifikasi akun saya. Terima kasih!";
        $waUrl = "https://wa.me/{$waNumber}?text=" . urlencode($waText);

        return view('auth.register-partner-success', compact('waUrl'));
    }
}
