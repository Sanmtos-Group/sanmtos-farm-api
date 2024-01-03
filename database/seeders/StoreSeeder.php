<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Promo;
use App\Models\Store;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Store::factory()
        ->count(20)
        ->hasInCoupons(2)
        ->hasInPromos(2)
        ->hasImages(1)
        ->create();
    }
}
