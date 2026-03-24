<?php

namespace Database\Seeders;

use App\Models\DeliveryCourierFee;
use App\Models\City;
use App\Models\DeliveryCourier;
use Illuminate\Database\Seeder;

/**
 * Delivery Courier Fee Seeder
 *
 * Seeds delivery fees for each courier to each city.
 */
class DeliveryCourierFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('💰 Seeding delivery courier fees...');

        $cities = City::all();
        $couriers = DeliveryCourier::all();

        $createdCount = 0;

        foreach ($couriers as $courier) {
            foreach ($cities as $city) {
                // Base fee varies by courier
                $baseFee = match($courier->name) {
                    'Aramex' => 5.00,
                    'FedEx' => 7.00,
                    'DHL' => 8.00,
                    'Jordan Post' => 3.00,
                    'SMSA Express' => 4.50,
                    'GAC' => 5.50,
                    'PalEngers' => 4.00,
                    default => 5.00,
                };

                // Adjust fee based on city (Aqaba is farther, Amman is central)
                $cityMultiplier = match($city->name) {
                    'Aqaba', 'Ma\'an', 'Tafilah' => 1.5,  // Far south
                    'Irbid', 'Ajloun', 'Jerash', 'Mafraq' => 1.3,  // North
                    'Karak', 'Madaba', 'Salt' => 1.1,  // Nearby
                    default => 1.0,  // Central
                };

                $realFee = $baseFee * $cityMultiplier;

                DeliveryCourierFee::firstOrCreate(
                    [
                        'delivery_courier_id' => $courier->id,
                        'city_id' => $city->id,
                    ],
                    [
                        'real_fee_amount' => round($realFee, 3),
                        'currency' => 'JOD',
                        'is_active' => true,
                    ]
                );

                $createdCount++;
            }
        }

        $this->command->info("✅ Successfully seeded {$createdCount} delivery courier fees.");
        $this->command->newLine();
    }
}
