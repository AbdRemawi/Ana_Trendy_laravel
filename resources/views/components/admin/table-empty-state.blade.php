@props([
    'title' => 'admin.no_records_found',
    'description' => null,
    'icon' => null,
    'actionText' => null,
    'actionRoute' => null,
])

<div class="flex flex-col items-center justify-center py-12 text-center">
    @if($icon)
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2h12a2 2 0 002 2v16m14 0h2M9 17V9a1 1 0 01-1 1h2a1 1 0 011 1v4a1 1 0 011 1 1z"/>
            </svg>
        </div>
    @endif

    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">
        {{ __($title) }}
    </h3>

    @if($description)
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            {{ __($description) }}
        </p>
    @endif

    @if($actionText && $actionRoute)
        <div class="mt-6">
            <a href="{{ $actionRoute }}"
               class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary/90">
                {{ __($actionText) }}
            </a>
        </div>
    @endif
</div>
