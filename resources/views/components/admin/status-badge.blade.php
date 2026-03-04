@props([
    'status',
    'activeText' => 'admin.status_active',
    'inactiveText' => 'admin.status_inactive',
])

@php
    $isActive = $status === 'active';
    $badgeClasses = $isActive
        ? 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
        : 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300';

    $iconSvg = $isActive
        ? '<svg class="h-3.5 w-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L7 11H4a2 2 0 01-2 2v6a2 2 0 012 2 8 0 012 2z"/></svg>'
        : '<svg class="h-3.5 w-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12M6 6v12m8 0h12M6 12v12m-4 0h.01M9 7h1m-1 4h1M9 17V9a1 1 0 01-1 1h2a1 1 0 011 1v4a1 1 0 011 1 1z"/></svg>';
@endphp

<span class="{{ $badgeClasses }}">
    {!! $iconSvg !!}
    {{ $isActive ? __($activeText) : __($inactiveText) }}
</span>
