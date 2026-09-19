<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Download every public image file that exists on production into local storage.
 *
 * Your local database already has the rows (products, brands, categories), but the
 * uploaded files under storage/app/public were never copied to your machine. Production
 * serves those files, so this pulls each one down over HTTPS to make local match
 * production. Files that are also missing on production (404) are reported and skipped.
 *
 * For each file it tries the raw /storage path first (true original bytes) and falls
 * back to /img (PHP-served) which works even when the production /storage symlink 404s.
 */
class PullImagesFromProduction extends Command
{
    protected $signature = 'products:pull-images
                            {--base=https://backend.anatrendy.com : Production base URL}
                            {--product= : Restrict to a single product id (products only)}
                            {--only= : Restrict to one type: products|brands|categories}
                            {--overwrite : Re-download even if the local file already exists}';

    protected $description = 'Download all image files (products, brands, categories) from production into local storage/app/public';

    public function handle(): int
    {
        $base = rtrim((string) $this->option('base'), '/');
        $only = $this->option('only');
        $overwrite = (bool) $this->option('overwrite');

        $this->info("⬇️  Pulling images from {$base} ...");
        $this->newLine();

        $items = $this->collectItems($only);

        if ($items->isEmpty()) {
            $this->warn('Nothing to pull for the given filters.');

            return self::SUCCESS;
        }

        $disk = Storage::disk('public');
        $downloaded = 0;
        $skippedExisting = 0;
        $missingOnProd = 0;
        $failed = 0;
        $missingList = [];

        $bar = $this->output->createProgressBar($items->count());
        $bar->start();

        foreach ($items as $item) {
            $path = (string) $item['path'];
            $label = $item['label'];

            if ($path === '' || str_contains($path, 'undefined')) {
                $missingOnProd++;
                $missingList[] = "{$label}: invalid path '{$path}'";
                $bar->advance();
                continue;
            }

            if (! $overwrite && $disk->exists($path)) {
                $skippedExisting++;
                $bar->advance();
                continue;
            }

            $rel = ltrim($path, '/');

            try {
                // Local dev utility: WAMP PHP often ships without a CA bundle, so skip
                // TLS verification here. This command only runs locally, never in prod.
                // 1) Try the raw file via /storage (true original bytes).
                $response = Http::withoutVerifying()->timeout(30)->get($base.'/storage/'.$rel);

                // 2) Fall back to /img (PHP-served) which works even when the production
                //    /storage symlink 404s the file. Returns a near-original re-encode.
                if ($response->status() === 404) {
                    $response = Http::withoutVerifying()->timeout(30)
                        ->get($base.'/img/'.$rel, ['w' => 1600, 'q' => 90, 'fmt' => 'jpg']);
                }
            } catch (\Throwable $e) {
                $failed++;
                $missingList[] = "{$label}: error {$e->getMessage()}";
                $bar->advance();
                continue;
            }

            if ($response->successful() && strlen($response->body()) > 0) {
                $disk->put($path, $response->body());
                $downloaded++;
            } elseif ($response->status() === 404) {
                $missingOnProd++;
                $missingList[] = "{$label}: 404 {$path}";
            } else {
                $failed++;
                $missingList[] = "{$label}: HTTP {$response->status()} {$path}";
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(['Result', 'Count'], [
            ['✅ Downloaded', $downloaded],
            ['↩️  Already present (skipped)', $skippedExisting],
            ['⚠️  Missing on production (404)', $missingOnProd],
            ['❌ Failed', $failed],
        ]);

        if (! empty($missingList)) {
            $this->newLine();
            $this->warn('Records with no file on production (need re-upload or pruning):');
            foreach (array_slice($missingList, 0, 30) as $line) {
                $this->line('  - '.$line);
            }
            if (count($missingList) > 30) {
                $this->line('  ... and '.(count($missingList) - 30).' more.');
            }
            $this->newLine();
            $this->line('Clean orphaned product rows with: php artisan products:prune-missing-images');
        }

        return self::SUCCESS;
    }

    /**
     * Build the unified list of files to pull: [['path' => ..., 'label' => ...], ...].
     */
    private function collectItems(?string $only): \Illuminate\Support\Collection
    {
        $productId = $this->option('product');
        $items = collect();

        if (($only === null || $only === 'products')) {
            $q = ProductImage::query()->orderBy('product_id')->orderBy('sort_order');
            if ($productId !== null) {
                $q->where('product_id', (int) $productId);
            }
            foreach ($q->get() as $img) {
                $items->push([
                    'path' => (string) $img->image_path,
                    'label' => "product {$img->product_id} image {$img->id}",
                ]);
            }
        }

        // Brands and categories are ignored when --product is used (products-only intent).
        if ($productId === null && ($only === null || $only === 'brands')) {
            foreach (Brand::whereNotNull('logo')->get() as $brand) {
                $items->push([
                    'path' => (string) $brand->logo,
                    'label' => "brand {$brand->id} logo",
                ]);
            }
        }

        if ($productId === null && ($only === null || $only === 'categories')) {
            foreach (Category::whereNotNull('image')->get() as $cat) {
                $items->push([
                    'path' => (string) $cat->image,
                    'label' => "category {$cat->id} image",
                ]);
            }
        }

        return $items;
    }
}
