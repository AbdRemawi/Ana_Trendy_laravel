<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);
        $slug = Str::slug($name) . '-' . fake()->unique()->numerify('####');
        $costPrice = fake()->randomFloat(2, 10, 500);
        $salePrice = $costPrice * fake()->randomFloat(1, 1.2, 2.5);
        $salePrice = round($salePrice, 2);
        $hasOffer = fake()->boolean(30); // 30% chance of having an offer
        $offerPrice = $hasOffer ? round($salePrice * fake()->randomFloat(1, 0.7, 0.95), 2) : null;

        return [
            'brand_id' => \App\Models\Brand::inRandomOrder()->first()->id ?? \App\Models\Brand::factory(),
            'category_id' => \App\Models\Category::inRandomOrder()->first()->id ?? \App\Models\Category::factory(),
            'name' => $name,
            'slug' => $slug,
            'description' => fake()->optional()->paragraph(),
            'size' => fake()->randomElement(['S', 'MD', 'LG']),
            'gender' => fake()->randomElement(['male', 'female', 'unisex']),
            'cost_price' => $costPrice,
            'sale_price' => $salePrice,
            'offer_price' => $offerPrice,
            'status' => 'active',
        ];
    }

    /**
     * Indicate that the product is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the product has an offer.
     */
    public function withOffer(): static
    {
        return $this->state(fn (array $attributes) => [
            'offer_price' => $attributes['sale_price'] * 0.8,
        ]);
    }

    /**
     * Set a specific size for the product.
     */
    public function size(string $size): static
    {
        return $this->state(fn (array $attributes) => [
            'size' => $size,
        ]);
    }

    /**
     * Set a specific gender for the product.
     */
    public function gender(string $gender): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => $gender,
        ]);
    }
}
