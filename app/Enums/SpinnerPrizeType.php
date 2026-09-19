<?php

namespace App\Enums;

use App\Enums\CouponType;

/**
 * Spinner Prize Type Enum
 *
 * Describes what happens when a wheel segment is landed on:
 *  - COUPON_FIXED       => a fixed-amount discount coupon is generated (e.g. 2 JOD, 5 JOD)
 *  - COUPON_PERCENTAGE  => a percentage discount coupon is generated (e.g. 50%)
 *  - TRY_AGAIN          => no prize, but the device is allowed to spin again the same day
 *  - NO_PRIZE           => "better luck next time" – no coupon, terminal result
 */
enum SpinnerPrizeType: string
{
    case COUPON_FIXED = 'coupon_fixed';
    case COUPON_PERCENTAGE = 'coupon_percentage';
    case TRY_AGAIN = 'try_again';
    case NO_PRIZE = 'no_prize';

    /**
     * Whether landing on this segment awards a coupon.
     */
    public function isWinning(): bool
    {
        return in_array($this, [self::COUPON_FIXED, self::COUPON_PERCENTAGE], true);
    }

    /**
     * Whether landing on this segment lets the device spin again today.
     */
    public function allowsRespin(): bool
    {
        return $this === self::TRY_AGAIN;
    }

    /**
     * Map a coupon-generating prize type to the matching CouponType value.
     */
    public function toCouponType(): ?string
    {
        return match ($this) {
            self::COUPON_FIXED => CouponType::FIXED->value,
            self::COUPON_PERCENTAGE => CouponType::PERCENTAGE->value,
            default => null,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
