<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Admin User Seeder
 *
 * Creates the default super admin account for Ana Trendy.
 * This account has full system access.
 *
 * Credentials:
 * - Email: admin@anatrendy.com
 * - Password: password
 */
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@anatrendy.com'],
            [
                'name' => 'Super Admin',
                'mobile' => '0501234567',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        // Assign super_admin role using Spatie's hasRole trait
        $admin->assignRole('super_admin');

        $this->command->info('✅ Super Admin account created/updated successfully.');
        $this->command->info('   Email: admin@anatrendy.com');
        $this->command->info('   Password: password');
        $this->command->newLine();
    }
}
