<?php

namespace App\Services;

use App\Models\User;

/**
 * Affiliate Service
 *
 * Handles affiliate-related business logic:
 * - Coupon code generation
 * - Commission calculations
 * - Affiliate validation
 *
 * Extracted from UserController for better separation of concerns.
 */
class AffiliateService
{
    /**
     * Generate a unique affiliate coupon code.
     *
     * Creates a random coupon code and ensures uniqueness.
     * Format: AFF-XXXXXXXX (8 random uppercase hex characters)
     *
     * @return string
     */
    public function generateUniqueCouponCode(): string
    {
        do {
            $code = 'AFF-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        } while (User::where('coupon_code', $code)->exists());

        return $code;
    }

    /**
     * Generate affiliate coupon code for a user.
     *
     * @param User $user
     * @return string The generated coupon code
     */
    public function generateCouponForUser(User $user): string
    {
        $couponCode = $this->generateUniqueCouponCode();

        $user->update([
            'coupon_code' => $couponCode,
        ]);

        return $couponCode;
    }

    /**
     * Ensure user has an affiliate coupon code.
     *
     * Generates one if not already exists.
     *
     * @param User $user
     * @return string The coupon code
     */
    public function ensureUserHasCoupon(User $user): string
    {
        if ($user->coupon_code) {
            return $user->coupon_code;
        }

        return $this->generateCouponForUser($user);
    }

    /**
     * Validate commission rate.
     *
     * @param float|null $rate
     * @return bool
     */
    public function isValidCommissionRate(?float $rate): bool
    {
        if ($rate === null) {
            return true; // Optional
        }

        return $rate >= 0 && $rate <= 100;
    }

    /**
     * Calculate commission amount from a sale.
     *
     * @param float $saleAmount
     * @param float $commissionRate
     * @return float
     */
    public function calculateCommission(float $saleAmount, float $commissionRate): float
    {
        return round($saleAmount * ($commissionRate / 100), 2);
    }

    /**
     * Check if a user is an affiliate.
     *
     * @param User $user
     * @return bool
     */
    public function isAffiliate(User $user): bool
    {
        return $user->hasRole('affiliate');
    }

    /**
     * Check if coupon code is valid format.
     *
     * @param string $code
     * @return bool
     */
    public function isValidCouponFormat(string $code): bool
    {
        return preg_match('/^AFF-[A-F0-9]{8}$/', $code) === 1;
    }

    /**
     * Find user by affiliate coupon code.
     *
     * @param string $couponCode
     * @return User|null
     */
    public function findUserByCoupon(string $couponCode): ?User
    {
        return User::where('coupon_code', $couponCode)->first();
    }
}
