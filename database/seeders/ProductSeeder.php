<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\InventoryTransaction;
use Illuminate\Database\Seeder;

/**
 * Product Seeder
 *
 * Seeds 130 products with:
 * - Valid relationships to existing brands and categories
 * - 2-4 images per product (exactly one primary image)
 * - Initial inventory supply transaction
 *
 * All products are active by default.
 */
class ProductSeeder extends Seeder
{
    /**
     * Number of products to create.
     */
    protected const int PRODUCT_COUNT = 130;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🛍️  Seeding products...');

        // Validate prerequisites
        $brandCount = \App\Models\Brand::count();
        $categoryCount = \App\Models\Category::count();

        if ($brandCount === 0) {
            $this->command->warn('   ⚠️  No brands found. Please run BrandSeeder first.');
            return;
        }

        if ($categoryCount === 0) {
            $this->command->warn('   ⚠️  No categories found. Please run CategorySeeder first.');
            return;
        }

        $this->command->info("   Found {$brandCount} brands and {$categoryCount} categories.");
        $this->command->newLine();

        // Create products with images and inventory
        Product::factory()
            ->count(self::PRODUCT_COUNT)
            ->afterCreating(function (Product $product) {
                $this->createProductImages($product);
                $this->createInitialInventory($product);
            })
            ->create();

        $this->command->info("✅ Successfully seeded " . self::PRODUCT_COUNT . " products.");
        $this->command->newLine();
    }

    /**
     * Create 2-4 images for a product with exactly one primary image.
     *
     * @param Product $product
     */
    protected function createProductImages(Product $product): void
    {
        $imageCount = fake()->numberBetween(2, 4);
        $primaryIndex = fake()->numberBetween(1, $imageCount);

        for ($i = 1; $i <= $imageCount; $i++) {
            ProductImage::factory()->create([
                'product_id' => $product->id,
                'is_primary' => ($i === $primaryIndex),
                'sort_order' => $i,
            ]);
        }
    }

    /**
     * Create initial inventory supply transaction for a product.
     *
     * @param Product $product
     */
    protected function createInitialInventory(Product $product): void
    {
        // Generate initial stock quantity between 10 and 200
        $quantity = fake()->numberBetween(10, 200);

        InventoryTransaction::factory()->supply()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'notes' => 'Initial stock',
        ]);
    }
}
