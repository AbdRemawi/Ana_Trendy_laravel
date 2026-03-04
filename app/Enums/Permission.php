<?php

namespace App\Enums;

/**
 * Application Permission Enum
 *
 * Defines all granular permissions for RBAC system.
 * Using an enum eliminates magic strings and provides type safety.
 */
enum Permission: string
{
    // ============ DASHBOARD ============
    case VIEW_DASHBOARD = 'view dashboard';

    // ============ USER MANAGEMENT ============
    case VIEW_USERS = 'view users';
    case MANAGE_USERS = 'manage users';
    case DELETE_USERS = 'delete users';

    // ============ PRODUCT MANAGEMENT ============
    case VIEW_PRODUCTS = 'view products';
    case MANAGE_PRODUCTS = 'manage products';
    case DELETE_PRODUCTS = 'delete products';

    // ============ BRAND MANAGEMENT ============
    case VIEW_BRANDS = 'view brands';
    case MANAGE_BRANDS = 'manage brands';
    case DELETE_BRANDS = 'delete brands';

    // ============ CATEGORY MANAGEMENT ============
    case VIEW_CATEGORIES = 'view categories';
    case MANAGE_CATEGORIES = 'manage categories';

    // ============ CITY MANAGEMENT ============
    case VIEW_CITIES = 'view cities';
    case MANAGE_CITIES = 'manage cities';

    // ============ DELIVERY COURIER MANAGEMENT ============
    case VIEW_DELIVERY_COURIERS = 'view delivery couriers';
    case MANAGE_DELIVERY_COURIERS = 'manage delivery couriers';

    // ============ DELIVERY COURIER FEE MANAGEMENT ============
    case VIEW_DELIVERY_COURIER_FEES = 'view delivery courier fees';
    case MANAGE_DELIVERY_COURIER_FEES = 'manage delivery courier fees';

    // ============ ORDER MANAGEMENT ============
    case VIEW_ORDERS = 'view orders';
    case MANAGE_ORDERS = 'manage orders';
    case DELETE_ORDERS = 'delete orders';

    // ============ COMMISSION & AFFILIATE ============
    case VIEW_COMMISSIONS = 'view commissions';
    case VIEW_OWN_PERFORMANCE = 'view own performance';
    case MANAGE_OWN_COUPON = 'manage own coupon';

    // ============ SYSTEM & ROLE MANAGEMENT ============
    case MANAGE_ROLES = 'manage roles';
    case MANAGE_PERMISSIONS = 'manage permissions';
    case VIEW_SYSTEM_CONFIG = 'view system config';

    /**
     * Get all permission values as an array.
     *
     * @return array<string>
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get permission label for display (supports translation).
     *
     * @return string
     */
    public function label(): string
    {
        return __("permissions.{$this->value}");
    }

    /**
     * Get permissions grouped by category.
     *
     * @return array<string, array<Permission>>
     */
    public static function grouped(): array
    {
        return [
            'dashboard' => [
                self::VIEW_DASHBOARD,
            ],
            'users' => [
                self::VIEW_USERS,
                self::MANAGE_USERS,
                self::DELETE_USERS,
            ],
            'products' => [
                self::VIEW_PRODUCTS,
                self::MANAGE_PRODUCTS,
                self::DELETE_PRODUCTS,
            ],
            'brands' => [
                self::VIEW_BRANDS,
                self::MANAGE_BRANDS,
                self::DELETE_BRANDS,
            ],
            'categories' => [
                self::VIEW_CATEGORIES,
                self::MANAGE_CATEGORIES,
            ],
            'cities' => [
                self::VIEW_CITIES,
                self::MANAGE_CITIES,
            ],
            'delivery_couriers' => [
                self::VIEW_DELIVERY_COURIERS,
                self::MANAGE_DELIVERY_COURIERS,
            ],
            'delivery_courier_fees' => [
                self::VIEW_DELIVERY_COURIER_FEES,
                self::MANAGE_DELIVERY_COURIER_FEES,
            ],
            'orders' => [
                self::VIEW_ORDERS,
                self::MANAGE_ORDERS,
                self::DELETE_ORDERS,
            ],
            'affiliate' => [
                self::VIEW_COMMISSIONS,
                self::VIEW_OWN_PERFORMANCE,
                self::MANAGE_OWN_COUPON,
            ],
            'system' => [
                self::MANAGE_ROLES,
                self::MANAGE_PERMISSIONS,
                self::VIEW_SYSTEM_CONFIG,
            ],
        ];
    }
}
