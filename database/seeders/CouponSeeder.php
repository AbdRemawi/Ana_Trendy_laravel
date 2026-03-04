<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

/**
 * Coupon Seeder
 *
 * Seeds sample coupons for testing.
 */
class CouponSeeder extends Seeder
{
    /**
     * Sample coupons.
     */
    private const array COUPONS = [
        [
            'code' => 'WELCOME10',
            'type' => 'fixed',
            'value' => 10.00,
            'minimum_order_amount' => 50.00,
            'max_uses' => null,
        ],
        [
            'code' => 'SUMMER20',
            'type' => 'percentage',
            'value' => 20.00,
            'minimum_order_amount' => 100.00,
            'max_uses' => 100,
        ],
        [
            'code' => 'FREESHIP',
            'type' => 'free_delivery',
            'value' => 0.00,
            'minimum_order_amount' => 75.00,
            'max_uses' => null,
        ],
        [
            'code' => 'VIP15',
            'type' => 'percentage',
            'value' => 15.00,
            'minimum_order_amount' => 150.00,
            'max_uses' => 50,
        ],
        [
            'code' => 'FLASH25',
            'type' => 'percentage',
            'value' => 25.00,
            'minimum_order_amount' => 200.00,
            'max_uses' => 25,
        ],
        [
            'code' => 'SAVE5',
            'type' => 'fixed',
            'value' => 5.00,
            'minimum_order_amount' => 30.00,
            'max_uses' => null,
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎟️  Seeding coupons...');

        foreach (self::COUPONS as $coupon) {
            Coupon::firstOrCreate(
                ['code' => $coupon['code']],
                [
                    'type' => $coupon['type'],
                    'value' => $coupon['value'],
                    'minimum_order_amount' => $coupon['minimum_order_amount'],
                    'max_uses' => $coupon['max_uses'],
                    'used_count' => 0,
                    'valid_from' => now()->subMonth(),
                    'valid_until' => now()->addMonth(),
                    'is_active' => true,
                ]
            );
        }

        $count = Coupon::count();
        $this->command->info("✅ Successfully seeded {$count} coupons.");
        $this->command->newLine();
    }
}
