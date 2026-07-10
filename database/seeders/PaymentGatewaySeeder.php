<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentGateway;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            ['name' => 'doku', 'label' => 'DOKU', 'is_active' => false],
            ['name' => 'tripay', 'label' => 'TriPay', 'is_active' => false],
            ['name' => 'midtrans', 'label' => 'Midtrans', 'is_active' => false],
            ['name' => 'xendit', 'label' => 'Xendit', 'is_active' => false],
            ['name' => 'ipaymu', 'label' => 'iPaymu', 'is_active' => false],
            ['name' => 'paypal', 'label' => 'PayPal', 'is_active' => false],
        ];


        foreach ($gateways as $g) {
            PaymentGateway::updateOrCreate(
                ['name' => $g['name']],
                [
                    'label' => $g['label'],
                    'is_active' => $g['is_active'],
                    'credentials' => null,
                    'channels' => null,
                    'channels_synced_at' => null,
                ]
            );
        }
    }
}
