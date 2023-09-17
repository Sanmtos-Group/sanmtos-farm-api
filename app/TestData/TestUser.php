<?php

namespace App\TestData; 

use App\Models\Role;
use App\Models\Store;
use App\Models\User;

class TestUser {

    private static $store_admin = null;
    private static $sanmtos_sales_person = null;
    private static $buyer = null;

    /**
     * Genenerate test user data using user factory
     * password:password
     * 
     * @return array
     */
    public static function data(): array
    {
        
        self::$store_admin = User::factory(['email'=> 'store-admin@example.com'])->make()->only((new User)->getFillable());
        self::$sanmtos_sales_person = User::factory(['email'=> 'sanmtos-salesperson@example.com'])->make()->only((new User)->getFillable());
        self::$buyer = User::factory(['email'=> 'buyer@example.com'])->make()->only((new User)->getFillable());
        
        $test_users = [
            'store-admin' => self::$store_admin,
            'sanmtos-salesperson' => self::$sanmtos_sales_person,
            'buyer' => self::$buyer,
        ];

        return $test_users;
    }

    /**
     * Create the test users using the test data 
     * Attach users related role
     * 
     * @return null 
     */
    public static function populateDB(){
        foreach (TestUser::data() as $key => $value) {
            $user = !empty($value)? User::where('email', $value['email']?? null)->first()?? User::create($value) : null;
        }

        $store_admin = User::where('email', self::$store_admin['email']?? 'store-admin@example.com')->first() ??
        User::factory(['email'=> 'store-admin@example.com'])->make()->only((new User)->getFillable());
       
        if(empty($store_admin->store)){
            $store = Store::factory([
                'name' => $store_admin->first_name. " & Sons Enterprise",
                'description' => 'Work smart with our modern smart devices at your reach.',
                'user_id' => $store_admin->id,
            ])->create();
            $store->verified_at = now();
            $store->save();
       }

        $store_admin_role = Role::firstOrCreate([
            'name' => 'store-admin',
            'store_id'=> null
        ]);
        
        if(empty($store_admin->roles()->where('role_id', $store_admin_role->id)->first())){
            $store_admin->roles()->attach($store_admin_role->id);
        }

        $sanmtos_sales_person = User::where('email', self::$sanmtos_sales_person['email']?? 'sanmtos-salesperson@example.com')->first() ??
        User::factory(['email'=> 'sanmtos-salesperson@example.com'])->make()->only((new User)->getFillable());

        $sanmtos_sales_person_role = Role::firstOrCreate([
            'name' => 'sanmtos-salesperson',
            'store_id'=> null
        ]);

        if(empty($sanmtos_sales_person->roles()->where('role_id', $sanmtos_sales_person_role->id)->first())){
            $sanmtos_sales_person->roles()->attach($sanmtos_sales_person_role->id);
        }
    }
}