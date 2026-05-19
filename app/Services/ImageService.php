<?php

namespace App\Services;

use App\Jobs\ProcessImageJob;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

/**
 * Primary image-processing service.
 *
 * - Stores the upload immediately so the HTTP request returns fast.
 * - Queues the heavy resize / re-encode / .webp generation via {@see ProcessImageJob}.
 * - The synchronous {@see ImageService::process()} entry point is what the job calls.
 */
class ImageService
{
    public const MAX_WIDTH = 1600;
    public const QUALITY = 80;

    private ImageManager $manager;

    public function __construct(?ImageManager $manager = null)
    {
        $this->manager = $manager ?? new ImageManager(new Driver());
    }

    /**
     * Persist an uploaded file and queue async processing.
     * Returns the storage path (relative to the disk root).
     */
    public function storeAndQueue(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $extension = $this->normalizeExtension($file->getClientOriginalExtension());
        $filename = Str::uuid()->toString().'.'.$extension;
        $path = trim($directory, '/').'/'.$filename;

        Storage::disk($disk)->putFileAs(trim($directory, '/'), $file, $filename);

        ProcessImageJob::dispatch($path, $disk);

        return $path;
    }

    /**
     * Synchronously process an image already in storage:
     *   - resize to MAX_WIDTH (preserves aspect ratio)
     *   - re-encode JPEG/PNG at QUALITY
     *   - write a sibling .webp variant
     *
     * Idempotent: safe to re-run on an already-processed file.
     */
    public function process(string $path, string $disk = 'public'): void
    {
        $fs = Storage::disk($disk);

        if (! $fs->exists($path)) {
            return;
        }

        $img = $this->manager->read($fs->path($path));

        if ($img->width() > self::MAX_WIDTH) {
            $img = $img->scale(width: self::MAX_WIDTH);
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $encoded = match ($extension) {
            'png' => $img->encode(new PngEncoder()),
            'webp' => $img->encode(new WebpEncoder(quality: self::QUALITY)),
            default => $img->encode(new JpegEncoder(quality: self::QUALITY)),
        };

        $fs->put($path, (string) $encoded);

        if ($extension !== 'webp') {
            $webpPath = preg_replace('/\.[^.]+$/', '.webp', $path);
            $webp = $img->encode(new WebpEncoder(quality: self::QUALITY));
            $fs->put($webpPath, (string) $webp);
        }
    }

    /**
     * Render an in-memory variant for the on-the-fly /img endpoint.
     * Returns [binary, mimeType].
     *
     * @return array{0:string,1:string}
     */
    public function variant(string $absolutePath, int $width, int $quality, string $format): array
    {
        $img = $this->manager->read($absolutePath);

        if ($img->width() > $width) {
            $img = $img->scale(width: $width);
        }

        $format = strtolower($format);
        $quality = max(1, min(100, $quality));

        [$encoded, $mime] = match ($format) {
            'png' => [$img->encode(new PngEncoder()), 'image/png'],
            'jpg', 'jpeg' => [$img->encode(new JpegEncoder(quality: $quality)), 'image/jpeg'],
            default => [$img->encode(new WebpEncoder(quality: $quality)), 'image/webp'],
        };

        return [(string) $encoded, $mime];
    }

    private function normalizeExtension(string $ext): string
    {
        $ext = strtolower($ext);
        return match ($ext) {
            'jpeg' => 'jpg',
            'jpg', 'png', 'webp' => $ext,
            default => 'jpg',
        };
    }
}
