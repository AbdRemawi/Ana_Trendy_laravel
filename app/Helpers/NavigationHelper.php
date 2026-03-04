<?php

namespace App\Helpers;

use App\Enums\Permission;

/**
 * Navigation Helper
 *
 * Provides navigation menu items for sidebar with permission filtering.
 * This keeps Blade templates clean and logic-free.
 */
class NavigationHelper
{
    /**
     * Get all navigation items.
     *
     * @return array<int, array{name: string, route: string, icon: string, permission: string}>
     */
    public static function getItems(): array
    {
        return [
            [
                'name' => 'dashboard',
                'route' => 'dashboard',
                'icon' => 'M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z',
                'permission' => Permission::VIEW_DASHBOARD->value,
            ],
            [
                'name' => 'orders',
                'route' => 'admin.orders.index',
                'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                'permission' => Permission::VIEW_ORDERS->value,
            ],
            [
                'name' => 'coupons',
                'route' => 'admin.coupons.index',
                'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7A2 2 0 018 20H5a2 2 0 01-2-2V5a2 2 0 012-2z',
                'permission' => Permission::VIEW_ORDERS->value,
            ],
            [
                'name' => 'products',
                'route' => 'admin.products.index',
                'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                'permission' => Permission::VIEW_PRODUCTS->value,
            ],
            [
                'name' => 'inventory',
                'route' => 'admin.inventory.index',
                'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                'permission' => Permission::VIEW_PRODUCTS->value,
            ],
            [
                'name' => 'brands',
                'route' => 'admin.brands.index',
                'icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01',
                'permission' => Permission::VIEW_BRANDS->value,
            ],
            [
                'name' => 'categories',
                'route' => 'admin.categories.index',
                'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7A2 2 0 018 20H5a2 2 0 01-2-2V5a2 2 0 012-2z',
                'permission' => Permission::VIEW_CATEGORIES->value,
            ],
            [
                'name' => 'cities',
                'route' => 'admin.cities.index',
                'icon' => 'M17.657 16.657L13 4H9m0 0L4.343 16.657A2 2 0 005.999 19h10.003a2 2 0 001.656-2.343zM17 6v6.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 01-.707.293H4a1 1 0 01-1-1V6a1 1 0 011-1h12a1 1 0 011 1z M3 3a1 1 0 011-1h16a1 1 0 011 1v1a1 1 0 11-2 0V3z',
                'permission' => Permission::VIEW_CITIES->value,
            ],
            [
                'name' => 'delivery_couriers',
                'route' => 'admin.delivery-couriers.index',
                'icon' => 'M9 2a1 1 0 000 2h2a1 1 0 100-2H9zM6 4a2 2 0 00-2 2v9a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2h-1V4a1 1 0 10-2 0v2H6zM9 12h2v2H9v-2zm0 3h2v2H9v-2zm-5 3.5L6 21l-3-1.5V16a1 1 0 011-1h12a1 1 0 011 1v3.5L12 18.5l-3 1.5V17a1 1 0 01-1-1H6a1 1 0 01-1 1v1.5z',
                'permission' => Permission::VIEW_DELIVERY_COURIERS->value,
            ],
            [
                'name' => 'delivery_courier_fees',
                'route' => 'admin.delivery-courier-fees.index',
                'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z M8 12h.01M8 10h.01M12 8v4m0 4h.01',
                'permission' => Permission::VIEW_DELIVERY_COURIER_FEES->value,
            ],
            [
                'name' => 'users',
                'route' => 'admin.users.index',
                'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                'permission' => Permission::VIEW_USERS->value,
            ],
            [
                'name' => 'roles_permissions',
                'route' => 'admin.roles.index',
                'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                'permission' => Permission::MANAGE_ROLES->value,
            ],
            [
                'name' => 'affiliate',
                'route' => '#',
                'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'permission' => Permission::VIEW_COMMISSIONS->value,
            ],
            [
                'name' => 'settings',
                'route' => '#',
                'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                'permission' => Permission::VIEW_SYSTEM_CONFIG->value,
            ],
        ];
    }

    /**
     * Get navigation items filtered by user permissions.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     * @return array<int, array{name: string, route: string, icon: string, permission: string}>
     */
    public static function getFilteredItems($user = null): array
    {
        $items = self::getItems();

        if (!$user) {
            return [];
        }

        return array_filter($items, function ($item) use ($user) {
            return $user->can($item['permission']);
        });
    }
}
