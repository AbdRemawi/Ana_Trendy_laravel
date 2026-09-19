<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ProductImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Safely remove ProductImage rows whose underlying file is missing from storage.
 *
 * Unlike products:fix-images / products:sync-images / products:check-image-storage --fix
 * (which assign RANDOM storage files to products and therefore show the wrong photo),
 * this command never invents data. It only deletes rows that point at a file that does
 * not exist, then re-ensures each affected product still has a valid primary image.
 *
 * This is the correct remedy for "only the primary image loads, the rest are broken":
 * the broken cards are DB rows referencing deleted files. Remove them, then re-upload
 * the real images from the product edit screen.
 */
class PruneMissingProductImages extends Command
{
    protected $signature = 'products:prune-missing-images
                            {--product= : Restrict to a single product id}
                            {--dry-run : Show what would be deleted without deleting}
                            {--yes : Skip the confirmation prompt}';

    protected $description = 'Delete product_images rows whose file is missing from storage (safe: never assigns random files)';

    public function handle(ProductImageService $imageService): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $productId = $this->option('product');

        $this->info('🔍 Scanning product images for missing files...');
        if ($dryRun) {
            $this->warn('⚠️  DRY RUN — nothing will be deleted');
        }
        $this->newLine();

        $disk = Storage::disk('public');

        $query = ProductImage::query();
        if ($productId !== null) {
            $query->where('product_id', (int) $productId);
        }

        $missing = $query->get()->filter(function (ProductImage $image) use ($disk) {
            $path = (string) $image->image_path;

            return $path === ''
                || str_contains($path, 'undefined')
                || ! $disk->exists($path);
        });

        if ($missing->isEmpty()) {
            $this->info('✅ No orphaned image rows found. Every image row has a real file.');

            return self::SUCCESS;
        }

        $this->warn("Found {$missing->count()} image row(s) pointing at a missing file:");
        $this->table(
            ['ID', 'Product', 'Primary', 'Missing path'],
            $missing->take(20)->map(fn (ProductImage $i) => [
                $i->id,
                $i->product_id,
                $i->is_primary ? 'yes' : '',
                $i->image_path === '' ? '(empty)' : $i->image_path,
            ])->all()
        );
        if ($missing->count() > 20) {
            $this->line('  ... and '.($missing->count() - 20).' more.');
        }
        $this->newLine();

        if ($dryRun) {
            $this->info('💡 Re-run without --dry-run to delete these rows.');

            return self::SUCCESS;
        }

        if (! $this->option('yes') && ! $this->confirm("Delete these {$missing->count()} orphaned row(s)?")) {
            $this->info('❌ Cancelled. Nothing changed.');

            return self::SUCCESS;
        }

        $affectedProductIds = $missing->pluck('product_id')->unique();

        DB::transaction(function () use ($missing) {
            ProductImage::whereIn('id', $missing->pluck('id'))->delete();
        });

        // Re-ensure a valid primary exists for every product we touched.
        $reassigned = 0;
        foreach ($affectedProductIds as $pid) {
            $product = Product::find($pid);
            if ($product) {
                $before = $product->images()->where('is_primary', true)->exists();
                $imageService->ensurePrimaryImageExists($product);
                if (! $before && $product->images()->where('is_primary', true)->exists()) {
                    $reassigned++;
                }
            }
        }

        $this->newLine();
        $this->info("✅ Deleted {$missing->count()} orphaned image row(s) across {$affectedProductIds->count()} product(s).");
        if ($reassigned > 0) {
            $this->info("✅ Re-assigned a primary image for {$reassigned} product(s) that lost theirs.");
        }
        $this->newLine();
        $this->line('Next: open the affected products and upload the real images ("تحميل صور جديدة").');

        return self::SUCCESS;
    }
}
