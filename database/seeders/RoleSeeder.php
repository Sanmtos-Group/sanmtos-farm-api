<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         /**
         * @var array $roles
         */
        $roles = [
            [ 
                'name'=>'super-admin',
                'store_id'=>null    
            ],
            [ 
                'name'=>'admin',
                'store_id'=>null    
            ],
            [ 
                'name'=>'sanmtos-salesperson',
                'store_id'=>null    
            ],
            [ 
                'name'=>'store-admin',
                'store_id'=>null    
            ],
        ];

        Role::upsert(
            $roles, 
            uniqueBy:['name', 'store_id'],
            update:['name', 'store_id'],
        );
    }
}
