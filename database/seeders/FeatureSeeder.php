<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use LucasDotVin\Soulbscription\Enums\PeriodicityType;
use LucasDotVin\Soulbscription\Models\Feature;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        if(Feature::count() <= 0)
        {
            Feature::upsert(
                $this->defaultFeatures(), 
                uniqueBy:['name'],
                update: (new Feature)->getFillable(),
            );
        }
    }

    public function defaultFeatures(){
        return [
            [
                'consumable'       => true,
                'name'             => 'deploy-minutes',
                'periodicity_type' => PeriodicityType::Month,
                'periodicity'      => 1,
            ],
            [
                'consumable' => false,
                'name'       => 'custom-domain',
                'periodicity_type' => null,
                'periodicity'      => null,
            ]
        ];
    }
}
