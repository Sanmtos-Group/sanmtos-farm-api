<?php

namespace Database\Seeders;

use App\Models\User;
use App\TestData\TestUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(User::all()->count() <= 0){
            TestUser::populateDB();
        }
        else {
            User::factory()->count(10)->create();
        }
    }
}
