<?php

namespace Database\Seeders;

use App\Enums\DiscountTypeEnums;
use App\Models\DiscountType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiscountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $discount_types =[
            [
                'name' =>ucwords(strtolower(str_replace('_', ' ',  DiscountTypeEnums::FlatOff->value ))),
                'description'=> 'Fixed discount amount deducted from the original price',
                'code'=> DiscountTypeEnums::FlatOff->value,
            ],
            [
                'name' =>ucwords(strtolower(str_replace('_', ' ',  DiscountTypeEnums::PercentageOff->value ))),
                'description'=> 'Reduction in price based on a percentage of the original price',
                'code'=>DiscountTypeEnums::PercentageOff->value,
            ],
        ];

        DiscountType::upsert(
            $discount_types, 
            uniqueBy:['code'],
            update:['name', 'description'],
        );
    }
}