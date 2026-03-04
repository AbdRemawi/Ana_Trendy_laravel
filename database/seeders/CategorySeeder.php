<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Category Seeder
 *
 * Seeds the categories table with 3 main categories:
 * - Handbags
 * - Shoes
 * - Accessories
 *
 * Creates placeholder images if they don't exist.
 */
class CategorySeeder extends Seeder
{
    /**
     * Categories data configuration.
     *
     * @var array<int, array{name: string, slug: string, image: string, sort_order: int}>
     */
    protected array $categories = [
        [
            'name' => 'Handbags',
            'slug' => 'handbags',
            'image' => 'categories/handbags.svg',
            'sort_order' => 1,
        ],
        [
            'name' => 'Shoes',
            'slug' => 'shoes',
            'image' => 'categories/shoes.svg',
            'sort_order' => 2,
        ],
        [
            'name' => 'Accessories',
            'slug' => 'accessories',
            'image' => 'categories/accessories.svg',
            'sort_order' => 3,
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📁 Seeding categories...');

        // Ensure categories directory exists in storage
        Storage::disk('public')->makeDirectory('categories');

        $count = 0;

        foreach ($this->categories as $categoryData) {
            $this->createCategory($categoryData);
            $count++;
        }

        $this->command->info("✅ Successfully seeded {$count} categories.");
        $this->command->newLine();
    }

    /**
     * Create or update a category.
     *
     * @param array{name: string, slug: string, image: string, sort_order: int} $data
     */
    protected function createCategory(array $data): void
    {
        // Create placeholder image if it doesn't exist
        $this->ensurePlaceholderImage($data['image'], $data['name']);

        // Create or update category using slug as unique identifier
        Category::updateOrCreate(
            ['slug' => $data['slug']],
            [
                'name' => $data['name'],
                'image' => $data['image'],
                'status' => Category::STATUS_ACTIVE,
                'sort_order' => $data['sort_order'],
                'parent_id' => null,
            ]
        );

        $this->command->info("   ✓ Category: {$data['name']}");
    }

    /**
     * Ensure a placeholder image exists for the category.
     *
     * @param string $path Relative path in storage/app/public
     * @param string $categoryName Category name to display on placeholder
     */
    protected function ensurePlaceholderImage(string $path, string $categoryName): void
    {
        if (Storage::disk('public')->exists($path)) {
            return;
        }

        // Generate and store SVG placeholder
        $svg = $this->generatePlaceholderSvg($categoryName);
        Storage::disk('public')->put($path, $svg);
    }

    /**
     * Generate a simple SVG placeholder for a category image.
     *
     * @param string $categoryName
     * @return string SVG content
     */
    protected function generatePlaceholderSvg(string $categoryName): string
    {
        $initials = strtoupper(substr($categoryName, 0, 2));
        $bgColor = $this->getCategoryColor($categoryName);
        $icon = $this->getCategoryIcon($categoryName);

        return <<<SVG
<svg width="400" height="300" xmlns="http://www.w3.org/2000/svg">
    <rect width="400" height="300" fill="{$bgColor}" rx="12"/>
    <text x="200" y="140" font-family="Arial, sans-serif" font-size="64" font-weight="bold"
          fill="#FFFFFF" text-anchor="middle" dominant-baseline="middle" opacity="0.3">
        {$icon}
    </text>
    <text x="200" y="180" font-family="Arial, sans-serif" font-size="28" font-weight="600"
          fill="#FFFFFF" text-anchor="middle" dominant-baseline="middle">
        {$this->escapeXml($categoryName)}
    </text>
</svg>
SVG;
    }

    /**
     * Get a distinctive icon character for each category.
     *
     * @param string $categoryName
     * @return string Icon character
     */
    protected function getCategoryIcon(string $categoryName): string
    {
        return match ($categoryName) {
            'Handbags' => '👜',
            'Shoes' => '👠',
            'Accessories' => '⌚',
            default => '📦',
        };
    }

    /**
     * Get a distinct color for each category.
     *
     * @param string $categoryName
     * @return string Hex color
     */
    protected function getCategoryColor(string $categoryName): string
    {
        return match ($categoryName) {
            'Handbags' => '#8B5A2B', // Saddle brown
            'Shoes' => '#CD5C5C', // Indian red
            'Accessories' => '#4682B4', // Steel blue
            default => '#6C757D',
        };
    }

    /**
     * Escape special XML characters.
     *
     * @param string $text
     * @return string
     */
    protected function escapeXml(string $text): string
    {
        return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
