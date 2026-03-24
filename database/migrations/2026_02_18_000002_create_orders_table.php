<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('full_name');
            $table->foreignId('city_id')
                ->constrained('cities')
                ->restrictOnDelete();
            $table->text('address');
            $table->foreignId('delivery_courier_id')
                ->nullable()
                ->constrained('delivery_couriers')
                ->nullOnDelete();
            $table->decimal('real_delivery_fee', 10, 3)->nullable();
            $table->decimal('subtotal_products', 10, 2);
            $table->foreignId('coupon_id')
                ->nullable()
                ->constrained('coupons')
                ->restrictOnDelete();
            $table->decimal('coupon_discount_amount', 10, 2)->default(0);
            $table->decimal('free_delivery_discount', 10, 3)->nullable();
            $table->decimal('actual_charge', 10, 2);
            $table->decimal('total_price_for_customer', 10, 2);
            $table->enum('status', [
                'processing',
                'with_delivery_company',
                'received',
                'cancelled',
                'returned'
            ])->default('processing');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('city_id');
            $table->index('delivery_courier_id');
            $table->index('coupon_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
