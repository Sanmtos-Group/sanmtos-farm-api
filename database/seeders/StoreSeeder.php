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
        Store::factory()->count(20)->create()->each(function($store){
            Image::factory([
               'imageable_id' => $store->id,
               'imageable_type' => $store::class,
            ])->create();

            Promo::factory([
                'promoable_id' => $store->id,
                'promoable_type' => $store::class,
             ])->create();
        });
    }
}
