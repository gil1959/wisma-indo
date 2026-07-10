<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PartnerApplicationApprovedAdmin;
use App\Mail\PartnerApplicationApprovedUser;
use App\Mail\PartnerApplicationRejectedAdmin;
use App\Mail\PartnerApplicationRejectedUser;
use App\Models\PartnerApplication;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
class PartnerApplicationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending'); // pending|approved|rejected
        $q = trim((string) $request->get('q'));

        $applications = PartnerApplication::query()
            ->where('status', $status)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('submitted_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.partners.applications.index', compact('applications', 'status', 'q'));
    }

    public function show(PartnerApplication $application)
    {
        return view('admin.partners.applications.show', compact('application'));
    }
public function showPartnerUser(User $user)
{
    if (!$user->hasRole('partner')) {
        abort(404);
    }

    $roles = $user->getRoleNames(); // collection
    return view('admin.partners.users.show', compact('user', 'roles'));
}

public function editPartnerUser(User $user)
{
    if (!$user->hasRole('partner')) {
        abort(404);
    }

    $roles = Role::query()
        ->where('guard_name', 'web')
        ->orderBy('name')
        ->pluck('name'); // <-- jadi string semua

    $currentRole = $user->getRoleNames()->first() ?? 'partner';

    return view('admin.partners.users.edit', compact('user', 'roles', 'currentRole'));
}


public function updatePartnerUser(Request $request, User $user)
{
    if (!$user->hasRole('partner')) {
        abort(404);
    }

    $data = $request->validate([
        'name'  => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        'phone' => ['nullable', 'string', 'max:30'],

        // alamat user
        'address'       => ['nullable', 'string'],
        'full_address'  => ['nullable', 'string'],
        'sub_district'  => ['nullable', 'string', 'max:120'],

        // partner fields
        'partner_type' => ['nullable', 'string', 'max:50'],
        'partner_tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],

        'partner_bank_name' => ['nullable', 'string', 'max:100'],
        'partner_bank_account_number' => ['nullable', 'string', 'max:50'],
        'partner_bank_account_holder' => ['nullable', 'string', 'max:100'],

        // role & password
        'role' => ['required', 'in:admin,user,partner'],
        'password' => ['nullable', 'string', 'min:8'],
    ]);

    // Guard: jangan demote diri sendiri kalau admin
    if (auth()->id() === $user->id && $data['role'] !== 'admin') {
        return back()->with('error', 'Tidak boleh mengubah role akun admin yang sedang login.')->withInput();
    }

    if (!empty($data['password'])) {
        $data['password'] = Hash::make($data['password']);
    } else {
        unset($data['password']);
    }

    $role = $data['role'];
    unset($data['role']);

    $user->fill($data);
    $user->save();

    // sync role
    $user->syncRoles([$role]);

    return redirect()
        ->route('admin.partners.users.show', $user->id)
        ->with('success', 'Data user partner berhasil diperbarui.');
}

    public function approve(Request $request, PartnerApplication $application)
    {
      if ($application->status !== 'pending') {
    return redirect()->route('admin.partners.applications.index', ['status' => 'pending'])
        ->with('error', 'Aplikasi ini sudah diproses.');
}


        $request->validate([
            'note' => ['nullable', 'string'],
            'tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        // Cegah kalau email sudah jadi user
       if (User::where('email', $application->email)->exists()) {
    return redirect()->route('admin.partners.applications.index', ['status' => 'pending'])
        ->with('error', 'Email sudah terdaftar sebagai user. Tidak bisa approve otomatis.');
}


        $plainPassword = null;
        try {
            $plainPassword = Crypt::decryptString($application->password_enc);
       } catch (\Throwable $e) {
    return redirect()->route('admin.partners.applications.index', ['status' => 'pending'])
        ->with('error', 'Password pendaftar tidak bisa diproses. Tolak aplikasi dan minta daftar ulang.');
}


        $user = User::create([
            'name' => $application->name,
            'email' => $application->email,
            'password' => $application->password_hash,
            'phone' => $application->phone,
            'address' => $application->address,
            'full_address' => $application->address,
            'email_verified_at' => now(), // biar bisa langsung login
            'partner_tax_percent' => $request->input('tax_percent', 0),
            'is_suspended' => false,
            'suspended_at' => null,
            'partner_type' => $application->partner_type,
'partner_bank_name' => $application->bank_name,
'partner_bank_account_number' => $application->bank_account_number,
'partner_bank_account_holder' => $application->bank_account_holder,
'partner_legal_document_path' => $application->legal_document_path,


        ]);

        $user->assignRole('partner');

        $application->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'review_note' => $request->input('note'),
            // habisin risiko: setelah approve, hapus password_enc biar nggak nyimpen plaintext terenkripsi selamanya
            'password_enc' => Crypt::encryptString('***REDACTED***'),
        ]);

        // Email
        $adminEmail = Setting::invoiceAdminEmail();
        if ($adminEmail) {
            Mail::to($adminEmail)->send(new PartnerApplicationApprovedAdmin($application, $user));
        }
        Mail::to($user->email)->send(new PartnerApplicationApprovedUser($application, $user, $plainPassword));

       return redirect()->route('admin.partners.applications.index', ['status' => 'pending'])
    ->with('success', 'Aplikasi partner disetujui & akun partner dibuat.');

    }

    public function reject(Request $request, PartnerApplication $application)
    {
       if ($application->status !== 'pending') {
    return redirect()->route('admin.partners.applications.index', ['status' => 'pending'])
        ->with('error', 'Aplikasi ini sudah diproses.');
}


        $data = $request->validate([
            'note' => ['required', 'string', 'min:3'],
        ]);

        $application->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'review_note' => $data['note'],
        ]);

        $adminEmail = Setting::invoiceAdminEmail();
        if ($adminEmail) {
            Mail::to($adminEmail)->send(new PartnerApplicationRejectedAdmin($application));
        }
        Mail::to($application->email)->send(new PartnerApplicationRejectedUser($application));

       return redirect()->route('admin.partners.applications.index', ['status' => 'pending'])
    ->with('success', 'Aplikasi partner ditolak & email sudah dikirim.');
    }

    public function partnerUsers(Request $request)
    {
        $q = trim((string) $request->get('q'));

        $users = User::query()
            ->whereHas('roles', fn($r) => $r->where('name', 'partner'))
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.partners.users.index', compact('users', 'q'));
    }

    public function suspend(User $user)
    {
        if (!$user->hasRole('partner')) abort(404);

        $user->update([
            'is_suspended' => true,
            'suspended_at' => now(),
        ]);

        return redirect()->route('admin.partners.users.index')
    ->with('success', 'Akun partner disuspend.');

    }

    public function unsuspend(User $user)
    {
        if (!$user->hasRole('partner')) abort(404);

        $user->update([
            'is_suspended' => false,
            'suspended_at' => null,
        ]);

        return redirect()->route('admin.partners.users.index')
    ->with('success', 'Akun partner diaktifkan kembali.');
    }

    public function setTax(Request $request, User $user)
    {
        if (!$user->hasRole('partner')) abort(404);

        $data = $request->validate([
            'tax_percent' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $user->update([
            'partner_tax_percent' => $data['tax_percent'],
        ]);

        return redirect()->route('admin.partners.users.index')
    ->with('success', 'Pajak partner berhasil diupdate.');

    }
    public function destroyPartner(User $user)
{
    if (!$user->hasRole('partner')) {
        abort(404);
    }

    // bersihin role biar pivot aman
    $user->syncRoles([]);

    // kalau ada relasi lain nanti, bisa ditambah delete cascade manual di sini

    $user->delete();

    return redirect()->route('admin.partners.users.index')
    ->with('success', 'User partner berhasil dihapus.');

}

}
