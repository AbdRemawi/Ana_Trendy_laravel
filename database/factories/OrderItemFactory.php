<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = \App\Models\Product::where('status', 'active')
            ->inRandomOrder()
            ->first();

        $quantity = fake()->numberBetween(1, 3);
        $basePrice = $product->sale_price;
        $salePrice = $product->offer_price ?? $product->sale_price;

        // Calculate discount per unit
        $discountPerUnit = max(0, $basePrice - $salePrice);

        // Calculate totals
        $totalPrice = $salePrice * $quantity;

        return [
            'order_id' => \App\Models\Order::factory(),
            'product_id' => $product->id,
            'quantity' => $quantity,
            'base_price' => $basePrice,
            'coupon_discount_per_unit' => $discountPerUnit,
            'unit_sale_price' => $salePrice,
            'unit_cost_price' => $product->cost_price,
            'total_price' => round($totalPrice, 2),
        ];
    }

    /**
     * Create an order item with discount.
     */
    public function withDiscount(): static
    {
        return $this->state(fn (array $attributes) => [
            'coupon_discount_per_unit' => fake()->randomFloat(2, 1, 20),
        ]);
    }

    /**
     * Create an order item for a specific order.
     */
    public function forOrder($orderId): static
    {
        return $this->state(fn (array $attributes) => [
            'order_id' => $orderId,
        ]);
    }

    /**
     * Create an order item for a specific product.
     */
    public function forProduct($productId): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => $productId,
        ]);
    }
}
