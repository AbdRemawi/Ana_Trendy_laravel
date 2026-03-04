<?php

namespace Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\Models\Role;

/**
 * Role and Permission Seeder
 *
 * This seeder creates all necessary permissions and roles for the RBAC system.
 * It is idempotent and can be run multiple times safely.
 *
 * Roles created:
 * - super_admin: Full system access with all permissions
 * - admin: Management access for products, orders, and users
 * - affiliate: Limited access to own performance and commissions
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear permission cache to ensure fresh data
        $this->clearPermissionCache();

        // Create all permissions from the enum
        $this->createPermissions();

        // Create roles and assign permissions
        $this->createSuperAdminRole();
        $this->createAdminRole();
        $this->createAffiliateRole();

        $this->command->info('✅ Roles and permissions seeded successfully.');
        $this->command->newLine();
        $this->displayRoleSummary();
    }

    /**
     * Clear Spatie permission cache.
     */
    protected function clearPermissionCache(): void
    {
        Cache::forget('spatie.permission.cache');
        $this->command->info('🗑️  Permission cache cleared.');
    }

    /**
     * Create all permissions defined in the Permission enum.
     */
    protected function createPermissions(): void
    {
        $permissions = PermissionEnum::all();

        foreach ($permissions as $permissionName) {
            SpatiePermission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $this->command->info('✅ All permissions created: '.count($permissions));
    }

    /**
     * Create super_admin role with all permissions.
     */
    protected function createSuperAdminRole(): void
    {
        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        // Super admin gets ALL permissions
        $allPermissions = SpatiePermission::all();
        $superAdmin->syncPermissions($allPermissions);

        $this->command->info('👑 Super Admin role created with all permissions ('.$allPermissions->count().')');
    }

    /**
     * Create admin role with management permissions.
     * Admin cannot manage roles, permissions, or system config.
     */
    protected function createAdminRole(): void
    {
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        // Admin permissions: manage users, products, brands, orders, but NOT system/roles
        $adminPermissions = [
            PermissionEnum::VIEW_DASHBOARD->value,

            // User Management
            PermissionEnum::VIEW_USERS->value,
            PermissionEnum::MANAGE_USERS->value,

            // Product Management
            PermissionEnum::VIEW_PRODUCTS->value,
            PermissionEnum::MANAGE_PRODUCTS->value,
            PermissionEnum::DELETE_PRODUCTS->value,

            // Brand Management
            PermissionEnum::VIEW_BRANDS->value,
            PermissionEnum::MANAGE_BRANDS->value,
            PermissionEnum::DELETE_BRANDS->value,

            // Category Management
            PermissionEnum::VIEW_CATEGORIES->value,
            PermissionEnum::MANAGE_CATEGORIES->value,

            // Order Management
            PermissionEnum::VIEW_ORDERS->value,
            PermissionEnum::MANAGE_ORDERS->value,
            PermissionEnum::DELETE_ORDERS->value,

            // Commission Viewing (read-only)
            PermissionEnum::VIEW_COMMISSIONS->value,
        ];

        $admin->syncPermissions($adminPermissions);

        $this->command->info('🔧 Admin role created with '.count($adminPermissions).' permissions');
    }

    /**
     * Create affiliate role with limited permissions.
     * Affiliates can only view their own performance and commissions.
     */
    protected function createAffiliateRole(): void
    {
        $affiliate = Role::firstOrCreate([
            'name' => 'affiliate',
            'guard_name' => 'web',
        ]);

        // Affiliate permissions: view only, own data only
        $affiliatePermissions = [
            PermissionEnum::VIEW_DASHBOARD->value,
            PermissionEnum::VIEW_OWN_PERFORMANCE->value,
            PermissionEnum::VIEW_COMMISSIONS->value,
            PermissionEnum::MANAGE_OWN_COUPON->value,
        ];

        $affiliate->syncPermissions($affiliatePermissions);

        $this->command->info('💼 Affiliate role created with '.count($affiliatePermissions).' permissions');
    }

    /**
     * Display a summary table of created roles and permissions.
     */
    protected function displayRoleSummary(): void
    {
        $roles = Role::with('permissions')->get()->keyBy('name');

        $this->command->table(
            ['Role', 'Permissions Count', 'Key Permissions'],
            [
                [
                    'super_admin',
                    $roles->get('super_admin')?->permissions->count() ?? 0,
                    'All permissions',
                ],
                [
                    'admin',
                    $roles->get('admin')?->permissions->count() ?? 0,
                    'manage users, products, orders',
                ],
                [
                    'affiliate',
                    $roles->get('affiliate')?->permissions->count() ?? 0,
                    'view own performance, commissions',
                ],
            ]
        );
    }
}
