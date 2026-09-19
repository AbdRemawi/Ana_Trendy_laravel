<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\SpinnerPrize;
use App\Models\SpinnerSpin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SpinnerService
{
    /**
     * Determine whether a device is allowed to spin right now.
     *
     * Rule: one spin per device per calendar day, UNLESS the most recent
     * spin today landed on "try again" (can_respin), in which case the
     * device may spin once more.
     */
    public function canPlay(string $deviceId): bool
    {
        $lastSpinToday = $this->lastSpinToday($deviceId);

        if (!$lastSpinToday) {
            return true;
        }

        return (bool) $lastSpinToday->can_respin;
    }

    /**
     * The most recent spin the device made today (or null).
     */
    public function lastSpinToday(string $deviceId): ?SpinnerSpin
    {
        return SpinnerSpin::where('device_id', $deviceId)
            ->playedToday()
            ->latest('id')
            ->first();
    }

    /**
     * The latest still-usable coupon this device has won and not yet redeemed.
     * Used so checkout only ever sees the LAST prize, not every past prize.
     */
    public function activeReward(string $deviceId): ?SpinnerSpin
    {
        return SpinnerSpin::where('device_id', $deviceId)
            ->where('is_win', true)
            ->where('is_redeemed', false)
            ->whereNotNull('coupon_id')
            ->whereHas('coupon', fn ($q) => $q->where('is_active', true))
            ->latest('id')
            ->first();
    }

    /**
     * Execute a spin: pick a weighted segment, generate a coupon if the
     * segment is a winning one, supersede any older un-redeemed reward, and
     * persist the spin. Returns the created SpinnerSpin.
     *
     * Caller MUST check canPlay() first.
     */
    public function spin(string $deviceId, ?string $ip = null): SpinnerSpin
    {
        return DB::transaction(function () use ($deviceId, $ip) {
            $prize = $this->pickWeightedPrize();
            $type = $prize->typeEnum();

            $coupon = null;
            if ($type->isWinning()) {
                // Keep only the newest reward valid: deactivate previous
                // un-redeemed spinner coupons for this device so a customer
                // who plays again before checking out can only use the last one.
                $this->deactivatePreviousRewards($deviceId);
                $coupon = $this->generateCoupon($prize);
            }

            return SpinnerSpin::create([
                'device_id' => $deviceId,
                'token' => (string) Str::uuid(),
                'spinner_prize_id' => $prize->id,
                'prize_label' => $prize->label,
                'result_type' => $prize->type,
                'coupon_id' => $coupon?->id,
                'is_win' => $type->isWinning(),
                'can_respin' => $type->allowsRespin(),
                'is_redeemed' => false,
                'ip_address' => $ip,
                'played_on' => today(),
            ]);
        });
    }

    /**
     * Attach the customer's phone number to a spin (from the claim modal).
     * Returns the updated spin, or null if the token is unknown.
     */
    public function attachPhone(string $token, string $phone): ?SpinnerSpin
    {
        $spin = SpinnerSpin::where('token', $token)->first();

        if (!$spin) {
            return null;
        }

        $spin->update(['phone_number' => $phone]);

        return $spin;
    }

    /**
     * Pick one winnable segment using weighted random selection.
     * Segments with weight 0 (e.g. the 50% decoy) can never be chosen.
     */
    public function pickWeightedPrize(): SpinnerPrize
    {
        $prizes = SpinnerPrize::winnable()->get();

        if ($prizes->isEmpty()) {
            throw new \RuntimeException('No winnable spinner prizes are configured.');
        }

        $totalWeight = (int) $prizes->sum('weight');
        // random_int is cryptographically secure and unbiased.
        $roll = random_int(1, $totalWeight);

        $cursor = 0;
        foreach ($prizes as $prize) {
            $cursor += (int) $prize->weight;
            if ($roll <= $cursor) {
                return $prize;
            }
        }

        // Fallback (should be unreachable): return the last prize.
        return $prizes->last();
    }

    /**
     * Create a single-use coupon for a winning prize.
     */
    private function generateCoupon(SpinnerPrize $prize): Coupon
    {
        return Coupon::create([
            'code' => $this->generateUniqueCode(),
            'type' => $prize->typeEnum()->toCouponType(),
            'value' => $prize->coupon_value,
            'minimum_order_amount' => $prize->coupon_min_order,
            'max_uses' => 1, // single use ("use one time for order, not all time")
            'used_count' => 0,
            'valid_from' => now(),
            'valid_until' => now()->addDays($prize->coupon_validity_days),
            'is_active' => true,
        ]);
    }

    /**
     * Deactivate this device's previous un-redeemed spinner coupons so only
     * the most recent prize remains usable at checkout.
     */
    private function deactivatePreviousRewards(string $deviceId): void
    {
        $couponIds = SpinnerSpin::where('device_id', $deviceId)
            ->where('is_win', true)
            ->where('is_redeemed', false)
            ->whereNotNull('coupon_id')
            ->pluck('coupon_id')
            ->all();

        if (!empty($couponIds)) {
            Coupon::whereIn('id', $couponIds)->update(['is_active' => false]);
        }
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = 'SPIN' . strtoupper(Str::random(8));
        } while (Coupon::where('code', $code)->exists());

        return $code;
    }
}
