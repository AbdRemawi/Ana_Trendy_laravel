<?php

namespace Database\Seeders;

use App\Enums\SpinnerPrizeType;
use App\Models\SpinnerPrize;
use Illuminate\Database\Seeder;

class SpinnerPrizeSeeder extends Seeder
{
    /**
     * Seed the spinner wheel segments.
     *
     * Weights are RELATIVE probabilities among segments with weight > 0.
     * A weight of 0 means the segment is displayed on the wheel but can
     * never be won (e.g. the 50% decoy).
     *
     * Requested behaviour:
     *  - خصم 5 دنانير (5 JOD)  => highest chance (main winning prize)
     *  - خصم 7 دنانير (7 JOD)  => second highest chance
     *  - خصم دينارين  (2 JOD)  => smaller chance
     *  - حظ أوفر      (no prize) => decoy, shown in low priority (last)
     *  - حاول مرة أخرى (try again) => lets the device spin again
     *  - خصم 50%      (percentage) => never wins (weight 0)
     *
     * So among the winnable discounts the odds rank: 5 دنانير > 7 دنانير > دينارين.
     */
    public function run(): void
    {
        $prizes = [
            [
                'label' => 'خصم دينارين',
                'type' => SpinnerPrizeType::COUPON_FIXED->value,
                'coupon_value' => 2,
                'coupon_min_order' => 0,
                'coupon_validity_days' => 30,
                'weight' => 7,           // smaller chance
                'color' => '#F4A9C7',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'label' => 'خصم 5 دنانير',
                'type' => SpinnerPrizeType::COUPON_FIXED->value,
                'coupon_value' => 5,
                'coupon_min_order' => 0,
                'coupon_validity_days' => 30,
                'weight' => 75,          // highest chance – the main winning prize
                'color' => '#B57EDC',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'label' => 'خصم 7 دنانير',
                'type' => SpinnerPrizeType::COUPON_FIXED->value,
                'coupon_value' => 7,
                'coupon_min_order' => 0,
                'coupon_validity_days' => 30,
                'weight' => 40,          // second highest chance
                'color' => '#9AD37E',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'label' => 'خصم 10 دينار',
                'type' => SpinnerPrizeType::COUPON_FIXED->value,
                'coupon_value' => 10,
                'coupon_min_order' => 0,
                'coupon_validity_days' => 30,
                'weight' => 0,           // decoy – shown on the wheel but never lands
                'color' => '#7EB8DC',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'label' => 'خصم 50%',
                'type' => SpinnerPrizeType::COUPON_PERCENTAGE->value,
                'coupon_value' => 50,
                'coupon_min_order' => 0,
                'coupon_validity_days' => 30,
                'weight' => 0,           // decoy – never lands
                'color' => '#FFD166',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'label' => 'حاول مرة أخرى',
                'type' => SpinnerPrizeType::TRY_AGAIN->value,
                'coupon_value' => null,
                'coupon_min_order' => 0,
                'coupon_validity_days' => 30,
                'weight' => 18,          // allows a re-spin (unchanged)
                'color' => '#7ED0C0',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'label' => 'حظ أوفر',
                'type' => SpinnerPrizeType::NO_PRIZE->value,
                'coupon_value' => null,
                'coupon_min_order' => 0,
                'coupon_validity_days' => 30,
                'weight' => 0,           // decoy – shown last but never lands
                'color' => '#C9CBCF',
                'sort_order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($prizes as $prize) {
            SpinnerPrize::updateOrCreate(
                ['label' => $prize['label']],
                $prize
            );
        }
    }
}
