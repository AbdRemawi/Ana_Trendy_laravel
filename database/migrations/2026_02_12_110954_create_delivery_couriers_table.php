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
        Schema::create('delivery_couriers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_phone', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique constraint for active courier names
            $table->unique(['name', 'is_active'], 'uniq_courier_active_name');

            // Index for filtering active couriers
            $table->index('is_active', 'idx_delivery_couriers_is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_couriers');
    }
};
