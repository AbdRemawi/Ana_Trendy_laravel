<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryTransaction>
 */
class InventoryTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['supply', 'sale', 'return', 'damage', 'adjustment']);
        $quantity = fake()->numberBetween(1, 100);

        // Make sale and damage quantities positive (the model handles the logic)
        if (in_array($type, ['sale', 'damage'])) {
            $quantity = abs($quantity);
        }

        return [
            'product_id' => \App\Models\Product::factory(),
            'type' => $type,
            'quantity' => $quantity,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Create a supply transaction.
     */
    public function supply(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'supply',
            'quantity' => fake()->numberBetween(10, 500),
        ]);
    }

    /**
     * Create a sale transaction.
     */
    public function sale(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'sale',
            'quantity' => fake()->numberBetween(1, 50),
        ]);
    }

    /**
     * Create a return transaction.
     */
    public function return(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'return',
            'quantity' => fake()->numberBetween(1, 20),
        ]);
    }

    /**
     * Create a damage transaction.
     */
    public function damage(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'damage',
            'quantity' => fake()->numberBetween(1, 10),
        ]);
    }

    /**
     * Create an adjustment transaction.
     */
    public function adjustment(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'adjustment',
            'quantity' => fake()->numberBetween(-50, 50),
        ]);
    }
}
