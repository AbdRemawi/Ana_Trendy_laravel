<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadRealBrandHandbags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:download-brand-handbags
                            {--brands= : Comma-separated brand IDs}
                            {--force : Force re-download}
                            {--test : Test mode only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download REAL handbag images specific to each luxury brand';

    /**
     * Brand-specific search keywords for handbags.
     */
    protected array $brandKeywords = [
        'Coach' => ['coach+handbag', 'coach+bag', 'coach+purse', 'coach+tote'],
        'Gucci' => ['gucci+handbag', 'gucci+bag', 'gucci+purse', 'gucci+tote'],
        'Hermès' => ['hermes+handbag', 'hermes+bag', 'hermes+birkin', 'hermes+kelly'],
        'Hermes' => ['hermes+handbag', 'hermes+bag', 'hermes+birkin', 'hermes+kelly'],
        'Fendi' => ['fendi+handbag', 'fendi+bag', 'fendi+purse', 'fendi+tote'],
        'Tory Burch' => ['tory+burch+handbag', 'tory+burch+bag', 'tory+burch+purse'],
        'Yves Saint Laurent' => ['ysl+handbag', 'yves+saint+laurent+bag', 'ysl+purse'],
    ];

    /**
     * Image sources that provide fashion/handbag content.
     */
    protected array $imageSources = [
        'loremflickr', // Best for specific keywords
        'placeholder', // Fallback
    ];

    /**
     * Track used signatures to avoid duplicates.
     */
    protected array $usedSignatures = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $testMode = $this->option('test');
        $force = $this->option('force');

        $this->info('👜 Downloading REAL Brand Handbag Images...');
        $this->newLine();

        if ($testMode) {
            $this->warn('⚠️  TEST MODE - No actual changes');
            $this->newLine();
        }

        // Get brands
        $query = Brand::active();
        if ($this->option('brands')) {
            $query->whereIn('id', explode(',', $this->option('brands')));
        }
        $brands = $query->get();

        $this->info("Processing {$brands->count()} luxury handbag brands:");
        $this->newLine();

        $stats = [
            'brands' => 0,
            'products' => 0,
            'downloaded' => 0,
            'failed' => 0,
            'bytes' => 0,
        ];

        foreach ($brands as $brand) {
            $this->info("🏷️  {$brand->name}");
            $this->processBrand($brand, $force, $testMode, $stats);
            $stats['brands']++;
            $this->newLine();
        }

        $totalMb = round($stats['bytes'] / 1024 / 1024, 2);
        $this->info('✅ Complete!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Brands processed', $stats['brands']],
                ['Products processed', $stats['products']],
                ['Images downloaded', $stats['downloaded']],
                ['Images failed', $stats['failed']],
                ['Total data', "{$totalMb} MB"],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Process all products for a brand.
     */
    protected function processBrand(Brand $brand, bool $force, bool $testMode, array &$stats): void
    {
        $products = $brand->products()->active()->with('images')->get();

        if ($products->isEmpty()) {
            $this->warn("  No products found");
            return;
        }

        $this->line("  Products: {$products->count()}");

        // Get brand-specific keywords
        $keywords = $this->getBrandKeywords($brand->name);
        $this->line("  Keywords: " . implode(', ', $keywords));
        $this->newLine();

        foreach ($products as $product) {
            $stats['products']++;
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
            // Skip if already good handbag image
            if (!$force && $this->isHandbagImage($image->image_path)) {
                continue;
            }

            // Get unique handbag image URL
            $keyword = $keywords[$index % count($keywords)];
            $url = $this->getHandbagImageUrl($keyword, $product->id, $image->id);

            if ($testMode) {
                $this->line("  TEST: Product #{$product->id} - {$keyword}");
                $stats['downloaded']++;
                continue;
            }

            // Download image
            $result = $this->downloadImage($image, $url);

            if ($result['success']) {
                $stats['downloaded']++;
                $stats['bytes'] += $result['bytes'];
                $this->line("  ✓ Product #{$product->id} Image #{$image->id} - {$keyword}");
            } else {
                $stats['failed']++;
                $this->warn("  ✗ Failed: Product #{$product->id}");
            }
        }
    }

    /**
     * Get brand-specific keywords.
     */
    protected function getBrandKeywords(string $brandName): array
    {
        foreach ($this->brandKeywords as $brand => $keywords) {
            if (stripos($brandName, $brand) !== false || stripos($brand, $brandName) !== false) {
                return $keywords;
            }
        }

        // Default handbag keywords
        return ['luxury+handbag', 'leather+bag', 'designer+purse', 'fashion+handbag'];
    }

    /**
     * Get a unique handbag image URL using LoremFlickr (supports keywords).
     */
    protected function getHandbagImageUrl(string $keyword, int $productId, int $imageId): string
    {
        // Generate unique signature
        do {
            $signature = time() . rand(1000, 9999);
        } while (in_array($signature, $this->usedSignatures));

        $this->usedSignatures[] = $signature;

        // Use LoremFlickr with lock parameter to get unique images
        // Format: https://loremflickr.com/800/800/keyword1,keyword2?lock=12345
        return "https://loremflickr.com/800/800/{$keyword}?lock={$signature}";
    }

    /**
     * Check if image is already a handbag (heuristic check).
     */
    protected function isHandbagImage(string $path): bool
    {
        if (!Storage::disk('public')->exists($path)) {
            return false;
        }

        // Real handbag images are typically larger than 50KB
        return Storage::disk('public')->size($path) > 50000;
    }

    /**
     * Download and replace an image with retries.
     */
    protected function downloadImage(ProductImage $image, string $url): array
    {
        $maxRetries = 3;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::timeout(30)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'])
                    ->get($url);

                if (!$response->successful()) {
                    if ($attempt < $maxRetries) {
                        sleep(1);
                        continue;
                    }
                    return $this->tryFallback($image);
                }

                $content = $response->body();

                // Validate image
                if (!$this->isValidImage($content)) {
                    if ($attempt < $maxRetries) {
                        sleep(1);
                        continue;
                    }
                    return $this->tryFallback($image);
                }

                // Save image
                $ext = $this->getExtension($response->header('Content-Type'));
                $newPath = 'products/' . Str::uuid() . '.' . $ext;

                // Delete old file
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }

                // Save new file
                Storage::disk('public')->put($newPath, $content);

                // Update database
                $image->image_path = $newPath;
                $image->save();

                return ['success' => true, 'bytes' => strlen($content)];

            } catch (\Exception $e) {
                if ($attempt == $maxRetries) {
                    return $this->tryFallback($image);
                }
                sleep(1);
            }
        }

        return ['success' => false, 'bytes' => 0];
    }

    /**
     * Try fallback image sources.
     */
    protected function tryFallback(ProductImage $image): array
    {
        $fallbackKeywords = ['handbag', 'purse', 'bag', 'leather+bag'];

        foreach ($fallbackKeywords as $keyword) {
            try {
                $url = "https://loremflickr.com/800/800/{$keyword}?lock=" . time() . rand(1000, 9999);
                $response = Http::timeout(15)->get($url);

                if ($response->successful() && $this->isValidImage($response->body())) {
                    $content = $response->body();
                    $ext = 'jpg';
                    $newPath = 'products/' . Str::uuid() . '.' . $ext;

                    Storage::disk('public')->delete($image->image_path);
                    Storage::disk('public')->put($newPath, $content);
                    $image->image_path = $newPath;
                    $image->save();

                    return ['success' => true, 'bytes' => strlen($content)];
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return ['success' => false, 'bytes' => 0];
    }

    /**
     * Validate image content.
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
                // Valid image and reasonable size (>20KB for quality handbag image)
                return strlen($content) > 20000;
            }
        }

        return false;
    }

    /**
     * Get file extension from content type.
     */
    protected function getExtension(?string $contentType): string
    {
        return match (true) {
            str_contains($contentType ?? '', 'png') => 'png',
            str_contains($contentType ?? '', 'gif') => 'gif',
            str_contains($contentType ?? '', 'webp') => 'webp',
            default => 'jpg',
        };
    }
}
