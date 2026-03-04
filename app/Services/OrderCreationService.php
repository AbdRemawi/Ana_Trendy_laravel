<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderMobile;
use App\Models\Coupon;
use App\Enums\OrderStatus;
use App\Enums\CouponType;
use Illuminate\Support\Facades\DB;

class OrderCreationService
{
    public function __construct(
        private OrderCalculationService $calculationService,
        private CouponValidationService $couponValidationService,
        private InventoryService $inventoryService
    ) {}

    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $cartItems = $data['items'];
            $couponCode = $data['coupon_code'] ?? null;

            $coupon = null;
            $couponDiscountAmount = 0;
            $freeDeliveryDiscount = null;

            if ($couponCode) {
                $subtotal = $this->calculationService->calculateSubtotal($cartItems);
                $validation = $this->couponValidationService->validate($couponCode, $subtotal);

                if (!$validation['valid']) {
                    throw new \Exception($validation['error']);
                }

                $coupon = $validation['coupon'];
                $couponDiscountAmount = $this->calculationService->calculateCouponDiscount(
                    $subtotal,
                    $coupon
                );

                if ($coupon->type === CouponType::FREE_DELIVERY->value) {
                    $freeDeliveryDiscount = 0;
                }
            }

            $preparedItems = $this->calculationService->distributeCouponAcrossItems(
                $cartItems,
                $couponDiscountAmount
            );

            $stockErrors = $this->inventoryService->validateStockAvailability($cartItems);
            if (!empty($stockErrors)) {
                throw new \Exception(implode(', ', $stockErrors));
            }

            $orderTotals = $this->calculationService->calculateOrderTotals(
                $preparedItems,
                $couponDiscountAmount
            );

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'full_name' => $data['full_name'],
                'city_id' => $data['city_id'],
                'address' => $data['address'],
                'delivery_courier_id' => null,
                'display_delivery_fee' => null,
                'real_delivery_fee' => null,
                'subtotal_products' => $orderTotals['subtotal_products'],
                'coupon_id' => $coupon?->id,
                'coupon_discount_amount' => $orderTotals['coupon_discount_amount'],
                'free_delivery_discount' => $freeDeliveryDiscount,
                'actual_charge' => $orderTotals['actual_charge'],
                'total_price_for_customer' => $orderTotals['total_price_for_customer'],
                'status' => OrderStatus::PROCESSING->value,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($preparedItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'base_price' => $item['base_price'],
                    'coupon_discount_per_unit' => $item['coupon_discount_per_unit'],
                    'unit_sale_price' => $item['unit_sale_price'],
                    'unit_cost_price' => $item['unit_cost_price'],
                    'total_price' => $item['total_price'],
                ]);
            }

            foreach ($data['phone_numbers'] as $phoneNumber) {
                OrderMobile::create([
                    'order_id' => $order->id,
                    'phone_number' => $phoneNumber,
                ]);
            }

            $this->inventoryService->decreaseStockForOrder($order);

            if ($coupon) {
                $coupon->incrementUsedCount();
            }

            return $order;
        });
    }

    public function calculateOrderPreview(array $cartItems, ?string $couponCode = null): array
    {
        $subtotal = $this->calculationService->calculateSubtotal($cartItems);

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
            $cartItems,
            $couponDiscount
        );

        $orderTotals = $this->calculationService->calculateOrderTotals(
            $preparedItems,
            $couponDiscount
        );

        return [
            'subtotal' => $subtotal,
            'coupon_discount' => $couponDiscount,
            'coupon' => $coupon,
            'coupon_error' => $couponError,
            'total' => $orderTotals['total_price_for_customer'],
            'items' => $preparedItems,
        ];
    }

    private function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $latestOrder = Order::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $latestOrder
            ? (int) substr($latestOrder->order_number, -4) + 1
            : 1;

        return "ORD-{$date}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
