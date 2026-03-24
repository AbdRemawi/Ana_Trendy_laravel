<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get random existing city
        $city = \App\Models\City::inRandomOrder()->first();
        $courier = \App\Models\DeliveryCourier::inRandomOrder()->first();
        $coupon = \App\Models\Coupon::where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where(function ($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>', now());
            })
            ->inRandomOrder()
            ->first();

        // Generate order items to calculate totals
        $itemCount = fake()->numberBetween(1, 5);
        $subtotalProducts = 0;
        $couponDiscountAmount = 0;
        $freeDeliveryDiscount = 0;

        // Get random products for this order
        $products = \App\Models\Product::where('status', 'active')
            ->inRandomOrder()
            ->limit($itemCount)
            ->get();

        foreach ($products as $product) {
            $qty = fake()->numberBetween(1, 3);
            $price = $product->offer_price ?? $product->sale_price;
            $subtotalProducts += $price * $qty;
        }

        // Apply coupon discount if applicable
        if ($coupon && $subtotalProducts >= $coupon->minimum_order_amount) {
            if ($coupon->type === 'fixed') {
                $couponDiscountAmount = min($coupon->value, $subtotalProducts);
            } elseif ($coupon->type === 'percentage') {
                $couponDiscountAmount = $subtotalProducts * ($coupon->value / 100);
            } elseif ($coupon->type === 'free_delivery') {
                $freeDeliveryDiscount = fake()->randomFloat(2, 3, 8);
            }
        }

        // Delivery fees
        $realDeliveryFee = fake()->randomFloat(3, 3, 10);

        // Calculate final charge
        $actualCharge = $subtotalProducts + $realDeliveryFee - $couponDiscountAmount - $freeDeliveryDiscount;
        $totalPriceForCustomer = max($actualCharge, 0);

        // Generate order number
        $orderNumber = 'ORD-' . fake()->unique()->numerify('######');

        // Random status with realistic distribution (50% received, 20% processing, 15% with_delivery, 10% cancelled, 5% returned)
        $statusArray = array_merge(
            array_fill(0, 50, OrderStatus::RECEIVED->value),
            array_fill(0, 20, OrderStatus::PROCESSING->value),
            array_fill(0, 15, OrderStatus::WITH_DELIVERY_COMPANY->value),
            array_fill(0, 10, OrderStatus::CANCELLED->value),
            array_fill(0, 5, OrderStatus::RETURNED->value),
        );
        $status = fake()->randomElement($statusArray);

        // Random date within last 30 days
        $createdAt = fake()->dateTimeBetween('-30 days', 'now');

        return [
            'order_number' => $orderNumber,
            'full_name' => fake()->name(),
            'city_id' => $city?->id ?? \App\Models\City::factory(),
            'address' => fake()->streetAddress() . ', ' . fake()->secondaryAddress(),
            'delivery_courier_id' => fake()->boolean(80) ? $courier?->id : null, // 80% have courier
            'real_delivery_fee' => $realDeliveryFee,
            'subtotal_products' => round($subtotalProducts, 2),
            'coupon_id' => $coupon?->id,
            'coupon_discount_amount' => round($couponDiscountAmount, 2),
            'free_delivery_discount' => $freeDeliveryDiscount > 0 ? round($freeDeliveryDiscount, 3) : null,
            'actual_charge' => round($actualCharge, 2),
            'total_price_for_customer' => round($totalPriceForCustomer, 2),
            'status' => $status,
            'notes' => fake()->optional(30)->sentence(),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }

    /**
     * Create a processing order.
     */
    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::PROCESSING->value,
        ]);
    }

    /**
     * Create an order with delivery company.
     */
    public function withDelivery(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::WITH_DELIVERY_COMPANY->value,
            'delivery_courier_id' => \App\Models\DeliveryCourier::inRandomOrder()->first()?->id,
        ]);
    }

    /**
     * Create a received (delivered) order.
     */
    public function received(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::RECEIVED->value,
            'delivery_courier_id' => \App\Models\DeliveryCourier::inRandomOrder()->first()?->id,
        ]);
    }

    /**
     * Create a cancelled order.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::CANCELLED->value,
        ]);
    }

    /**
     * Create a returned order.
     */
    public function returned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::RETURNED->value,
            'delivery_courier_id' => \App\Models\DeliveryCourier::inRandomOrder()->first()?->id,
        ]);
    }

    /**
     * Create an order with a coupon.
     */
    public function withCoupon(): static
    {
        return $this->state(fn (array $attributes) => [
            'coupon_id' => \App\Models\Coupon::inRandomOrder()->first()?->id,
        ]);
    }

    /**
     * Create an order from a specific date.
     */
    public function createdAt($date): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }
}
