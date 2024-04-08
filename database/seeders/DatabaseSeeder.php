<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            CountrySeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            AttributeSeeder::class,
            CategorySeeder::class,
            PlanSeeder::class,
            FeatureSeeder::class,
            // ImageSeeder::class, 
            // AddressSeeder::class,
            UserSeeder::class, 
            StoreSeeder::class,  
            ProductSeeder::class,  
            DiscountTypeSeeder::class,
            PromoSeeder::class,
            CouponSeeder::class,
            // CouponUsageSeeder::class,
            PaymentGatewaySeeder::class,
            // PaymentSeeder::class,
            OrderSeeder::class,
            CurrencyExchangeRateSeeder::class,
            // LikeSeeder::class,
            // RatingSeeder::class,
            NotificationPreferenceSeeder::class,
        ]);
    }
}
