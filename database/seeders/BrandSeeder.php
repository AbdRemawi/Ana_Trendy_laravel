<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Brand Seeder
 *
 * Seeds the brands table with luxury fashion brands.
 * Creates placeholder logos if they don't exist.
 *
 * Brands included:
 * - Coach
 * - Gucci
 * - Yves Saint Laurent (YSL)
 * - Tory Burch
 * - Fendi
 * - Hermès
 */
class BrandSeeder extends Seeder
{
    /**
     * Brands data configuration.
     *
     * @var array<int, array{name: string, slug: string, logo: string}>
     */
    protected array $brands = [
        [
            'name' => 'Coach',
            'slug' => 'coach',
            'logo' => 'brands/coach.svg',
        ],
        [
            'name' => 'Gucci',
            'slug' => 'gucci',
            'logo' => 'brands/gucci.svg',
        ],
        [
            'name' => 'Yves Saint Laurent',
            'slug' => 'yves-saint-laurent',
            'logo' => 'brands/yves-saint-laurent.svg',
        ],
        [
            'name' => 'Tory Burch',
            'slug' => 'tory-burch',
            'logo' => 'brands/tory-burch.svg',
        ],
        [
            'name' => 'Fendi',
            'slug' => 'fendi',
            'logo' => 'brands/fendi.svg',
        ],
        [
            'name' => 'Hermès',
            'slug' => 'hermes',
            'logo' => 'brands/hermes.svg',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏷️  Seeding brands...');

        // Ensure brands directory exists in storage
        Storage::disk('public')->makeDirectory('brands');

        $count = 0;

        foreach ($this->brands as $brandData) {
            $this->createBrand($brandData);
            $count++;
        }

        $this->command->info("✅ Successfully seeded {$count} brands.");
        $this->command->newLine();
    }

    /**
     * Create or update a brand.
     *
     * @param array{name: string, slug: string, logo: string} $data
     */
    protected function createBrand(array $data): void
    {
        // Create placeholder logo if it doesn't exist
        $this->ensurePlaceholderLogo($data['logo'], $data['name']);

        // Create or update brand using slug as unique identifier
        Brand::updateOrCreate(
            ['slug' => $data['slug']],
            [
                'name' => $data['name'],
                'logo' => $data['logo'],
                'status' => Brand::STATUS_ACTIVE,
            ]
        );

        $this->command->info("   ✓ Brand: {$data['name']}");
    }

    /**
     * Ensure a placeholder logo exists for the brand.
     *
     * @param string $path Relative path in storage/app/public
     * @param string $brandName Brand name to display on placeholder
     */
    protected function ensurePlaceholderLogo(string $path, string $brandName): void
    {
        if (Storage::disk('public')->exists($path)) {
            return;
        }

        // Generate and store SVG placeholder
        $svg = $this->generatePlaceholderSvg($brandName);
        Storage::disk('public')->put($path, $svg);
    }

    /**
     * Generate a simple SVG placeholder for a brand logo.
     *
     * @param string $brandName
     * @return string SVG content
     */
    protected function generatePlaceholderSvg(string $brandName): string
    {
        $initials = $this->getBrandInitials($brandName);
        $bgColor = $this->getBrandColor($brandName);

        return <<<SVG
<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
    <rect width="200" height="200" fill="{$bgColor}" rx="16"/>
    <text x="100" y="100" font-family="Arial, sans-serif" font-size="48" font-weight="bold"
          fill="#FFFFFF" text-anchor="middle" dominant-baseline="middle">
        {$initials}
    </text>
    <text x="100" y="140" font-family="Arial, sans-serif" font-size="16" font-weight="normal"
          fill="#FFFFFF" text-anchor="middle" dominant-baseline="middle" opacity="0.9">
        {$this->escapeXml($brandName)}
    </text>
</svg>
SVG;
    }

    /**
     * Get initials from brand name.
     *
     * @param string $brandName
     * @return string
     */
    protected function getBrandInitials(string $brandName): string
    {
        // Special cases
        return match ($brandName) {
            'Yves Saint Laurent' => 'YSL',
            'Hermès' => 'H',
            default => strtoupper(substr($brandName, 0, 1)),
        };
    }

    /**
     * Get a distinct color for each brand.
     *
     * @param string $brandName
     * @return string Hex color
     */
    protected function getBrandColor(string $brandName): string
    {
        return match ($brandName) {
            'Coach' => '#D4AF37', // Gold
            'Gucci' => '#1A1A1A', // Black
            'Yves Saint Laurent' => '#4B0082', // Indigo/Purple
            'Tory Burch' => '#8B4513', // Saddle Brown
            'Fendi' => '#FFD700', // Yellow/Gold
            'Hermès' => '#FF6600', // Orange (Hermès orange)
            default => '#333333',
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
