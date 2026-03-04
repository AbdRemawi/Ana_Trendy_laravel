<?php

namespace App\Console\Commands;

use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class InspectProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:inspect-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inspect all product images in the database and show their status';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Inspecting product images...');
        $this->newLine();

        $totalImages = ProductImage::count();
        $this->info("Total images in database: {$totalImages}");
        $this->newLine();

        // Get all images
        $images = ProductImage::all();

        $problematicImages = [];
        $validImages = [];

        foreach ($images as $image) {
            $path = $image->image_path;
            $issues = [];

            // Check for various problems
            if (empty($path)) {
                $issues[] = 'Empty path';
            } elseif ($path === 'undefined' || str_contains($path, 'undefined')) {
                $issues[] = 'Contains "undefined"';
            }

            // Check if file exists in storage
            if (!empty($path) && !Storage::disk('public')->exists($path)) {
                $issues[] = 'File not found in storage';
            }

            if (empty($issues)) {
                $validImages[] = $image;
            } else {
                $problematicImages[] = [
                    'id' => $image->id,
                    'product_id' => $image->product_id,
                    'image_path' => $path,
                    'issues' => implode(', ', $issues),
                ];
            }
        }

        $this->info("✅ Valid images: " . count($validImages));
        $this->warn("⚠️  Problematic images: " . count($problematicImages));
        $this->newLine();

        if (!empty($problematicImages)) {
            $this->error('Problematic images found:');
            $this->table(
                ['ID', 'Product ID', 'Image Path', 'Issues'],
                $problematicImages
            );
            $this->newLine();
        }

        // Show sample of valid images
        if (count($validImages) > 0) {
            $this->info('Sample of valid images:');
            $samples = array_slice($validImages, 0, 5);
            $this->table(
                ['ID', 'Product ID', 'Image Path', 'Is Primary'],
                collect($samples)->map(fn($img) => [
                    $img->id,
                    $img->product_id,
                    $img->image_path,
                    $img->is_primary ? 'Yes' : 'No',
                ])
            );
        }

        // Check storage files
        $this->newLine();
        $storageFiles = collect(Storage::disk('public')->files('products'));
        $this->info("Files in storage/app/public/products: {$storageFiles->count()}");

        return Command::SUCCESS;
    }
}
