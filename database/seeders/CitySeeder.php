<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

/**
 * City Seeder
 *
 * Seeds Jordanian cities.
 */
class CitySeeder extends Seeder
{
    /**
     * Major Jordanian cities.
     */
    private const array JORDANIAN_CITIES = [
        'Amman',
        'Zarqa',
        'Irbid',
        'Russifa',
        'Wadi as Sir',
        'Aqaba',
        'Karak',
        'Madaba',
        'Mafraq',
        'Jerash',
        'Ajloun',
        'Salt',
        'Ma\'an',
        'Tafilah',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏙️  Seeding cities...');

        foreach (self::JORDANIAN_CITIES as $city) {
            City::firstOrCreate(
                ['name' => $city],
                ['is_active' => true]
            );
        }

        $count = City::count();
        $this->command->info("✅ Successfully seeded {$count} cities.");
        $this->command->newLine();
    }
}
