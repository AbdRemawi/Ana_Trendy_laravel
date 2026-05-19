<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

/**
 * Backwards-compatible facade over {@see ImageService}.
 *
 * Existing callers expect a synchronous compressAndStore() returning a storage path.
 * The actual resize/encode/.webp generation is now queued via ProcessImageJob,
 * so uploads return immediately while the worker does the heavy lifting.
 */
class ImageCompressorService
{
    public function __construct(private readonly ImageService $images)
    {
    }

    public function compressAndStore(UploadedFile $image, string $directory, string $disk = 'public'): string
    {
        return $this->images->storeAndQueue($image, $directory, $disk);
    }

    /**
     * @param  array<int, UploadedFile>  $images
     * @return array<int, string>
     */
    public function compressAndStoreMultiple(array $images, string $directory, string $disk = 'public'): array
    {
        $paths = [];
        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $paths[] = $this->compressAndStore($image, $directory, $disk);
            }
        }
        return $paths;
    }
}
