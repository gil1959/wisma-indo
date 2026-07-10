<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\PartnerApplicationSubmittedAdmin;
use App\Mail\PartnerApplicationSubmittedUser;
use App\Models\PartnerApplication;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;

class PartnerRegistrationController extends Controller
{
    public function create()
    {
        return view('partner.register');
    }

    public function pending()
    {
        return view('partner.pending');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'reason' => ['required', 'string', 'min:10'],
'partner_type' => ['required', 'in:agency_paket_tour,agency_rental_mobil,agency_restoran,agency_hotel_vila'],
        'bank_name' => ['required', 'string', 'max:100'],
        'bank_account_number' => ['required', 'string', 'max:50'],
        'bank_account_holder' => ['required', 'string', 'max:100'],
            'identity_type' => ['required', 'string', 'in:KTP,SIM,PASPOR,KK'],
            'identity_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            
            'legal_document' => ['required', 'file', 'mimes:pdf', 'max:10240'], // 10MB

        ]);

        // Cegah double submit untuk email yang sama (kalau masih pending)
        $existsPending = PartnerApplication::where('email', $data['email'])
            ->where('status', 'pending')
            ->exists();
        if ($existsPending) {
            return redirect()->route('partner.pending')
                ->with('success', 'Pendaftaran kamu sudah masuk dan sedang diverifikasi.');
        }

        // Upload
        // NOTE: pastikan sudah `php artisan storage:link`
        $identityPath = $request->file('identity_file')->store('partners/identity', 'public');
       
$legalPath = $request->file('legal_document')->store('partners/legal-documents', 'public');
        $app = PartnerApplication::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'reason' => $data['reason'],
            'identity_type' => $data['identity_type'],
            'identity_file_path' => $identityPath,
            
'legal_document_path' => $legalPath,
            'password_hash' => Hash::make($data['password']),
            'password_enc'  => Crypt::encryptString($data['password']),

            'status' => 'pending',
            'submitted_at' => now(),
            'partner_type' => $data['partner_type'],
'bank_name' => $data['bank_name'],
'bank_account_number' => $data['bank_account_number'],
'bank_account_holder' => $data['bank_account_holder'],

        ]);

        // Email notif
        $adminEmail = Setting::invoiceAdminEmail();
        if ($adminEmail) {
            Mail::to($adminEmail)->send(new PartnerApplicationSubmittedAdmin($app));
        }
        Mail::to($app->email)->send(new PartnerApplicationSubmittedUser($app));

        return redirect()->route('partner.pending')
            ->with('success', 'Pendaftaran berhasil dikirim. Tim admin akan memverifikasi data kamu.');
    }
}
