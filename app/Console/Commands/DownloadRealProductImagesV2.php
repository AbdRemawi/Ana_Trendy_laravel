<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadRealProductImagesV2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:download-images-v2
                            {--count=4 : Number of images per product to download}
                            {--force : Force re-download even if image exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download real product images from multiple sources (Picsum, LoremFlickr, etc.)';

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

        $this->info("Found {$products->count()} products to process.");
        $this->newLine();

        $stats = [
            'products_processed' => 0,
            'images_downloaded' => 0,
            'images_failed' => 0,
            'images_skipped' => 0,
        ];

        $this->withProgressBar($products, function ($product) use ($force, &$stats) {
            $stats['products_processed']++;

            $images = $product->images;

            foreach ($images as $index => $image) {
                // Skip if already a real image and not forcing
                if (!$force && $this->isRealImage($image->image_path)) {
                    $stats['images_skipped']++;
                    continue;
                }

                // Get image URL from multiple sources with fallback
                $imageUrl = $this->getImageUrlWithFallback($index);

                if ($imageUrl) {
                    $success = $this->downloadAndReplaceImage($image, $imageUrl);

                    if ($success) {
                        $stats['images_downloaded']++;
                    } else {
                        $stats['images_failed']++;
                    }
                } else {
                    $stats['images_failed']++;
                }
            }
        });

        $this->newLine();
        $this->newLine();

        $this->info('✅ Download completed!');
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Products processed', $stats['products_processed']],
                ['Images downloaded', $stats['images_downloaded']],
                ['Images skipped', $stats['images_skipped']],
                ['Images failed', $stats['images_failed']],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Get image URL with multiple fallback sources.
     */
    protected function getImageUrlWithFallback(int $index): ?string
    {
        $sources = [
            // Source 1: Picsum Photos (reliable, random photos)
            fn($i) => "https://picsum.photos/800/800?random=" . md5(time() . $i . rand(1000, 9999)),

            // Source 2: LoremFlickr (category-based images)
            fn($i) => "https://loremflickr.com/800/800/fashion, clothing?random=" . rand(1000, 9999),

            // Source 3: PlaceKitten (cute images as fallback)
            fn($i) => "https://placekitten.com/800/800",

            // Source 4: Via Placeholder (simple fallback)
            fn($i) => "https://via.placeholder.com/800x800/4CAF50/FFFFFF?text=Product+" . ($i + 1),
        ];

        // Try each source until we get a unique URL
        foreach ($sources as $source) {
            $url = $source($index);

            if (!in_array($url, $this->downloadedUrls)) {
                $this->downloadedUrls[] = $url;
                return $url;
            }
        }

        return null;
    }

    /**
     * Check if an image is a real image (not SVG placeholder).
     */
    protected function isRealImage(string $imagePath): bool
    {
        if (!Storage::disk('public')->exists($imagePath)) {
            return false;
        }

        $size = Storage::disk('public')->size($imagePath);
        return $size > 5000; // Real images are larger than 5KB
    }

    /**
     * Download and replace an image.
     */
    protected function downloadAndReplaceImage(ProductImage $image, string $imageUrl): bool
    {
        try {
            $response = Http::timeout(30)->get($imageUrl);

            if (!$response->successful()) {
                return false;
            }

            $imageContent = $response->body();

            // Verify it's an image
            if (!$this->isValidImage($imageContent)) {
                return false;
            }

            // Get file extension
            $extension = $this->getImageExtension($response->header('Content-Type'));

            // Create new filename
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

            return true;

        } catch (\Exception $e) {
            return false;
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

        // Check minimum size
        return strlen($content) > 5000;
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
