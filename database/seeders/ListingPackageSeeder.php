<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ListingPackage;
use Illuminate\Support\Facades\DB;

class ListingPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $packages = [
            [
                'name' => 'Sundul Iklan 1x',
                'type' => 'sundul',
                'amount' => 1,
                'price' => 5000,
            ],
            [
                'name' => 'Sundul Iklan 2x',
                'type' => 'sundul',
                'amount' => 2,
                'price' => 10000,
            ],
            [
                'name' => 'Sundul Iklan 3x',
                'type' => 'sundul',
                'amount' => 3,
                'price' => 15000,
            ],
            [
                'name' => 'Sundul Iklan 4x',
                'type' => 'sundul',
                'amount' => 4,
                'price' => 20000,
            ],
            [
                'name' => 'Sundul Iklan 5x',
                'type' => 'sundul',
                'amount' => 5,
                'price' => 25000,
            ],
            [
                'name' => 'PREMIUM',
                'type' => 'premium',
                'amount' => 0,
                'price' => 25000,
            ],
        ];

        foreach ($packages as $pkg) {
            ListingPackage::updateOrCreate(
                ['type' => $pkg['type'], 'amount' => $pkg['amount']],
                ['name' => $pkg['name'], 'price' => $pkg['price']]
            );
        }
    }
}
