<?php

namespace App\Services;

use App\Models\Order;
use App\Models\DeliveryCourierFee;
use App\Enums\OrderStatus;
use App\Enums\CouponType;
use Illuminate\Support\Facades\DB;

class OrderStatusService
{
    public function __construct(
        private InventoryService $inventoryService
    ) {}

    public function transitionStatus(Order $order, OrderStatus $newStatus): void
    {
        $currentStatus = OrderStatus::from($order->status);

        if (!$currentStatus->canTransitionTo($newStatus)) {
            throw new \Exception(
                "Cannot transition from {$currentStatus->label()} to {$newStatus->label()}"
            );
        }

        DB::transaction(function () use ($order, $newStatus, $currentStatus) {
            if ($newStatus->decreasesInventory()) {
                if ($currentStatus->restoresInventory()) {
                    $errors = $this->inventoryService->validateStockAvailability(
                        $order->items->map(fn($item) => [
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                        ])->toArray()
                    );

                    if (!empty($errors)) {
                        throw new \Exception(implode(', ', $errors));
                    }
                }

                $this->inventoryService->decreaseStockForOrder($order);
            }

            if ($newStatus->restoresInventory()) {
                $this->inventoryService->restoreStockForOrder($order);
            }

            $order->update(['status' => $newStatus->value]);
        });
    }

    public function assignDeliveryCourier(
        Order $order,
        int $courierId,
    ): void {
        if (!$order->isProcessing()) {
            throw new \Exception('Can only assign courier to processing orders');
        }

        $fee = DeliveryCourierFee::where('delivery_courier_id', $courierId)
            ->where('city_id', $order->city_id)
            ->where('is_active', true)
            ->firstOrFail();

        $hasFreeDelivery = $order->coupon
            && $order->coupon->type === CouponType::FREE_DELIVERY->value;

        $deliveryFee = $hasFreeDelivery ? 0 : $fee->real_fee_amount;

        $order->update([
            'delivery_courier_id' => $courierId,
            'real_delivery_fee' => $deliveryFee,
            'display_delivery_fee' => $deliveryFee,
            'actual_charge' => round(
                $order->subtotal_products
                - $order->coupon_discount_amount
                + $deliveryFee,
                2
            ),
            'total_price_for_customer' => round(
                $order->subtotal_products
                - $order->coupon_discount_amount
                + $deliveryFee,
                2
            ),
            'status' => OrderStatus::WITH_DELIVERY_COMPANY->value,
        ]);
    }

    public function canTransitionTo(Order $order, OrderStatus $status): bool
    {
        return OrderStatus::from($order->status)->canTransitionTo($status);
    }

    public function getAllowedTransitions(Order $order): array
    {
        $current = OrderStatus::from($order->status);
        $allowed = [];

        foreach (OrderStatus::cases() as $status) {
            if ($current->canTransitionTo($status)) {
                $allowed[] = [
                    'value' => $status->value,
                    'label' => $status->label(),
                ];
            }
        }

        return $allowed;
    }
}
