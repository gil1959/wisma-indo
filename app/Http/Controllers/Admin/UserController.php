<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));

        $users = User::query()
            // Jangan tampilkan admin di list (biar aman)
            ->whereDoesntHave('roles', function ($r) {
                $r->where('name', 'admin');
            })
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

        return view('admin.users.index', compact('users', 'q'));
    }

    public function show(User $user)
{
    return view('admin.users.show', compact('user'));
}

public function create()
{
    // jangan kasih opsi admin dari sini
    $roles = Role::query()
        ->whereIn('name', ['user', 'partner', 'site_moderator'])
        ->orderBy('name')
        ->pluck('name');

    $permissionMap = config('admin_permissions', []);
    $permissions = collect($permissionMap)->map(function ($v, $k) {
        return [
            'name' => $k,
            'label' => $v['label'] ?? $k,
        ];
    })->values();

    return view('admin.users.create', compact('roles', 'permissions'));
}

public function store(Request $request)
{
    $data = $request->validate([
        'name'     => ['required', 'string', 'max:255'],
        'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
        'phone'    => ['nullable', 'string', 'max:30'],
        'address'  => ['nullable', 'string'],
        'sub_district' => ['nullable', 'string', 'max:120'],
        'password' => ['required', 'string', 'min:6'],

        'role'     => ['required', 'in:user,partner,site_moderator'],
        'is_verified' => ['nullable', 'boolean'],

        // permissions untuk site moderator
        'permissions'   => ['nullable', 'array'],
        'permissions.*' => ['string', 'exists:permissions,name'],

        // partner fields (wajib kalau role partner)
        'partner_type' => ['nullable', 'in:agency_paket_tour,agency_rental_mobil,agency_restoran,agency_hotel_vila'],
        'partner_bank_name' => ['nullable', 'string', 'max:100'],
        'partner_bank_account_number' => ['nullable', 'string', 'max:50'],
        'partner_bank_account_holder' => ['nullable', 'string', 'max:100'],
    ]);

    if ($data['role'] === 'partner') {
        $request->validate([
           'partner_type' => ['required', 'in:agency_paket_tour,agency_rental_mobil,agency_restoran,agency_hotel_vila'],
            'partner_bank_name' => ['required', 'string', 'max:100'],
            'partner_bank_account_number' => ['required', 'string', 'max:50'],
            'partner_bank_account_holder' => ['required', 'string', 'max:100'],
        ]);
    }

    $isVerified = (bool)($data['is_verified'] ?? false);

    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone'] ?? null,
        'address' => $data['address'] ?? null,
        'full_address' => $data['address'] ?? null,
        'sub_district' => $data['sub_district'] ?? null,
        'email_verified_at' => $isVerified ? now() : null,
        'password' => Hash::make($data['password']),

        // partner fields (kalau ada kolomnya di users)
        'partner_type' => $data['partner_type'] ?? null,
        'partner_bank_name' => $data['partner_bank_name'] ?? null,
        'partner_bank_account_number' => $data['partner_bank_account_number'] ?? null,
        'partner_bank_account_holder' => $data['partner_bank_account_holder'] ?? null,
    ]);

    $user->syncRoles([$data['role']]);

    if ($data['role'] === 'site_moderator') {
        $user->syncPermissions($data['permissions'] ?? []);
    } else {
        $user->syncPermissions([]);
    }

    return redirect()
        ->route('admin.users.show', $user)
        ->with('success', 'User berhasil dibuat.');
}

    public function edit(User $user)
{
   $roles = Role::query()
    ->whereIn('name', ['user', 'partner', 'site_moderator'])
    ->orderBy('name')
    ->pluck('name');

$currentRole = $user->roles->pluck('name')->first();

$permissionMap = config('admin_permissions', []);
$permissions = collect($permissionMap)->map(function ($v, $k) {
    return [
        'name' => $k,
        'label' => $v['label'] ?? $k,
    ];
})->values();

$currentPermissions = $user->permissions->pluck('name')->toArray();

return view('admin.users.edit', compact('user', 'roles', 'currentRole', 'permissions', 'currentPermissions'));

}


    public function update(Request $request, User $user)
    {
        if ($user->hasRole('admin')) {
            abort(404);
        }

        $data = $request->validate([
    'name'         => ['required', 'string', 'max:255'],
    'email'        => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
    'phone'        => ['nullable', 'string', 'max:30'],
    'address'      => ['nullable', 'string'],
    'sub_district' => ['nullable', 'string', 'max:120'],
    'password'     => ['nullable', 'string', 'min:6'],
'permissions'   => ['nullable', 'array'],
'permissions.*' => ['string', 'exists:permissions,name'],

    // role (1 role utama)
    'role'         => ['required', 'string', 'exists:roles,name'],

    // verify toggle dari checkbox
    'is_verified'  => ['nullable', 'boolean'],
]);

// Guard: jangan bisa bikin admin nge-demote dirinya sendiri (biar gak lockout)
if (auth()->id() === $user->id && $data['role'] !== 'admin') {
    return back()->with('error', 'Tidak boleh mengubah role akun admin yang sedang login.')->withInput();
}

// Password opsional
if (!empty($data['password'])) {
    $data['password'] = Hash::make($data['password']);
} else {
    unset($data['password']);
}

// Email verified toggle
// Checkbox => 1/0
$isVerified = (bool) ($data['is_verified'] ?? false);
unset($data['is_verified']);

$data['email_verified_at'] = $isVerified ? ($user->email_verified_at ?? now()) : null;

// Konsistensi alamat:
// - kita simpan ke `address`
// - dan sekalian mirror ke `full_address` biar fitur lain yg baca full_address tetap jalan
$data['full_address'] = $data['address'] ?? null;

// Update user
$user->update($data);

// Update role
$user->syncRoles([$data['role']]);
if ($data['role'] === 'site_moderator') {
    $perms = $request->input('permissions', []);
    $user->syncPermissions($perms);
} else {
    $user->syncPermissions([]);
}


return redirect()
    ->route('admin.users.show', $user)
    ->with('success', 'User berhasil diupdate.');

    }

    public function destroy(User $user)
    {
        // Jangan bisa hapus admin dari sini
        if ($user->hasRole('admin')) {
            return back()->with('error', 'User admin tidak boleh dihapus dari menu ini.');
        }

        // Jangan bisa hapus diri sendiri
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Tidak bisa menghapus akun yang sedang login.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
