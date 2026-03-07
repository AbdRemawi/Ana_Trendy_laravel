@props([
    'model',
    'viewRoute' => null,
    'editRoute' => null,
    'deleteRoute' => null,
    'restoreRoute' => null,
    'viewPermission' => null,
    'editPermission' => null,
    'deletePermission' => null,
    'restorePermission' => null,
    'showView' => true,
    'showEdit' => true,
    'showDelete' => true,
    'showRestore' => false,
])

@php
    $isDeleted = method_exists($model, 'trashed') && $model->trashed();
@endphp

<div class="flex items-center gap-1">
    {{-- View Button --}}
    @if($showView && $viewRoute && !$isDeleted)
        @if($viewPermission)
            @can($viewPermission)
                <a href="{{ route($viewRoute, $model) }}"
                   class="p-2 text-gray-400 transition-colors hover:text-primary dark:hover:text-primary/80"
                   title="{{ __('admin.view') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 16 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057 5.064 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </a>
            @endcan
        @else
            <a href="{{ route($viewRoute, $model) }}"
               class="p-2 text-gray-400 transition-colors hover:text-primary dark:hover:text-primary/80"
               title="{{ __('admin.view') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 16 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057 5.064 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </a>
        @endif
    @endif

    {{-- Edit Button --}}
    @if($showEdit && $editRoute && !$isDeleted)
        @if($editPermission)
            @can($editPermission)
                <a href="{{ route($editRoute, $model) }}"
                   class="p-2 text-gray-400 transition-colors hover:text-primary dark:hover:text-primary/80"
                   title="{{ __('admin.edit') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414A2 2 0 012.828 2.828L11 828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </a>
            @endcan
        @else
            <a href="{{ route($editRoute, $model) }}"
               class="p-2 text-gray-400 transition-colors hover:text-primary dark:hover:text-primary/80"
               title="{{ __('admin.edit') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414A2 2 0 012.828 2.828L11 828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
        @endif
    @endif

    {{-- Restore Button (for soft deleted items) --}}
    @if($showRestore && $restoreRoute && $isDeleted)
        @if($restorePermission)
            @can($restorePermission)
                <a href="{{ route($restoreRoute, $model) }}"
                   class="p-2 text-gray-400 transition-colors hover:text-green-600 dark:hover:text-green-400"
                   title="{{ __('admin.restore') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v2a2 2 0 012-2h10a2 2 0 112 2v6a2 2 0 01-2 2h-2a1 1 0 01-1 1v5a1 1 0 011-1 1z"/>
                    </svg>
                </a>
            @endcan
        @else
            <a href="{{ route($restoreRoute, $model) }}"
               class="p-2 text-gray-400 transition-colors hover:text-green-600 dark:hover:text-green-400"
               title="{{ __('admin.restore') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v2a2 2 0 012-2h10a2 2 0 112 2v6a2 2 0 01-2 2h-2a1 1 0 01-1 1v5a1 1 0 011-1 1z"/>
                </svg>
            </a>
        @endif
    @endif

    {{-- Delete Button --}}
    @if($showDelete && $deleteRoute && !$isDeleted)
        @if($deletePermission)
            @can($deletePermission)
                <button type="button"
                        class="delete-btn p-2 text-gray-400 transition-colors hover:text-red-600 dark:hover:text-red-400"
                        title="{{ __('admin.delete') }}"
                        data-delete-url="{{ route($deleteRoute, $model) }}"
                        data-confirm-message="{{ __('admin.confirm_delete_item', ['item' => $model->name ?? $model->id ?? '']) }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6h2m-2 0h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 012 2z"/>
                    </svg>
                </button>
            @endcan
        @else
            <button type="button"
                    class="delete-btn p-2 text-gray-400 transition-colors hover:text-red-600 dark:hover:text-red-400"
                    title="{{ __('admin.delete') }}"
                    data-delete-url="{{ route($deleteRoute, $model) }}"
                    data-confirm-message="{{ __('admin.confirm_delete_item', ['item' => $model->name ?? $model->id ?? '']) }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6h2m-2 0h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 012 2z"/>
                </svg>
            </button>
        @endif
    @endif
</div>
