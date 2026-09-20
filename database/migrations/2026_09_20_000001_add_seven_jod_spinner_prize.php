<?php

use App\Enums\SpinnerPrizeType;
use App\Models\SpinnerPrize;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Add the new "خصم 7 دنانير" (7 JOD) segment and re-balance the odds so that
     * among the winnable discounts the chances rank: 5 دنانير > 7 دنانير > دينارين.
     * Try-again and the decoys (10 دينار, 50%, حظ أوفر) are left untouched, aside
     * from shifting their display order to make room for the new segment.
     */
    public function up(): void
    {
        // Insert / update the new 7 JOD prize.
        SpinnerPrize::updateOrCreate(
            ['label' => 'خصم 7 دنانير'],
            [
                'type' => SpinnerPrizeType::COUPON_FIXED->value,
                'coupon_value' => 7,
                'coupon_min_order' => 0,
                'coupon_validity_days' => 30,
                'weight' => 40,          // second highest chance
                'color' => '#9AD37E',
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        // Push the segments that used to sit at/after position 3 one step down
        // so the new 7 JOD prize slots in right after "خصم 5 دنانير".
        SpinnerPrize::where('label', 'خصم 10 دينار')->update(['sort_order' => 4]);
        SpinnerPrize::where('label', 'خصم 50%')->update(['sort_order' => 5]);
        SpinnerPrize::where('label', 'حاول مرة أخرى')->update(['sort_order' => 6]);
        SpinnerPrize::where('label', 'حظ أوفر')->update(['sort_order' => 7]);
    }

    public function down(): void
    {
        SpinnerPrize::where('label', 'خصم 7 دنانير')->delete();

        // Restore the original display order.
        SpinnerPrize::where('label', 'خصم 10 دينار')->update(['sort_order' => 3]);
        SpinnerPrize::where('label', 'خصم 50%')->update(['sort_order' => 4]);
        SpinnerPrize::where('label', 'حاول مرة أخرى')->update(['sort_order' => 5]);
        SpinnerPrize::where('label', 'حظ أوفر')->update(['sort_order' => 6]);
    }
};
