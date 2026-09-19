<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    /**
     * Stream a public-disk file straight through PHP.
     *
     * Why this exists: on production the public/storage nginx symlink is stale and 404s
     * most uploaded files, even though the files exist in storage/app/public. This route
     * reads the file with PHP (like the /img endpoint does) so it always resolves,
     * independent of the symlink — and unlike /img it does no image processing, so it has
     * no GD/Intervention dependency and streams the original bytes at full speed.
     *
     * GET /media/{path}
     */
    public function show(Request $request, string $path): Response
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            abort(404);
        }

        return $disk->response($path, null, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
