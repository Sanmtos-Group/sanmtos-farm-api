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
        if(Permission::count() <=0)
        {
            Permission::upsert(
                $this->defaultPermissions(), 
                uniqueBy:['name'],
                update:['name'],
            );
        }
    }

    public function defaultPermissions() {
        return [
            ['name'=>'create store'],
            ['name'=>'read store'],
            ['name'=>'update store'],
            ['name'=>'delete store'],
            ['name'=>'verify store'],

            ['name'=>'create product'],
            ['name'=>'read product'],
            ['name'=>'update product'],
            ['name'=>'delete product'],

            ['name'=>'create role'],
            ['name'=>'read role'],
            ['name'=>'update role'],
            ['name'=>'delete role'],
            ['name'=>'assign role'],
            ['name'=>'remove role'],
                
            ['name'=>'create permission'],
            ['name'=>'read permission'],
            ['name'=>'update permission'],
            ['name'=>'delete permission'],
            ['name'=>'grant permission'],
            ['name'=>'revoke permission'],
            ['name'=>'sync permission'],

            ['name'=>'create promo'],
            ['name'=>'read promo'],
            ['name'=>'update promo'],
            ['name'=>'delete promo'],
            ['name'=>'cancel promo'],
        ];
    }
}
