<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheImagesHeader
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (str_starts_with($request->path(), 'storage/')) {
            $response->headers->set('Cache-Control', 'public, max-age=2592000, immutable');
            $response->headers->set('Vary', 'Accept');
        }

        return $response;
    }
}
