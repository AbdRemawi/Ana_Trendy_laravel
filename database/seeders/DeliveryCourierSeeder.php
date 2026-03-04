<?php

namespace Database\Seeders;

use App\Models\DeliveryCourier;
use Illuminate\Database\Seeder;

/**
 * Delivery Courier Seeder
 *
 * Seeds delivery companies operating in Jordan.
 */
class DeliveryCourierSeeder extends Seeder
{
    /**
     * Major delivery companies in Jordan.
     */
    private const array COURIERS = [
        [
            'name' => 'Aramex',
            'contact_phone' => '0790000001',
        ],
        [
            'name' => 'FedEx',
            'contact_phone' => '0790000002',
        ],
        [
            'name' => 'DHL',
            'contact_phone' => '0790000003',
        ],
        [
            'name' => 'Jordan Post',
            'contact_phone' => '0790000004',
        ],
        [
            'name' => 'SMSA Express',
            'contact_phone' => '0790000005',
        ],
        [
            'name' => 'GAC',
            'contact_phone' => '0790000006',
        ],
        [
            'name' => 'PalEngers',
            'contact_phone' => '0790000007',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚚 Seeding delivery couriers...');

        foreach (self::COURIERS as $courier) {
            DeliveryCourier::firstOrCreate(
                ['name' => $courier['name']],
                [
                    'contact_phone' => $courier['contact_phone'],
                    'is_active' => true,
                ]
            );
        }

        $count = DeliveryCourier::count();
        $this->command->info("✅ Successfully seeded {$count} delivery couriers.");
        $this->command->newLine();
    }
}
