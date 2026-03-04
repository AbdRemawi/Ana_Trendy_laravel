<?php

namespace App\Console\Commands;

use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CheckImageStorageMatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:check-image-storage
                            {--fix : Automatically fix missing images}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if product image files exist in storage and optionally fix missing ones';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Checking product image files in storage...');
        $this->newLine();

        $shouldFix = $this->option('fix');

        // Get all product images from database
        $dbImages = ProductImage::all();
        $totalDbImages = $dbImages->count();

        $this->info("Database has {$totalDbImages} product image records.");
        $this->newLine();

        // Get all files from storage
        $storageFiles = collect(Storage::disk('public')->files('products'));
        $totalStorageFiles = $storageFiles->count();

        $this->info("Storage has {$totalStorageFiles} files.");
        $this->newLine();

        // Check which database records have missing files
        $missingFiles = [];
        $existingFiles = [];

        foreach ($dbImages as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                $existingFiles[] = $image->id;
            } else {
                $missingFiles[] = [
                    'id' => $image->id,
                    'product_id' => $image->product_id,
                    'image_path' => $image->image_path,
                ];
            }
        }

        $this->info("✅ Files that exist: " . count($existingFiles));
        $this->warn("⚠️  Files missing from storage: " . count($missingFiles));
        $this->newLine();

        if (!empty($missingFiles)) {
            $this->error('The following database records reference files that do not exist:');
            $this->table(
                ['ID', 'Product ID', 'Missing Path'],
                array_slice($missingFiles, 0, 10)
            );

            if (count($missingFiles) > 10) {
                $this->info("... and " . (count($missingFiles) - 10) . " more.");
            }

            $this->newLine();

            if ($shouldFix) {
                $this->info('🔧 Fixing missing images...');
                $this->newLine();

                // Get available files that aren't used
                $usedPaths = $dbImages->pluck('image_path')->toArray();
                $availableFiles = $storageFiles->filter(fn($file) => !in_array($file, $usedPaths));

                $this->info("Available unused files: {$availableFiles->count()}");
                $this->newLine();

                if ($availableFiles->isEmpty()) {
                    $this->error('❌ No available files to assign. All storage files are already in use.');
                    return Command::FAILURE;
                }

                $fixedCount = 0;
                $couldNotFix = 0;

                foreach ($missingFiles as $missing) {
                    if ($availableFiles->isNotEmpty()) {
                        // Get the next available file
                        $newPath = $availableFiles->pop();

                        // Update the database record
                        $image = ProductImage::find($missing['id']);
                        $image->image_path = $newPath;
                        $image->save();

                        $fixedCount++;
                        $this->line("✓ Fixed image ID {$missing['id']} (Product {$missing['product_id']}) -> {$newPath}");
                    } else {
                        $couldNotFix++;
                    }
                }

                $this->newLine();
                $this->info("✅ Fixed {$fixedCount} images.");

                if ($couldNotFix > 0) {
                    $this->warn("⚠️  Could not fix {$couldNotFix} images (not enough available files).");
                }
            } else {
                $this->warn('💡 Run with --fix option to automatically fix missing images.');
                $this->newLine();
                $this->info('Example: php artisan products:check-image-storage --fix');
            }
        } else {
            $this->info('✅ All database image files exist in storage!');
        }

        return Command::SUCCESS;
    }
}
