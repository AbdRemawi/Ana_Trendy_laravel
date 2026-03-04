@props([
    'title' => '',
    'value' => null,
    'icon' => null,
    'trend' => null,
    'trendUp' => true,
    'color' => 'primary',
    'description' => null,
])

@php
    $colorClasses = [
        'primary' => [
            'bg' => 'bg-white',
            'border' => 'border-accent-light',
            'text' => 'text-primary',
            'iconBg' => 'bg-accent-light',
            'label' => 'text-primary/50',
            'trendUp' => 'text-primary/80',
            'trendDown' => 'text-accent/80',
        ],
        'accent' => [
            'bg' => 'bg-white',
            'border' => 'border-accent/12',
            'text' => 'text-accent',
            'iconBg' => 'bg-accent-light',
            'label' => 'text-accent/60',
            'trendUp' => 'text-primary/80',
            'trendDown' => 'text-accent/80',
        ],
        'success' => [
            'bg' => 'bg-white',
            'border' => 'border-green-100',
            'text' => 'text-green-600',
            'iconBg' => 'bg-green-50',
            'label' => 'text-primary/50',
            'trendUp' => 'text-green-600',
            'trendDown' => 'text-red-600',
        ],
        'warning' => [
            'bg' => 'bg-white',
            'border' => 'border-amber-100',
            'text' => 'text-amber-600',
            'iconBg' => 'bg-amber-50',
            'label' => 'text-primary/50',
            'trendUp' => 'text-green-600',
            'trendDown' => 'text-red-600',
        ],
        'danger' => [
            'bg' => 'bg-white',
            'border' => 'border-red-100',
            'text' => 'text-red-600',
            'iconBg' => 'bg-red-50',
            'label' => 'text-primary/50',
            'trendUp' => 'text-red-600',
            'trendDown' => 'text-green-600',
        ],
    ];

    $colors = $colorClasses[$color] ?? $colorClasses['primary'];
@endphp

<div class="{{ $colors['bg'] }} p-4 sm:p-5 md:p-6 rounded-xl border {{ $colors['border'] }} shadow-sm hover:shadow-md transition-shadow duration-200 focus-within:ring-2 focus-within:ring-accent/50">
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <h3 class="text-sm font-medium {{ $colors['label'] }} mb-1">{{ $title }}</h3>
            @if($value !== null)
                <p class="text-3xl font-semibold {{ $colors['text'] }} tracking-tight">{{ $value }}</p>
            @endif
            @if($description !== null)
                <p class="text-xs {{ $colors['label'] }} mt-1">{{ $description }}</p>
            @endif
            @if($trend !== null)
                <div class="flex items-center gap-1 mt-2" aria-label="Trend: {{ $trendUp ? 'Up' : 'Down' }} {{ $trend }}">
                    <svg class="w-4 h-4 {{ $trendUp ? $colors['trendUp'] : $colors['trendDown'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $trendUp ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}"/>
                    </svg>
                    <span class="text-sm {{ $trendUp ? $colors['trendUp'] : $colors['trendDown'] }} font-medium">{{ $trend }}</span>
                </div>
            @endif
        </div>

        @if($icon !== null)
            <div class="w-12 h-12 {{ $colors['iconBg'] }} rounded-xl flex items-center justify-center flex-shrink-0 ring-1 ring-primary/8" aria-hidden="true">
                <svg class="w-6 h-6 {{ $colors['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icon }}"/>
                </svg>
            </div>
        @endif
    </div>
</div>
