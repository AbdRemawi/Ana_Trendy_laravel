@props([
    'title' => null,
    'description' => null,
    'createRoute' => null,
    'createPermission' => null,
    'createText' => null,
])

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                {{ __($title) }}
            </h1>
            @if($description)
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __($description) }}
                </p>
            @endif
        </div>

        @if($createRoute && $createPermission)
            @can($createPermission)
                <a href="{{ $createRoute }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ $createText ? __($createText) : __('admin.create') }}
                </a>
            @endcan
        @endif
    </div>
</div>
