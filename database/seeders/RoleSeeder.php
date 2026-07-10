<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Pastikan guard web (default Laravel)
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole  = Role::firstOrCreate(['name' => 'user',  'guard_name' => 'web']);
        $partnerRole = Role::firstOrCreate(['name' => 'partner', 'guard_name' => 'web']);
        $siteModeratorRole = Role::firstOrCreate(['name' => 'site_moderator', 'guard_name' => 'web']);

        // bikin user admin pertama (kalau belum ada)
        $admin = User::firstOrCreate(
            ['email' => 'bintangwisataofficial@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('@BINTANGwisata12345'),
            ]
        );

        // assign role admin kalau belum
        if (! $admin->hasRole('admin')) {
            $admin->assignRole($adminRole); // atau: $admin->assignRole('admin');
        }
    }
}
