<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Status filter (active products)
            $table->index('status', 'products_status_index');

            // Price filtering (COALESCE(offer_price, sale_price))
            // Individual indexes for offer_price and sale_price
            $table->index('sale_price', 'products_sale_price_index');
            $table->index('offer_price', 'products_offer_price_index');

            // Sorting by newest
            $table->index('created_at', 'products_created_at_index');

            // Composite index for common queries
            // Status + Brand (for brand filtering)
            $table->index(['status', 'brand_id'], 'products_status_brand_index');

            // Status + Category (for category filtering)
            $table->index(['status', 'category_id'], 'products_status_category_index');

            // Composite index for price sorting with status
            $table->index(['status', 'sale_price'], 'products_status_sale_price_index');
            $table->index(['status', 'offer_price'], 'products_status_offer_price_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_status_index');
            $table->dropIndex('products_sale_price_index');
            $table->dropIndex('products_offer_price_index');
            $table->dropIndex('products_created_at_index');
            $table->dropIndex('products_status_brand_index');
            $table->dropIndex('products_status_category_index');
            $table->dropIndex('products_status_sale_price_index');
            $table->dropIndex('products_status_offer_price_index');
        });
    }
};
