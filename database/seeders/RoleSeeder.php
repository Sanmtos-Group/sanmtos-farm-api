<?php

namespace Database\Seeders;

use App\Models\Role;
use App\TestData\TestRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(Role::all()->count() <= 0){
            TestRole::populateDB();
        }
        else {
            Role::factory()->count(5)->create();
        }
    }
}
