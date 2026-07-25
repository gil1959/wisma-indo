<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PartnerRegistration;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class PartnerRegistrationController extends Controller
{
    public function index()
    {
        $registrations = PartnerRegistration::with('user')->latest()->paginate(15);
        return view('admin.partner_registrations.index', compact('registrations'));
    }

    public function show($id)
    {
        $registration = PartnerRegistration::with('user')->findOrFail($id);
        return view('admin.partner_registrations.show', compact('registration'));
    }

    public function approve($id)
    {
        $registration = PartnerRegistration::findOrFail($id);
        
        if ($registration->status !== 'pending') {
            return back()->with('error', 'Status pengajuan sudah tidak pending.');
        }

        $registration->status = 'approved';
        $registration->save();

        $user = $registration->user;
        $user->syncRoles(['partner']); // Remove user role and assign partner role
        
        if (is_null($user->email_verified_at)) {
            $user->email_verified_at = \Carbon\Carbon::now();
            $user->save();
        }

        // Assign Free Package
        $freePackage = \App\Models\PartnerPackage::firstOrCreate(
            ['is_free' => true],
            [
                'name' => 'Paket Dasar (Free)',
                'description' => 'Paket bawaan untuk semua partner baru.',
                'price' => 0,
                'listing_quota' => 10,
                'duration_days' => 30,
            ]
        );

        // Update real user quota
        $userQuota = \App\Models\UserQuota::firstOrCreate(['user_id' => $user->id]);
        if ($freePackage->listing_quota != -1) {
            $userQuota->listing_quota = $freePackage->listing_quota;
        } else {
            $userQuota->listing_quota = -1; // unlimited
        }
        $userQuota->save();

        \App\Models\PartnerSubscription::create([
            'user_id' => $user->id,
            'partner_package_id' => $freePackage->id,
            'amount' => 0,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays($freePackage->duration_days)
        ]);

        // Send Email
        try {
            Mail::raw("Halo {$user->name},\n\nSelamat! Pengajuan Anda sebagai Agen Partner Wismaindo telah disetujui. Anda sekarang dapat mengakses Dashboard Agen Partner.", function ($message) use ($user) {
                $message->to($user->email)->subject('Pengajuan Agen Partner Disetujui');
            });
        } catch (\Exception $e) {}

        return back()->with('success', 'Pengajuan partner berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        $registration = PartnerRegistration::findOrFail($id);
        
        if ($registration->status !== 'pending') {
            return back()->with('error', 'Status pengajuan sudah tidak pending.');
        }

        $registration->status = 'rejected';
        $registration->rejection_note = $request->reason ?? $request->rejection_note;
        $registration->save();

        $user = $registration->user;

        // Send Email
        try {
            Mail::raw("Halo {$user->name},\n\nMohon maaf, pengajuan Anda sebagai Agen Partner Wismaindo ditolak dengan alasan:\n\n{$request->reason}\n\nSilakan perbaiki data Anda dan ajukan kembali.", function ($message) use ($user) {
                $message->to($user->email)->subject('Pengajuan Agen Partner Ditolak');
            });
        } catch (\Exception $e) {}

        return back()->with('success', 'Pengajuan partner berhasil ditolak.');
    }

    public function suspend(Request $request, $id)
    {
        $request->validate([
            'duration' => 'required|integer|min:1'
        ]);

        $registration = PartnerRegistration::findOrFail($id);
        $user = $registration->user;

        $user->is_suspended = true;
        // 9999 means permanent in our context
        if ($request->duration == 9999) {
            $user->suspended_until = \Carbon\Carbon::now()->addYears(100);
            $msgDuration = "secara permanen";
        } else {
            $user->suspended_until = \Carbon\Carbon::now()->addDays($request->duration);
            $msgDuration = "hingga " . $user->suspended_until->format('d M Y H:i');
        }
        $user->save();

        // Send Email
        try {
            Mail::raw("Halo {$user->name},\n\nAkun Partner Anda telah disuspend {$msgDuration} karena melanggar ketentuan layanan kami. Anda tidak dapat menggunakan fitur Partner selama masa suspend ini.", function ($message) use ($user) {
                $message->to($user->email)->subject('Pemberitahuan Suspend Akun Partner');
            });
        } catch (\Exception $e) {}

        return back()->with('success', 'Partner berhasil disuspend.');
    }

    public function unsuspend($id)
    {
        $registration = PartnerRegistration::findOrFail($id);
        $user = $registration->user;

        $user->is_suspended = false;
        $user->suspended_until = null;
        $user->save();

        // Send Email
        try {
            Mail::raw("Halo {$user->name},\n\nStatus suspend pada Akun Partner Anda telah dicabut. Anda sekarang dapat kembali menggunakan fitur Partner seperti biasa.", function ($message) use ($user) {
                $message->to($user->email)->subject('Pemberitahuan Pencabutan Suspend Akun Partner');
            });
        } catch (\Exception $e) {}

        return back()->with('success', 'Status suspend partner berhasil dicabut.');
    }

    public function destroy($id)
    {
        $registration = PartnerRegistration::findOrFail($id);
        $user = $registration->user;

        // Delete registration
        $registration->delete();

        // Completely delete user
        if ($user) {
            $user->delete();
        }

        return redirect()->route('admin.partner_registrations.index')->with('success', 'Akun partner berhasil dihapus permanen dari sistem.');
    }
}
