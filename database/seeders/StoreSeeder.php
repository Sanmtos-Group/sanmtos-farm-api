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

            /**
             * Create an image for the stroe
             */
            Image::factory([
               'imageable_id' => $store->id,
               'imageable_type' => $store::class,
            ])->create();

            /**
             * Create a promo for the store
             */

            Promo::factory([
                'store_id' => $store->id,
             ])->create();
        });
    }
}
