<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds performance optimization indexes to existing tables.
     * These indexes improve query performance for common filters and searches.
     */
    public function up(): void
    {
        // Products table - add indexes for commonly filtered columns
        Schema::table('products', function (Blueprint $table) {
            // Index for status filtering (active/inactive)
            $table->index('status', 'idx_products_status');

            // Index for size filtering (S/M/L/XL/XXL)
            $table->index('size', 'idx_products_size');

            // Index for gender filtering (male/female/unisex)
            $table->index('gender', 'idx_products_gender');

            // Index for name searches (LIKE queries)
            $table->index('name', 'idx_products_name');

            // Composite index for common filter combinations (brand + status)
            $table->index(['brand_id', 'status'], 'idx_products_brand_status');

            // Composite index for category + status
            $table->index(['category_id', 'status'], 'idx_products_category_status');

            // Composite index for gender + size + status (product browsing)
            $table->index(['gender', 'size', 'status'], 'idx_products_gender_size_status');
        });

        // Categories table - add indexes for hierarchical queries
        Schema::table('categories', function (Blueprint $table) {
            // Index for status filtering
            $table->index('status', 'idx_categories_status');

            // Index for parent_id (self-join for hierarchy)
            $table->index('parent_id', 'idx_categories_parent_id');

            // Index for sort ordering
            $table->index('sort_order', 'idx_categories_sort_order');
        });

        // Users table - add indexes for common searches
        Schema::table('users', function (Blueprint $table) {
            // Note: email and mobile already have unique indexes from Laravel default auth

            // Index for status filtering
            $table->index('status', 'idx_users_status');
        });

        // Product images table - optimize for primary image queries
        Schema::table('product_images', function (Blueprint $table) {
            // Composite index for product + primary flag
            $table->index(['product_id', 'is_primary'], 'idx_product_images_product_primary');

            // Index for sort order
            $table->index('sort_order', 'idx_product_images_sort_order');
        });

        // Inventory transactions table - optimize for stock calculations
        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Composite index for product + type (stock calculations)
            $table->index(['product_id', 'type'], 'idx_inventory_product_type');

            // Index for date-based queries
            $table->index('created_at', 'idx_inventory_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_status');
            $table->dropIndex('idx_products_size');
            $table->dropIndex('idx_products_gender');
            $table->dropIndex('idx_products_name');
            $table->dropIndex('idx_products_brand_status');
            $table->dropIndex('idx_products_category_status');
            $table->dropIndex('idx_products_gender_size_status');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('idx_categories_status');
            $table->dropIndex('idx_categories_parent_id');
            $table->dropIndex('idx_categories_sort_order');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_mobile');
            $table->dropIndex('idx_users_email');
            $table->dropIndex('idx_users_status');
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->dropIndex('idx_product_images_product_primary');
            $table->dropIndex('idx_product_images_sort_order');
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_inventory_product_type');
            $table->dropIndex('idx_inventory_created_at');
        });
    }
};
