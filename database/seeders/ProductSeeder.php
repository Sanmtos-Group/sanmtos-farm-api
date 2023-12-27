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
        ->create()->each(function($product){
            
            //Attached an active promo
            $product->promos()->sync($product->store->inActivePromos()->inRandomOrder()->first());

            // Attached an active coupon
            $product->coupons()->sync($product->store->inActiveCoupons()->inRandomOrder()->first());
        });
    }
}
