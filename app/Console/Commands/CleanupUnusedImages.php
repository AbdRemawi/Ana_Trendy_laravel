<?php

namespace App\Console\Commands;

use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupUnusedImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:cleanup-unused-images
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--yes : Skip confirmation and delete automatically}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove unused image files from storage (old placeholders, orphaned files)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info('🧹 Cleaning up unused product images...');
        $this->newLine();

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No files will be deleted');
            $this->newLine();
        }

        // Get all image paths from database
        $dbImages = ProductImage::pluck('image_path')->toArray();

        // Get all files from storage
        $allFiles = Storage::disk('public')->files('products');

        $this->info("Files in storage: " . count($allFiles));
        $this->info("Files in database: " . count($dbImages));
        $this->newLine();

        // Find unused files
        $unusedFiles = array_filter($allFiles, function ($file) use ($dbImages) {
            return !in_array($file, $dbImages);
        });

        $unusedCount = count($unusedFiles);
        $this->warn("Unused files found: {$unusedCount}");
        $this->newLine();

        if ($unusedCount === 0) {
            $this->info('✅ No unused files to clean up.');
            return Command::SUCCESS;
        }

        // Calculate space used by unused files
        $totalSize = 0;
        foreach ($unusedFiles as $file) {
            $totalSize += Storage::disk('public')->size($file);
        }

        $this->info('Total space used by unused files: ' . $this->formatBytes($totalSize));
        $this->newLine();

        // Show sample of files to be deleted
        $samples = array_slice($unusedFiles, 0, 10);
        $this->info('Sample files to be deleted:');
        foreach ($samples as $sample) {
            $size = Storage::disk('public')->size($sample);
            $this->line("  - {$sample} (" . $this->formatBytes($size) . ")");
        }

        if ($unusedCount > 10) {
            $this->line("  ... and " . ($unusedCount - 10) . " more.");
        }

        $this->newLine();

        if (!$dryRun) {
            $shouldDelete = $this->option('yes') || $this->confirm("Do you want to delete these {$unusedCount} unused files?");

            if ($shouldDelete) {
                foreach ($unusedFiles as $file) {
                    Storage::disk('public')->delete($file);
                }
                $this->info("✅ Successfully deleted {$unusedCount} unused files.");
            } else {
                $this->info('❌ Cleanup cancelled.');
            }
        } else {
            $this->info('💡 Run without --dry-run to actually delete these files.');
        }

        return Command::SUCCESS;
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
