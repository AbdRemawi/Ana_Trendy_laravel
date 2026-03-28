<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Image Compressor Service
 *
 * Compresses images on upload to ensure maximum file size of 2MB
 * while maintaining acceptable quality.
 */
class ImageCompressorService
{
    private $manager;
    private int $maxFileSize;
    private int $maxDimension;
    private int $initialQuality;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
        $this->maxFileSize = 2 * 1024 * 1024; // 2MB in bytes
        $this->maxDimension = 2500; // Maximum width or height
        $this->initialQuality = 85; // Starting quality (0-100)
    }

    /**
     * Compress and store an uploaded image.
     *
     * @param UploadedFile $image
     * @param string $directory Storage directory (e.g., 'products', 'categories')
     * @param string $disk Storage disk (default: 'public')
     * @return string The path where the image was stored
     */
    public function compressAndStore(UploadedFile $image, string $directory, string $disk = 'public'): string
    {
        // Load the image using decode method
        $img = $this->manager->decode($image->getRealPath());

        // Get original dimensions
        $width = $img->width();
        $height = $img->height();

        // Resize if dimensions are too large (maintain aspect ratio)
        if ($width > $this->maxDimension || $height > $this->maxDimension) {
            if ($width > $height) {
                // Landscape
                $img = $img->scale($this->maxDimension, null);
            } else {
                // Portrait or square
                $img = $img->scale(null, $this->maxDimension);
            }
        }

        // Generate unique filename
        $extension = $this->getOptimalExtension($image->getClientOriginalExtension());
        $filename = Str::uuid() . '.' . $extension;
        $tempPath = sys_get_temp_dir() . '/' . $filename;

        // Try to compress with decreasing quality until size is acceptable
        $quality = $this->initialQuality;
        $minQuality = 50; // Don't go below this quality
        $iteration = 0;
        $maxIterations = 10;

        do {
            // Encode with current quality using v4 API
            if ($extension === 'png') {
                // For PNG, save directly (no quality parameter for PNG in v4)
                $encoded = $img->encode(new PngEncoder());
            } elseif ($extension === 'webp') {
                // For WebP with quality
                $encoded = $img->encode(new WebpEncoder($quality));
            } else {
                // For JPEG with quality
                $encoded = $img->encode(new JpegEncoder($quality));
            }

            // Save to temp file
            file_put_contents($tempPath, $encoded->toString());
            $fileSize = filesize($tempPath);

            // If size is acceptable or we've reached minimum quality, stop
            if ($fileSize <= $this->maxFileSize || $quality <= $minQuality || $iteration >= $maxIterations) {
                break;
            }

            // Reduce quality for next iteration
            $quality -= 5;
            $iteration++;

        } while ($quality >= $minQuality && $iteration < $maxIterations);

        // Store the compressed image
        $path = $directory . '/' . $filename;
        Storage::disk($disk)->put($path, file_get_contents($tempPath));

        // Clean up temp file
        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }

        return $path;
    }

    /**
     * Get the optimal file extension for compression.
     *
     * @param string $originalExtension
     * @return string
     */
    private function getOptimalExtension(string $originalExtension): string
    {
        $originalExtension = strtolower($originalExtension);

        // Always use JPEG for better compression (unless original is WebP)
        // PNGs with transparency will be converted to JPEG with white background
        if (in_array($originalExtension, ['jpg', 'jpeg', 'png'])) {
            return 'jpg';
        }

        return $originalExtension === 'webp' ? 'webp' : 'jpg';
    }

    /**
     * Compress multiple images at once.
     *
     * @param array $images Array of UploadedFile objects
     * @param string $directory Storage directory
     * @param string $disk Storage disk
     * @return array Array of storage paths
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
