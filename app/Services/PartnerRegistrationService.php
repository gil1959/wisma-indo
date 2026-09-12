<?php
namespace App\Services;

use App\Models\PartnerRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PartnerRegistrationService
{
    public function register(Request $request): User
    {
        $data = $request->validate([
            'name' => 'required|string|max:255', 'email' => 'required|email|max:255|unique:users',
            'phone' => 'required|string|max:20', 'password' => 'required|string|min:8|confirmed',
            'ktp_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'nib_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'foto_file' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'npwp_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'lisensi_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);
        $paths = [];
        try {
            return DB::transaction(function () use ($data, $request, &$paths) {
                $user = User::create(['name' => $data['name'], 'email' => $data['email'], 'phone' => $data['phone'], 'password' => Hash::make($data['password'])]);
                $user->assignRole('user');
                $registration = PartnerRegistration::create(['user_id' => $user->id, 'ktp_file' => 'pending', 'foto_file' => null, 'status' => 'pending']);
                foreach (['ktp_file', 'nib_file', 'foto_file', 'npwp_file', 'lisensi_file'] as $field) {
                    if ($request->hasFile($field)) {
                        $path = $request->file($field)->store('partners/' . $registration->id, 'public');
                        if (!$path) throw new \RuntimeException('Dokumen tidak dapat disimpan.');
                        $paths[] = $path;
                        $registration->$field = 'storage/' . $path;
                    }
                }
                $registration->save();
                return $user;
            });
        } catch (\Throwable $e) {
            foreach ($paths as $path) Storage::disk('public')->delete($path);
            throw $e;
        }
    }
}
