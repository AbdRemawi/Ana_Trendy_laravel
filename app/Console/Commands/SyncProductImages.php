<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SyncProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:sync-images
                            {--min=2 : Minimum number of images per product}
                            {--max=4 : Maximum number of images per product}
                            {--force : Force update existing images}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync product images with storage files. Ensures all products have images and updates any undefined/null paths.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $minImages = (int) $this->option('min');
        $maxImages = (int) $this->option('max');
        $force = $this->option('force');

        $this->info('🔄 Syncing product images...');
        $this->newLine();

        // Get all available files from storage
        $allStorageFiles = collect(Storage::disk('public')->files('products'));
        $this->info("Found {$allStorageFiles->count()} files in storage.");
        $this->newLine();

        // Get products and their image counts
        $products = Product::with('images')->get();
        $totalProducts = $products->count();

        $this->info("Processing {$totalProducts} products...");
        $this->newLine();

        // Track used files
        $usedFiles = ProductImage::pluck('image_path')->toArray();
        $availableFiles = $allStorageFiles->filter(fn($file) => !in_array($file, $usedFiles));

        $this->info("Available unused files: {$availableFiles->count()}");
        $this->newLine();

        if ($availableFiles->isEmpty()) {
            $this->warn('⚠️  No available files to add. All storage files are already in use.');
            $this->newLine();
        }

        $stats = [
            'products_checked' => 0,
            'images_fixed' => 0,
            'images_added' => 0,
            'products_with_issues' => 0,
            'primary_images_set' => 0,
        ];

        $this->withProgressBar($products, function ($product) use ($minImages, $maxImages, $force, &$availableFiles, &$stats) {
            $stats['products_checked']++;

            $images = $product->images;
            $imageCount = $images->count();
            $hasIssues = false;

            // Check for images with undefined/null paths
            foreach ($images as $image) {
                if (empty($image->image_path) || $image->image_path === 'undefined' || str_contains($image->image_path, 'undefined')) {
                    if ($availableFiles->isNotEmpty()) {
                        $newPath = $availableFiles->pop();
                        $image->image_path = $newPath;
                        $image->save();
                        $stats['images_fixed']++;
                        $hasIssues = true;
                    }
                }
            }

            // Ensure minimum number of images
            if ($imageCount < $minImages) {
                $needed = $minImages - $imageCount;
                $primaryExists = $images->contains('is_primary', true);

                for ($i = 0; $i < $needed; $i++) {
                    if ($availableFiles->isNotEmpty()) {
                        $isPrimary = (!$primaryExists && $i === 0);
                        $newPath = $availableFiles->pop();

                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $newPath,
                            'is_primary' => $isPrimary,
                            'sort_order' => $imageCount + $i + 1,
                        ]);

                        $stats['images_added']++;
                        if ($isPrimary) {
                            $stats['primary_images_set']++;
                            $primaryExists = true;
                        }
                        $hasIssues = true;
                    }
                }
            }

            // Ensure at least one primary image exists
            $primaryImage = $images->firstWhere('is_primary', true);
            if (!$primaryImage && $images->isNotEmpty()) {
                $images->first()->update(['is_primary' => true]);
                $stats['primary_images_set']++;
                $hasIssues = true;
            }

            if ($hasIssues) {
                $stats['products_with_issues']++;
            }
        });

        $this->newLine();
        $this->newLine();

        $this->info('✅ Sync completed!');
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Products checked', $stats['products_checked']],
                ['Products with issues', $stats['products_with_issues']],
                ['Images fixed', $stats['images_fixed']],
                ['Images added', $stats['images_added']],
                ['Primary images set', $stats['primary_images_set']],
                ['Remaining available files', $availableFiles->count()],
            ]
        );

        $this->newLine();

        // Show sample of products
        $sampleProducts = Product::with('images')->take(5)->get();
        $this->info('Sample products:');
        $this->table(
            ['ID', 'Name', 'Image Count', 'Has Primary'],
            $sampleProducts->map(fn($p) => [
                $p->id,
                $p->name,
                $p->images->count(),
                $p->images->contains('is_primary', true) ? 'Yes' : 'No',
            ])
        );

        return Command::SUCCESS;
    }
}
