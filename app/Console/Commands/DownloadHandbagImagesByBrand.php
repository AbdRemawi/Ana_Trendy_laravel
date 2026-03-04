<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadHandbagImagesByBrand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:download-handbag-images
                            {--brands= : Comma-separated brand IDs to process (default: all)}
                            {--force : Force re-download even if image exists}
                            {--test : Test mode - only show what would be done}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download real handbag images organized by brands';

    /**
     * Handbag image search keywords for different brand styles.
     */
    protected array $brandKeywords = [
        'luxury' => ['luxury handbag', 'designer bag', 'leather purse', 'premium handbag'],
        'sport' => ['sport bag', 'athletic bag', 'gym bag', 'sport purse'],
        'casual' => ['casual handbag', 'tote bag', 'shoulder bag', 'everyday bag'],
        'elegant' => ['elegant purse', 'clutch bag', 'evening bag', 'formal handbag'],
        'travel' => ['travel bag', 'suitcase', 'luggage bag', 'travel purse'],
    ];

    /**
     * Image sources with fallback options.
     */
    protected array $imageSources = [
        // Unsplash (high quality fashion images)
        'unsplash',
        // LoremFlickr (category-based)
        'loremflickr',
        // Picsum (random quality photos)
        'picsum',
    ];

    /**
     * Track downloaded URLs to avoid duplicates.
     */
    protected array $downloadedUrls = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $testMode = $this->option('test');
        $force = $this->option('force');
        $specificBrands = $this->option('brands');

        $this->info('👜 Downloading Handbag Images by Brand...');
        $this->newLine();

        if ($testMode) {
            $this->warn('⚠️  TEST MODE - No actual changes will be made');
            $this->newLine();
        }

        // Get brands to process
        $query = Brand::active();

        if ($specificBrands) {
            $brandIds = explode(',', $specificBrands);
            $query->whereIn('id', $brandIds);
        }

        $brands = $query->get();

        if ($brands->isEmpty()) {
            $this->error('No active brands found.');
            return Command::FAILURE;
        }

        $this->info("Found {$brands->count()} brands to process:");
        $this->newLine();

        // Show brands with product counts
        $brandList = [];
        foreach ($brands as $brand) {
            $productCount = $brand->products()->active()->count();
            $brandList[] = [
                $brand->id,
                $brand->name,
                $productCount,
            ];
        }

        $this->table(
            ['ID', 'Brand Name', 'Products'],
            $brandList
        );

        $this->newLine();

        // Statistics
        $stats = [
            'brands_processed' => 0,
            'products_processed' => 0,
            'images_downloaded' => 0,
            'images_failed' => 0,
            'total_bytes' => 0,
        ];

        // Process each brand
        foreach ($brands as $brand) {
            $this->info("Processing brand: {$brand->name}");
            $this->processBrand($brand, $force, $testMode, $stats);
            $stats['brands_processed']++;
            $this->newLine();
        }

        // Show final statistics
        $this->info('✅ Processing Complete!');
        $this->newLine();

        $totalMb = round($stats['total_bytes'] / 1024 / 1024, 2);
        $this->table(
            ['Metric', 'Count'],
            [
                ['Brands processed', $stats['brands_processed']],
                ['Products processed', $stats['products_processed']],
                ['Images downloaded', $stats['images_downloaded']],
                ['Images failed', $stats['images_failed']],
                ['Total data', $totalMb . ' MB'],
            ]
        );

        if (!$testMode && $stats['images_downloaded'] > 0) {
            $this->newLine();
            $this->info('✨ All handbag images have been downloaded successfully!');
        }

        return Command::SUCCESS;
    }

    /**
     * Process all products for a specific brand.
     */
    protected function processBrand(Brand $brand, bool $force, bool $testMode, array &$stats): void
    {
        // Get brand's keyword category
        $keywords = $this->getKeywordsForBrand($brand);

        // Get all products for this brand
        $products = $brand->products()->active()->with('images')->get();

        if ($products->isEmpty()) {
            $this->warn("  No products found for this brand.");
            return;
        }

        $this->line("  Found {$products->count()} products");
        $this->line("  Using keywords: " . implode(', ', $keywords));
        $this->newLine();

        foreach ($products as $product) {
            $stats['products_processed']++;
            $this->processProduct($product, $keywords, $force, $testMode, $stats);
        }
    }

    /**
     * Process a single product's images.
     */
    protected function processProduct(Product $product, array $keywords, bool $force, bool $testMode, array &$stats): void
    {
        $images = $product->images;

        foreach ($images as $index => $image) {
            // Skip if already a real handbag image and not forcing
            if (!$force && $this->isRealHandbagImage($image->image_path)) {
                continue;
            }

            // Get unique image URL
            $imageUrl = $this->getHandbagImageUrl($keywords, $product->id, $index);

            if (!$imageUrl) {
                $stats['images_failed']++;
                continue;
            }

            if ($testMode) {
                $this->line("  TEST: Would download image for product #{$product->id}");
                $stats['images_downloaded']++;
                continue;
            }

            // Download and replace image
            $result = $this->downloadAndReplaceImage($image, $imageUrl);

            if ($result['success']) {
                $stats['images_downloaded']++;
                $stats['total_bytes'] += $result['bytes'];
                $this->line("  ✓ Downloaded: product #{$product->id}, image #{$image->id}");
            } else {
                $stats['images_failed']++;
                $this->warn("  ✗ Failed: product #{$product->id}, image #{$image->id}");
            }
        }
    }

    /**
     * Get appropriate keywords for a brand based on its name.
     */
    protected function getKeywordsForBrand(Brand $brand): array
    {
        $name = strtolower($brand->name);

        // Match brand name to keyword category
        foreach ($this->brandKeywords as $category => $keywords) {
            if (Str::contains($name, $category)) {
                return $keywords;
            }
        }

        // Default to general handbag keywords
        return ['handbag', 'purse', 'bag', 'leather bag', 'fashion bag'];
    }

    /**
     * Get a unique handbag image URL.
     */
    protected function getHandbagImageUrl(array $keywords, int $productId, int $imageIndex): ?string
    {
        $keyword = $keywords[array_rand($keywords)];

        // Try multiple sources with fallback
        $sources = [
            // Unsplash source API
            fn($k, $sig) => "https://source.unsplash.com/800x800/?{$k},handbag,fashion&sig={$sig}",
            // LoremFlickr
            fn($k, $sig) => "https://loremflickr.com/800/800/handbag,{$k}?lock={$sig}",
            // Picsum as last resort
            fn($k, $sig) => "https://picsum.photos/800/800?random={$sig}",
        ];

        foreach ($sources as $source) {
            $signature = md5($productId . $imageIndex . microtime(true) . rand(1000, 9999));
            $url = $source($keyword, $signature);

            if (!in_array($url, $this->downloadedUrls)) {
                $this->downloadedUrls[] = $url;
                return $url;
            }
        }

        return null;
    }

    /**
     * Check if image is already a real handbag image.
     */
    protected function isRealHandbagImage(string $imagePath): bool
    {
        if (!Storage::disk('public')->exists($imagePath)) {
            return false;
        }

        $size = Storage::disk('public')->size($imagePath);
        return $size > 10000; // Real images are larger than 10KB
    }

    /**
     * Download and replace an image.
     */
    protected function downloadAndReplaceImage(ProductImage $image, string $imageUrl): array
    {
        try {
            $response = Http::timeout(30)->get($imageUrl);

            if (!$response->successful()) {
                return ['success' => false, 'bytes' => 0];
            }

            $imageContent = $response->body();

            // Verify it's a valid image
            if (!$this->isValidImage($imageContent)) {
                return ['success' => false, 'bytes' => 0];
            }

            // Get file extension
            $extension = $this->getImageExtension($response->header('Content-Type'));

            // Create new filename with handbag identifier
            $uuid = Str::uuid();
            $newPath = 'products/' . $uuid . '.' . $extension;

            // Delete old file
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }

            // Save new image
            Storage::disk('public')->put($newPath, $imageContent);

            // Update database
            $image->image_path = $newPath;
            $image->save();

            return ['success' => true, 'bytes' => strlen($imageContent)];

        } catch (\Exception $e) {
            return ['success' => false, 'bytes' => 0];
        }
    }

    /**
     * Check if content is a valid image.
     */
    protected function isValidImage(string $content): bool
    {
        $signatures = [
            "\xFF\xD8\xFF", // JPEG
            "\x89\x50\x4E\x47", // PNG
            "GIF", // GIF
            "RIFF", // WEBP
        ];

        foreach ($signatures as $sig) {
            if (str_starts_with($content, $sig)) {
                return true;
            }
        }

        return strlen($content) > 10000;
    }

    /**
     * Get image extension from content type.
     */
    protected function getImageExtension(?string $contentType): string
    {
        return match (true) {
            str_contains($contentType ?? '', 'jpeg') => 'jpg',
            str_contains($contentType ?? '', 'png') => 'png',
            str_contains($contentType ?? '', 'gif') => 'gif',
            str_contains($contentType ?? '', 'webp') => 'webp',
            default => 'jpg',
        };
    }
}
