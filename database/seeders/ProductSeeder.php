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
        $product_factory = Product::factory()
        ->count(100);

        if(method_exists(Product::class, 'images'))
        {
            $product_factory->hasImages(1);
        }

        if(method_exists(Product::class, 'likes'))
        {
            $product_factory->hasLikes(3);
        }

        if(method_exists(Product::class, 'ratings'))
        {
            $product_factory->hasRatings(4);
        }
        
        if(method_exists(Product::class, 'promos'))
        {
            $product_factory->hasPromos(1);
        }

        if(method_exists(Product::class, 'coupons'))
        {
            // $product_factory->hasCoupons(1);
        }
  
        $product_factory->create();
    }
}
