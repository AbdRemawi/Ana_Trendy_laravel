<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadRealProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:download-real-images
                            {--count=5 : Number of images per product to download}
                            {--force : Force re-download even if image exists}
                            {--source=unsplash : Image source (unsplash, pexels)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download real product images from Unsplash and replace placeholder images';

    /**
     * Search keywords for different product types.
     */
    protected array $searchKeywords = [
        'fashion' => ['fashion', 'clothing', 'apparel', 'outfit', 'style', 'wear'],
        'casual' => ['t-shirt', 'shirt', 'casual', 'jeans', 'hoodie'],
        'formal' => ['suit', 'formal', 'dress', 'elegant', 'business'],
        'sports' => ['sportswear', 'athletic', 'fitness', 'gym', 'active'],
        'accessories' => ['bag', 'shoes', 'watch', 'accessories', 'jewelry'],
    ];

    /**
     * Downloaded image URLs to avoid duplicates.
     */
    protected array $downloadedUrls = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $imagesPerProduct = (int) $this->option('count');
        $force = $this->option('force');

        $this->info('🖼️  Downloading real product images...');
        $this->newLine();

        // Get all products
        $products = Product::with('images')->active()->get();
        $totalProducts = $products->count();

        $this->info("Found {$totalProducts} products to process.");
        $this->newLine();

        $stats = [
            'products_processed' => 0,
            'images_downloaded' => 0,
            'images_failed' => 0,
            'total_bytes' => 0,
        ];

        $this->withProgressBar($products, function ($product) use ($imagesPerProduct, $force, &$stats) {
            $stats['products_processed']++;

            // Get category for relevant search keywords
            $category = $product->category?->name ?? 'fashion';
            $keywords = $this->getKeywordsForCategory($category);

            // Get existing images for this product
            $images = $product->images;

            foreach ($images as $index => $image) {
                // Skip if already downloaded and not forcing
                if (!$force && $this->isRealImage($image->image_path)) {
                    continue;
                }

                // Download a real image
                $imageUrl = $this->getRandomImageUrl($keywords, $index);

                if ($imageUrl) {
                    $success = $this->downloadAndReplaceImage($image, $imageUrl);

                    if ($success) {
                        $stats['images_downloaded']++;
                    } else {
                        $stats['images_failed']++;
                    }
                }
            }
        });

        $this->newLine();
        $this->newLine();

        $this->info('✅ Download completed!');
        $this->newLine();

        $totalMb = round($stats['total_bytes'] / 1024 / 1024, 2);
        $this->table(
            ['Metric', 'Count'],
            [
                ['Products processed', $stats['products_processed']],
                ['Images downloaded', $stats['images_downloaded']],
                ['Images failed', $stats['images_failed']],
                ['Total data downloaded', $totalMb . ' MB'],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Get relevant keywords for a category.
     */
    protected function getKeywordsForCategory(string $category): array
    {
        $categoryLower = strtolower($category);

        foreach ($this->searchKeywords as $key => $keywords) {
            if (Str::contains($categoryLower, $key)) {
                return $keywords;
            }
        }

        return $this->searchKeywords['fashion'];
    }

    /**
     * Get a random image URL from Unsplash.
     */
    protected function getRandomImageUrl(array $keywords, int $index): ?string
    {
        $keyword = $keywords[array_rand($keywords)];

        // Try multiple times to get a unique image
        for ($i = 0; $i < 5; $i++) {
            // Unsplash source API with randomization
            $imageUrl = "https://source.unsplash.com/800x800/?{$keyword}&sig=" . md5(time() . $index . $i . rand());

            // Check if we've already used this URL
            if (!in_array($imageUrl, $this->downloadedUrls)) {
                $this->downloadedUrls[] = $imageUrl;
                return $imageUrl;
            }
        }

        return null;
    }

    /**
     * Check if an image is a real image (not SVG placeholder).
     */
    protected function isRealImage(string $imagePath): bool
    {
        // Check if file exists
        if (!Storage::disk('public')->exists($imagePath)) {
            return false;
        }

        // Check file size (real images are larger than SVG placeholders)
        $size = Storage::disk('public')->size($imagePath);

        // SVG placeholders are usually small (< 1KB)
        return $size > 2000;
    }

    /**
     * Download and replace an image.
     */
    protected function downloadAndReplaceImage(ProductImage $image, string $imageUrl): bool
    {
        try {
            $this->line("Downloading for image ID {$image->id}...");

            // Download the image
            $response = Http::timeout(30)->get($imageUrl);

            if (!$response->successful()) {
                $this->warn("Failed to download: HTTP {$response->status()}");
                return false;
            }

            $imageContent = $response->body();

            // Verify it's actually an image
            if (!$this->isValidImage($imageContent)) {
                $this->warn("Downloaded content is not a valid image");
                return false;
            }

            // Get the file extension from content type
            $extension = $this->getImageExtension($response->header('Content-Type'));

            // Create new filename (keep same UUID but with real extension)
            $uuid = Str::before(pathinfo($image->image_path, PATHINFO_FILENAME), '.');
            $newPath = 'products/' . $uuid . '.' . $extension;

            // Delete old file
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }

            // Save new image
            Storage::disk('public')->put($newPath, $imageContent);

            // Update database record
            $image->image_path = $newPath;
            $image->save();

            $this->line("✓ Downloaded: {$newPath} (" . $this->formatBytes(strlen($imageContent)) . ")");

            return true;

        } catch (\Exception $e) {
            $this->error("Error downloading image: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Check if content is a valid image.
     */
    protected function isValidImage(string $content): bool
    {
        // Check for common image signatures (magic numbers)
        $signatures = [
            'jpg' => "\xFF\xD8\xFF",
            'png' => "\x89\x50\x4E\x47",
            'gif' => "GIF",
            'webp' => "RIFF",
        ];

        foreach ($signatures as $signature) {
            if (str_starts_with($content, $signature)) {
                return true;
            }
        }

        return false;
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

    /**
     * Format bytes to human readable.
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return round($bytes / 1048576, 2) . ' MB';
        }
    }
}
