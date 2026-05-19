<?php

namespace App\Http\Controllers;

use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function __construct(private readonly ImageService $images)
    {
    }

    /**
     * Serve a resized variant of a public-disk image with a 1-year immutable cache.
     *
     * GET /img/{path}?w=600&q=80&fmt=webp
     */
    public function show(Request $request, string $path): Response
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            abort(404);
        }

        $width = (int) $request->query('w', ImageService::MAX_WIDTH);
        $quality = (int) $request->query('q', ImageService::QUALITY);
        $format = (string) $request->query('fmt', 'webp');

        $width = max(16, min(4096, $width));

        [$binary, $mime] = $this->images->variant($disk->path($path), $width, $quality, $format);

        return response($binary, 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'Vary' => 'Accept',
        ]);
    }
}
