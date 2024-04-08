<?php

namespace Database\Seeders;

use App\Enums\DiscountTypeEnums;
use App\Models\DiscountType;
use App\Models\Coupon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(Coupon::count() == 0)
        {
            Coupon::upsert(
                $this->defaultCoupons(), 
                uniqueBy:['code'],
                update: (new Coupon)->getFillable(),
            );    
        }
    }

    public function defaultCoupons(){
        return [
            [
                'code'=> 'SANMTOS',
                'description' => 'Lauching coupon of 10% discount on all purchase for next 30 days',
                'discount_type_id' =>  DiscountType::where('code', DiscountTypeEnums::PercentageOff->value)->first()->id ?? null,
                'discount' => 10,
                'is_for_first_purchase_only' => false,
                'expiration_date' => now()->addDays(30),
                'store_id' => null
            ],

            [
                'code'=> 'FRESHERS',
                'description' => '10% discount on first purchase in the next 60 days',
                'discount_type_id' =>  DiscountType::where('code', DiscountTypeEnums::PercentageOff->value)->first()->id ?? null,
                'discount' => 10,
                'is_for_first_purchase_only' => true,
                'expiration_date' => now()->addDays(60),
                'store_id' => null
            ],
        ];
    }
}
