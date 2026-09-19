<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spinner_spins', function (Blueprint $table) {
            $table->id();
            // Opaque per-browser identifier generated on the front-end (localStorage UUID).
            $table->string('device_id', 100);
            // Public token used by the claim endpoint to attach a phone number to this spin.
            $table->uuid('token')->unique();

            $table->foreignId('spinner_prize_id')->constrained('spinner_prizes');
            // Snapshots so admin history stays readable even if a prize is later edited/removed.
            $table->string('prize_label');
            $table->string('result_type');

            // Filled after the customer submits the modal.
            $table->string('phone_number', 30)->nullable();

            // Coupon generated for a winning spin (null for try_again / no_prize).
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();

            $table->boolean('is_win')->default(false);
            // True when the result is "try again" (device may spin once more today).
            $table->boolean('can_respin')->default(false);
            // Set to true once the generated coupon is consumed by a checkout.
            $table->boolean('is_redeemed')->default(false);

            $table->string('ip_address', 45)->nullable();
            // The calendar date the spin happened – drives the once-per-day rule.
            $table->date('played_on');

            $table->timestamps();

            $table->index(['device_id', 'played_on']);
            $table->index('phone_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spinner_spins');
    }
};
