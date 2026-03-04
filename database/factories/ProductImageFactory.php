<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImage>
 */
class ProductImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure products directory exists
        Storage::disk('public')->makeDirectory('products');

        $imageName = fake()->uuid() . '.jpg';
        $imagePath = 'products/' . $imageName;

        // Create a placeholder image
        $this->ensurePlaceholderImage($imagePath);

        return [
            'product_id' => \App\Models\Product::factory(),
            'image_path' => $imagePath,
            'is_primary' => false,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the image is primary.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }

    /**
     * Set the sort order.
     */
    public function sortOrder(int $order): static
    {
        return $this->state(fn (array $attributes) => [
            'sort_order' => $order,
        ]);
    }

    /**
     * Ensure a placeholder image exists.
     *
     * @param string $path Relative path in storage/app/public
     */
    protected function ensurePlaceholderImage(string $path): void
    {
        if (Storage::disk('public')->exists($path)) {
            return;
        }

        // Generate and store SVG placeholder for product images
        $svg = $this->generatePlaceholderSvg();
        Storage::disk('public')->put($path, $svg);
    }

    /**
     * Generate a simple SVG placeholder for a product image.
     *
     * @return string SVG content
     */
    protected function generatePlaceholderSvg(): string
    {
        $bgColor = fake()->randomElement([
            '#F5F5DC', // Beige
            '#E8DCC8', // Light tan
            '#D4C4B0', // Tan
            '#C8B8A0', // Medium tan
            '#E0E0E0', // Light gray
        ]);

        return <<<SVG
<svg width="600" height="600" xmlns="http://www.w3.org/2000/svg">
    <rect width="600" height="600" fill="{$bgColor}"/>
    <text x="300" y="300" font-family="Arial, sans-serif" font-size="48" font-weight="500"
          fill="#666666" text-anchor="middle" dominant-baseline="middle" opacity="0.5">
        Product Image
    </text>
</svg>
SVG;
    }
}
