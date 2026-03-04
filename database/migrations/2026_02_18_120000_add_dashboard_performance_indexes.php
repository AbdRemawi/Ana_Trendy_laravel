<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds performance indexes specifically for dashboard queries.
     * These indexes optimize the common dashboard queries for orders,
     * order_items, and related tables.
     */
    public function up(): void
    {
        // Orders table - optimize for dashboard date filter and status queries
        Schema::table('orders', function (Blueprint $table) {
            // Index for created_at (date filtering in dashboard)
            $table->index('created_at', 'idx_orders_created_at');

            // Index for status filtering (order status breakdown)
            $table->index('status', 'idx_orders_status');

            // Composite index for status + created_at (most common dashboard query)
            $table->index(['status', 'created_at'], 'idx_orders_status_created_at');

            // Index for coupon_id (coupon usage reports)
            $table->index('coupon_id', 'idx_orders_coupon_id');

            // Index for delivery_courier_id (delivery performance)
            $table->index('delivery_courier_id', 'idx_orders_delivery_courier_id');

            // Index for city_id (delivery by city reports)
            $table->index('city_id', 'idx_orders_city_id');
        });

        // Order items table - optimize for product sales and profit calculations
        Schema::table('order_items', function (Blueprint $table) {
            // Index for product_id (top selling products)
            $table->index('product_id', 'idx_order_items_product_id');

            // Index for order_id (joining with orders)
            $table->index('order_id', 'idx_order_items_order_id');

            // Composite index for order + product (efficient joins)
            $table->index(['order_id', 'product_id'], 'idx_order_items_order_product');
        });

        // Inventory transactions table - additional indexes
        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Index for type alone (filtering by transaction type)
            $table->index('type', 'idx_inventory_type');

            // Composite index for product + created_at (recent movements)
            $table->index(['product_id', 'created_at'], 'idx_inventory_product_created_at');
        });

        // Products table - additional index for dashboard calculations
        Schema::table('products', function (Blueprint $table) {
            // Composite index for brand + status + deleted_at (product reports)
            $table->index(['brand_id', 'status', 'deleted_at'], 'idx_products_brand_status_deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_created_at');
            $table->dropIndex('idx_orders_status');
            $table->dropIndex('idx_orders_status_created_at');
            $table->dropIndex('idx_orders_coupon_id');
            $table->dropIndex('idx_orders_delivery_courier_id');
            $table->dropIndex('idx_orders_city_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('idx_order_items_product_id');
            $table->dropIndex('idx_order_items_order_id');
            $table->dropIndex('idx_order_items_order_product');
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_inventory_type');
            $table->dropIndex('idx_inventory_product_created_at');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_brand_status_deleted');
        });
    }
};
