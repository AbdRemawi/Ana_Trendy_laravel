<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders in correct order:
        // 1. Create roles and permissions first
        // 2. Then create admin user and assign super_admin role
        // 3. Seed brands table with luxury fashion brands
        // 4. Seed categories (Handbags, Shoes, Accessories)
        // 5. Seed products with images and inventory
        // 6. Seed cities (Jordanian cities)
        // 7. Seed delivery couriers
        // 8. Seed delivery courier fees
        // 9. Seed coupons
        // 10. Seed orders with items and mobiles
        $this->call([
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            CitySeeder::class,
            DeliveryCourierSeeder::class,
            DeliveryCourierFeeSeeder::class,
            CouponSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
