<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Coupon;
use App\Enums\CouponType;

class OrderCalculationService
{
    public function calculateSubtotal(array $cartItems): float
    {
        return collect($cartItems)->sum(function ($item) {
            $product = Product::findOrFail($item['product_id']);
            $basePrice = $product->offer_price ?? $product->sale_price;
            return $basePrice * $item['quantity'];
        });
    }

    public function calculateCouponDiscount(
        float $subtotal,
        Coupon $coupon
    ): float {
        return match ($coupon->type) {
            CouponType::FIXED->value => min($coupon->value, $subtotal),
            CouponType::PERCENTAGE->value => $subtotal * ($coupon->value / 100),
            CouponType::FREE_DELIVERY->value => 0,
            default => 0,
        };
    }

    public function distributeCouponAcrossItems(
        array $cartItems,
        float $totalCouponDiscount
    ): array {
        if ($totalCouponDiscount <= 0) {
            return $this->prepareItemsWithoutDiscount($cartItems);
        }

        $totalUnits = collect($cartItems)->sum('quantity');

        return collect($cartItems)->map(function ($item) use ($totalCouponDiscount, $totalUnits) {
            $product = Product::findOrFail($item['product_id']);
            $basePrice = $product->offer_price ?? $product->sale_price;

            $itemUnits = $item['quantity'];
            $discountShare = ($itemUnits / $totalUnits) * $totalCouponDiscount;
            $discountPerUnit = $discountShare / $itemUnits;

            $unitSalePrice = round($basePrice - $discountPerUnit, 2);
            $totalPrice = round($unitSalePrice * $item['quantity'], 2);

            return [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'base_price' => $basePrice,
                'coupon_discount_per_unit' => round($discountPerUnit, 2),
                'unit_sale_price' => $unitSalePrice,
                'unit_cost_price' => $product->cost_price,
                'total_price' => $totalPrice,
            ];
        })->all();
    }

    private function prepareItemsWithoutDiscount(array $cartItems): array
    {
        return collect($cartItems)->map(function ($item) {
            $product = Product::findOrFail($item['product_id']);
            $basePrice = $product->offer_price ?? $product->sale_price;

            return [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'base_price' => $basePrice,
                'coupon_discount_per_unit' => 0,
                'unit_sale_price' => $basePrice,
                'unit_cost_price' => $product->cost_price,
                'total_price' => round($basePrice * $item['quantity'], 2),
            ];
        })->all();
    }

    public function calculateOrderTotals(array $preparedItems, float $couponDiscountAmount): array
    {
        $subtotalProducts = collect($preparedItems)->sum('total_price');

        return [
            'subtotal_products' => $subtotalProducts,
            'coupon_discount_amount' => $couponDiscountAmount,
            'actual_charge' => $subtotalProducts,
            'total_price_for_customer' => $subtotalProducts,
        ];
    }

    public function calculateWithDelivery(
        array $orderTotals,
        float $realDeliveryFee,
        float $displayDeliveryFee,
        ?float $freeDeliveryDiscount
    ): array {
        $actualRealFee = $freeDeliveryDiscount !== null ? 0 : $realDeliveryFee;
        $actualDisplayFee = $freeDeliveryDiscount !== null ? 0 : $displayDeliveryFee;

        return [
            'real_delivery_fee' => $actualRealFee,
            'display_delivery_fee' => $actualDisplayFee,
            'actual_charge' => round(
                $orderTotals['subtotal_products']
                - $orderTotals['coupon_discount_amount']
                + $actualRealFee,
                2
            ),
            'total_price_for_customer' => round(
                $orderTotals['subtotal_products']
                - $orderTotals['coupon_discount_amount']
                + $actualDisplayFee,
                2
            ),
        ];
    }

    public function validateCouponForSubtotal(Coupon $coupon, float $subtotal): bool
    {
        if (!$coupon->isValidNow()) {
            return false;
        }

        if (!$coupon->hasRemainingUses()) {
            return false;
        }

        if (!$coupon->isValidForOrderAmount($subtotal)) {
            return false;
        }

        return true;
    }
}
