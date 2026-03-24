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
        Schema::create('delivery_courier_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_courier_id')
                  ->constrained('delivery_couriers')
                  ->onDelete('cascade');
            $table->foreignId('city_id')
                  ->constrained('cities')
                  ->onDelete('cascade');
            $table->decimal('real_fee_amount', 10, 3);
            $table->string('currency', 3)->default('JOD');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // One fee record per courier per city
            $table->unique(['delivery_courier_id', 'city_id'], 'uniq_courier_city_fee');

            // Indexes for high-performance checkout queries
            $table->index('delivery_courier_id', 'idx_courier_fees_courier_id');
            $table->index('city_id', 'idx_courier_fees_city_id');
            $table->index('is_active', 'idx_courier_fees_is_active');
            $table->index(['delivery_courier_id', 'city_id', 'is_active'], 'idx_courier_fees_active_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_courier_fees');
    }
};
