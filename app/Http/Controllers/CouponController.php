<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateCouponRequest;
use App\Services\CouponValidationService;
use App\Services\OrderCalculationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct(
        private CouponValidationService $couponValidationService,
        private OrderCalculationService $calculationService
    ) {}

    public function validateCoupon(ValidateCouponRequest $request): JsonResponse
    {
        try {
            $result = $this->couponValidationService->validate(
                $request->coupon_code,
                $request->subtotal
            );

            if ($result['valid']) {
                $coupon = $result['coupon'];
                $discount = $this->calculationService->calculateCouponDiscount(
                    $request->subtotal,
                    $coupon
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Coupon is valid',
                    'data' => [
                        'coupon' => $coupon,
                        'discount_amount' => $discount,
                        'subtotal_after_discount' => round($request->subtotal - $discount, 2),
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'],
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function calculatePreview(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'items' => ['required', 'array', 'min:1'],
                'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
                'items.*.quantity' => ['required', 'integer', 'min:1'],
                'coupon_code' => ['nullable', 'string', 'exists:coupons,code'],
            ]);

            $items = $request->input('items');
            $couponCode = $request->input('coupon_code');

            $subtotal = $this->calculationService->calculateSubtotal($items);

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

            $preparedItems = $this->calculationService->distributeCouponAcrossItems(
                $items,
                $couponDiscount
            );

            $orderTotals = $this->calculationService->calculateOrderTotals(
                $preparedItems,
                $couponDiscount
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'subtotal' => $subtotal,
                    'coupon_discount' => $couponDiscount,
                    'coupon' => $coupon,
                    'coupon_error' => $couponError,
                    'total' => $orderTotals['total_price_for_customer'],
                    'items' => $preparedItems,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
