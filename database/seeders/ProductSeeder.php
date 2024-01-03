<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Product;
use App\Models\Promo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory()
        ->count(100)
        ->hasImages(1)
        ->hasLikes(3)
        ->hasPromos(1)
        // ->hasCoupons(1)
        ->create();
    }
}
