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
        Schema::table('users', function (Blueprint $table) {
            $table->string('coupon_code')->nullable()->unique()->after('role');
            $table->decimal('commission_rate', 5, 2)->nullable()->after('coupon_code');
            $table->decimal('total_earnings', 10, 2)->default(0)->after('commission_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['coupon_code', 'commission_rate', 'total_earnings']);
        });
    }
};
