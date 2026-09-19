<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spinner_prizes', function (Blueprint $table) {
            $table->id();
            // Arabic (or localized) label rendered on the wheel segment.
            $table->string('label');
            // What happens on a win: coupon_fixed | coupon_percentage | try_again | no_prize
            $table->enum('type', ['coupon_fixed', 'coupon_percentage', 'try_again', 'no_prize']);
            // Discount amount for coupon prizes (JOD for fixed, percent for percentage). Null otherwise.
            $table->decimal('coupon_value', 10, 2)->nullable();
            // Minimum order amount required for the generated coupon.
            $table->decimal('coupon_min_order', 10, 2)->default(0);
            // How many days the generated coupon stays valid.
            $table->unsignedInteger('coupon_validity_days')->default(30);
            // Relative probability weight. 0 => never lands (e.g. the 50% decoy).
            $table->unsignedInteger('weight')->default(0);
            // Hex color for the wheel segment (front-end rendering).
            $table->string('color', 20)->nullable();
            // Display order on the wheel (also used for "low priority" placement).
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spinner_prizes');
    }
};
