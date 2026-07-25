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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'ktp_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'foto_file' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'npwp_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'lisensi_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // Default role for new registration is 'user' until approved
        $user->assignRole('user');

        $registration = new PartnerRegistration();
        $registration->user_id = $user->id;

        if ($request->hasFile('ktp_file')) {
            $path = $request->file('ktp_file')->store('partners/ktp', 'public');
            $registration->ktp_file = 'storage/' . $path;
        }
        if ($request->hasFile('foto_file')) {
            $path = $request->file('foto_file')->store('partners/foto', 'public');
            $registration->foto_file = 'storage/' . $path;
        }
        if ($request->hasFile('npwp_file')) {
            $path = $request->file('npwp_file')->store('partners/npwp', 'public');
            $registration->npwp_file = 'storage/' . $path;
        }
        if ($request->hasFile('lisensi_file')) {
            $path = $request->file('lisensi_file')->store('partners/lisensi', 'public');
            $registration->lisensi_file = 'storage/' . $path;
        }

        $registration->save();

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

        $waNumber = \App\Models\Setting::getValue('whatsapp_number') ?? '6281234567890';
        $user = session('registered_user');
        
        $waText = "Halo admin Wismaindo,\n\nSaya baru saja mendaftar sebagai Partner.\n\nNama: {$user['name']}\nEmail: {$user['email']}\n\nMohon bantuannya untuk proses verifikasi akun saya. Terima kasih!";
        $waUrl = "https://wa.me/{$waNumber}?text=" . urlencode($waText);

        return view('auth.register-partner-success', compact('waUrl'));
    }
}
