<?php

namespace Database\Seeders;

use App\Enums\DiscountTypeEnum;
use App\Models\DiscountType;
use App\Models\Promo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promos = [
            Promo::factory([
                'name'=> 'Loyal Luxuries Promo!!',
                'description' => 'Lauching coupon of 10% discount on all purchase for next 30 days',

                'discount_type_id' =>  DiscountType::where('code', DiscountTypeEnum::PercentageOff->value)->first()->id ?? null,
            ])->make()->toArray()
            ,
            Promo::factory([
                'name'=> 'Discount Delight Day Promo!!',
                'description' => '10% discount on first purchase in the next 60 days',
                'discount_type_id' =>  DiscountType::where('code', DiscountTypeEnum::PercentageOff->value)->first()->id ?? null,
            ])->make()->toArray()
            ,
        ];

        if(Promo::count() == 0)
        {
            Promo::upsert(
                $promos, 
                uniqueBy:['name', 'store_id'],
                update: (new Promo)->getFillable(),
            );    
        }
    }
}
