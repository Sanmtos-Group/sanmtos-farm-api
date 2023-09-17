<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\TestData\TestPermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(Permission::all()->count() <= 0){
            TestPermission::populateDB();
        }
        else {
            Permission::factory()->count(5)->create();
        }
    }
}
