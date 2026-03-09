<?php

namespace App\Services;

use App\Models\Coupon;

class CouponValidationService
{
    public function validate(string $code, float $orderAmount): array
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return [
                'valid' => false,
                'error' => 'Coupon code not found.',
            ];
        }

        if (!$coupon->is_active) {
            return [
                'valid' => false,
                'error' => 'This coupon is inactive.',
            ];
        }

        if ($coupon->isNotYetStarted()) {
            \Log::error('Coupon not yet started', [
                'coupon_code' => $coupon->code,
                'valid_from' => $coupon->valid_from ? $coupon->valid_from->format('Y-m-d H:i:s') : 'null',
                'valid_from_timezone' => $coupon->valid_from ? $coupon->valid_from->timezone : 'null',
                'now' => now()->format('Y-m-d H:i:s'),
                'now_timezone' => now()->timezone,
                'is_null' => is_null($coupon->valid_from),
                'comparison_result' => $coupon->valid_from ? now()->lt($coupon->valid_from) : 'null comparison',
            ]);
            return [
                'valid' => false,
                'error' => 'This coupon is not yet valid.',
            ];
        }

        if ($coupon->isExpired()) {
            return [
                'valid' => false,
                'error' => 'This coupon has expired.',
            ];
        }

        if (!$coupon->hasRemainingUses()) {
            return [
                'valid' => false,
                'error' => 'This coupon has reached maximum usage limit.',
            ];
        }

        if (!$coupon->isValidForOrderAmount($orderAmount)) {
            return [
                'valid' => false,
                'error' => "Minimum order amount of {$coupon->minimum_order_amount} required.",
            ];
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
        ];
    }

    public function validateCouponEntity(Coupon $coupon, float $orderAmount): array
    {
        if (!$coupon->isValidNow()) {
            if (!$coupon->is_active) {
                return [
                    'valid' => false,
                    'error' => 'This coupon is inactive.',
                ];
            }

            if ($coupon->isNotYetStarted()) {
                return [
                    'valid' => false,
                    'error' => 'This coupon is not yet valid.',
                ];
            }

            if ($coupon->isExpired()) {
                return [
                    'valid' => false,
                    'error' => 'This coupon has expired.',
                ];
            }
        }

        if (!$coupon->hasRemainingUses()) {
            return [
                'valid' => false,
                'error' => 'This coupon has reached maximum usage limit.',
            ];
        }

        if (!$coupon->isValidForOrderAmount($orderAmount)) {
            return [
                'valid' => false,
                'error' => "Minimum order amount of {$coupon->minimum_order_amount} required.",
            ];
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
        ];
    }
}
