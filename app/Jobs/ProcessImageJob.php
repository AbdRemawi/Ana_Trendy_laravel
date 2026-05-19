<?php

namespace App\Jobs;

use App\Services\ImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;
    public int $backoff = 30;

    public function __construct(
        public readonly string $path,
        public readonly string $disk = 'public',
    ) {
    }

    public function handle(ImageService $images): void
    {
        $images->process($this->path, $this->disk);
    }

    public function failed(Throwable $e): void
    {
        Log::warning('ProcessImageJob failed', [
            'path' => $this->path,
            'disk' => $this->disk,
            'error' => $e->getMessage(),
        ]);
    }
}
