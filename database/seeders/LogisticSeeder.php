<?php

namespace Database\Seeders;

use App\Enums\LogisticEnum;
use App\Models\Logistic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LogisticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Logistic::upsert(
            $this->defaultDefaultLogistics(), 
            uniqueBy:['name'],
            update:['name'],
        );
    }

    public function defaultDefaultLogistics()
    {
        $logistics = [];

        foreach(LogisticEnum::values() as $logistic)
        {
            $logistics[] = [
                'name' => $logistic
            ];
        }
        
        return $logistics;
    }
}
