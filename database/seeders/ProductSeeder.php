<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory()->count(100)->create()->each(function($product){
            Image::factory([
               'imageable_id' => $product->id,
               'imageable_type' => $product::class,
            ])->create();
        });
    }
}
