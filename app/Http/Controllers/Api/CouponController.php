<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ValidateCouponRequest;
use App\Services\CouponValidationService;
use App\Services\OrderCalculationService;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    public function __construct(
        private CouponValidationService $couponValidationService,
        private OrderCalculationService $calculationService
    ) {}

    /**
     * Validate a coupon code and calculate discount details
     *
     * This endpoint validates a coupon code and returns detailed information
     * about the discount that will be applied to the order.
     *
     * @param ValidateCouponRequest $request
     * @return JsonResponse
     */
    public function validateCoupon(ValidateCouponRequest $request): JsonResponse
    {
        $result = $this->couponValidationService->validate(
            $request->coupon_code,
            $request->subtotal
        );

        if (!$result['valid']) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon',
                'error' => $result['error'],
                'data' => [
                    'coupon_code' => $request->coupon_code,
                    'is_valid' => false,
                    'subtotal' => $request->subtotal,
                    'discount_amount' => 0,
                    'total_after_discount' => $request->subtotal,
                ],
            ], 422);
        }

        $coupon = $result['coupon'];
        $discountAmount = $this->calculationService->calculateCouponDiscount(
            $request->subtotal,
            $coupon
        );

        $totalAfterDiscount = $request->subtotal - $discountAmount;

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully',
            'data' => [
                'coupon' => [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'minimum_order_amount' => $coupon->minimum_order_amount,
                ],
                'is_valid' => true,
                'subtotal' => (float) $request->subtotal,
                'discount_amount' => (float) $discountAmount,
                'total_after_discount' => (float) $totalAfterDiscount,
                'savings' => [
                    'amount' => (float) $discountAmount,
                    'percentage' => $coupon->type === 'percentage'
                        ? (float) $coupon->value
                        : round(($discountAmount / $request->subtotal) * 100, 2),
                ],
            ],
        ], 200);
    }

    /**
     * Calculate order preview with optional coupon discount
     *
     * This endpoint provides a full preview of order calculations including
     * coupon discounts, itemized breakdown, and totals.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function preview(\Illuminate\Http\Request $request): JsonResponse
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'coupon_code' => ['nullable', 'string'],
            'shipping' => ['nullable', 'numeric', 'min:0'],
        ]);

        $items = $request->input('items');
        $couponCode = $request->input('coupon_code');
        $shipping = (float) ($request->input('shipping') ?? 0);

        // Calculate subtotal from cart items
        $cartItems = collect($items)->map(fn($item) => [
            'product_id' => $item['id'],
            'quantity' => $item['quantity']
        ])->toArray();

        $subtotal = $this->calculationService->calculateSubtotal($cartItems);

        // Validate and apply coupon if provided
        $coupon = null;
        $couponDiscount = 0;
        $couponError = null;

        if ($couponCode) {
            $validation = $this->couponValidationService->validate($couponCode, $subtotal);

            if ($validation['valid']) {
                $coupon = $validation['coupon'];
                $couponDiscount = $this->calculationService->calculateCouponDiscount(
                    $subtotal,
                    $coupon
                );
            } else {
                $couponError = $validation['error'];
            }
        }

        // Prepare items with discount distribution
        $preparedItems = $this->calculationService->distributeCouponAcrossItems(
            $cartItems,
            $couponDiscount
        );

        $totalAfterDiscount = $subtotal - $couponDiscount;
        $totalWithShipping = $totalAfterDiscount + $shipping;

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $preparedItems,
                'summary' => [
                    'subtotal' => (float) $subtotal,
                    'shipping' => (float) $shipping,
                    'coupon_discount' => (float) $couponDiscount,
                    'coupon' => $coupon ? [
                        'code' => $coupon->code,
                        'type' => $coupon->type,
                        'value' => $coupon->value,
                    ] : null,
                    'coupon_error' => $couponError,
                    'total' => (float) $totalWithShipping,
                ],
            ],
        ], 200);
    }
}
