<?php
namespace App\TestData; 

use App\Models\Permission;

class TestPermission {

    /**
     * @var array $permissions
     */
    private static $permissions = [
        'create store',
        'read store',
        'update store',
        'delete store',
        'verify store',

        'create product',
        'read product',
        'update product',
        'delete product',

        'create role',
        'read role',
        'update role',
        'delete role',
        'assign role',
        'remove role',
        
        'create permission',
        'read permission',
        'update permission',
        'delete permission',
        'grant permission',
        'revoke permission',
        'sync permission',

    ];

    /**
     * Get the test permissions 
     * 
     * @return array
     */
    public static function data(){
        return self::$permissions;
    }

    /**
     * Create the test permissions using the test data 
     * 
     * @return true 
     */
    public static function populateDB(){
        foreach (TestPermission::data() as $key => $value) {
            $permission = Permission::firstOrCreate([
                'name' => $value,
            ]);
        }

    }
}