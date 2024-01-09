<?php

namespace Database\Seeders;

use App\Models\CurrencyExchangeRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencyExchangeRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CurrencyExchangeRate::factory()->count(3)->create();
    }
}
