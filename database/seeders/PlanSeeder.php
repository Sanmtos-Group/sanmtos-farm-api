<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use LucasDotVin\Soulbscription\Enums\PeriodicityType;
use LucasDotVin\Soulbscription\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(Plan::count() <=0 )
        {
            Plan::upsert(
                $this->defaultPlans(), 
                uniqueBy:['name'],
                update: (new Plan)->getFillable(),
            );
        }
    }

    public function defaultPlans(){
        return [
            [
                'name'             => 'silver',
                'periodicity_type' => PeriodicityType::Month,
                'periodicity'      => 1,
            ],
            [
                'name'             => 'gold',
                'periodicity_type' => PeriodicityType::Month,
                'periodicity'      => 1,
            ]
        ];
    }
}
