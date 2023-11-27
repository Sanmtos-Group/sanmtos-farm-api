<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentGateway::factory()->count(count(Payment::GATEWAYS) ?? 3)->create();
    }
}
