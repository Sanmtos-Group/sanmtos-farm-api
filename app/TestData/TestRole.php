<?php
namespace App\TestData; 

use App\Models\Role;
use App\Models\Store;

class TestRole {

    /**
     * @var array $roles
     */
    private static $roles = [
        'super-admin',
        'admin',
        'sanmtos-salesperson',
        'store-admin',
    ];

    /**
     * Get the test roles 
     * 
     * @return array
     */
    public static function data(){
        return self::$roles;
    }

    /**
     * Create the test roles using the test data 
     * 
     * @return null 
     */
    public static function populateDB(){
        foreach (TestRole::data() as $key => $value) {
            $role = Role::where('name', $value)->where('store_id', null)->first()?? Role::create([
                'name' => $value,
                'store_id' => null
            ]);
        }

    }
}