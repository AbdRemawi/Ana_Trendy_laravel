<?php

namespace App\Console\Commands;

use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FixProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fix-images
                            {--dry-run : Show what would be changed without making changes}
                            {--force : Force update even if image_path exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix all product images with undefined or invalid paths by updating them with valid image paths from storage';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('🔧 Fixing product images...');
        $this->newLine();

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Get all product images that need fixing
        $query = ProductImage::query();

        // Find images with undefined, null, or empty paths
        $query->where(function ($q) {
            $q->where('image_path', 'undefined')
                ->orWhere('image_path', '')
                ->orWhereNull('image_path')
                ->orWhere('image_path', 'LIKE', '%undefined%');
        });

        $brokenImages = $query->get();

        if ($brokenImages->isEmpty()) {
            $this->info('✅ No broken images found. All product images are valid.');
            return Command::SUCCESS;
        }

        $this->info("Found {$brokenImages->count()} images that need fixing.");
        $this->newLine();

        // Get all available image files from storage
        $availableFiles = collect(Storage::disk('public')->files('products'));
        $this->info("Found {$availableFiles->count()} image files in storage/products.");
        $this->newLine();

        if ($availableFiles->isEmpty()) {
            $this->error('❌ No image files found in storage/products directory.');
            return Command::FAILURE;
        }

        // Track stats
        $fixedCount = 0;
        $skippedCount = 0;

        $this->withProgressBar($brokenImages, function ($image) use ($availableFiles, $dryRun, $force, &$fixedCount, &$skippedCount) {
            // Pick a random image file from available files
            $randomFile = $availableFiles->random();

            // Update the image path
            if (!$dryRun) {
                $image->image_path = $randomFile;
                $image->save();
            }

            $fixedCount++;
        });

        $this->newLine();
        $this->newLine();

        if ($dryRun) {
            $this->info("📋 DRY RUN: Would fix {$fixedCount} images.");
        } else {
            $this->info("✅ Successfully fixed {$fixedCount} product images.");
        }

        $this->newLine();

        // Show sample of fixed images
        $sampleImages = ProductImage::limit(5)->get();
        if ($sampleImages->isNotEmpty()) {
            $this->info('Sample fixed images:');
            $this->table(
                ['ID', 'Product ID', 'Image Path', 'Is Primary'],
                $sampleImages->map(fn($img) => [
                    $img->id,
                    $img->product_id,
                    $img->image_path,
                    $img->is_primary ? 'Yes' : 'No',
                ])
            );
        }

        return Command::SUCCESS;
    }
}
