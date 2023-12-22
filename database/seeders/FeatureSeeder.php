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
        $deployMinutes = Feature::create([
            'consumable'       => true,
            'name'             => 'deploy-minutes',
            'periodicity_type' => PeriodicityType::Month,
            'periodicity'      => 1,
        ]);

        $customDomain = Feature::create([
            'consumable' => false,
            'name'       => 'custom-domain',
        ]);
    }
}
