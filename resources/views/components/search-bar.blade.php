@php
    $placeholder = $placeholder ?? __('app.search');
    $name = $name ?? 'search';
    $value = $value ?? '';
@endphp

<div
    x-data="{ searchValue: '{{ $value }}' }"
    class="relative group w-full"
>
    {{-- Search Input Wrapper --}}
    <div class="relative flex items-center">
        {{-- Search Icon (Left/Right based on RTL) --}}
        <svg
            class="absolute start-3 w-5 h-5 text-primary/40 pointer-events-none group-focus-within:text-primary/60 transition-colors duration-200"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            />
        </svg>

        {{-- Search Input - Full width on mobile, fixed on desktop --}}
        <input
            type="search"
            name="{{ $name }}"
            x-model="searchValue"
            placeholder="{{ $placeholder }}"
            aria-label="{{ $placeholder }}"
            class="search-input w-full sm:w-48 md:w-56 lg:w-64 h-10 ps-10 pe-10 bg-accent-light/50 hover:bg-accent-light/80 focus:bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary/40 border border-primary/10 rounded-full text-sm text-gray-900 placeholder-primary/40 transition-all duration-200 ease-in-out"
        />

        {{-- Clear Button (shows when input has value) --}}
        <button
            x-show="searchValue && searchValue.length > 0"
            x-cloak
            x-transition
            type="button"
            aria-label="{{ __('app.clear') }}"
            class="absolute end-3 p-1 text-primary/40 hover:text-primary/70 rounded-full hover:bg-accent-light/80 transition-colors duration-150"
            @click="searchValue = ''; $el.previousElementSibling.focus()"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Keyboard Shortcut Indicator (Desktop Only) --}}
        <div
            x-show="!searchValue || searchValue.length === 0"
            x-transition
            class="hidden lg:flex absolute end-3 top-1/2 -translate-y-1/2 items-center gap-0.5 px-1.5 py-0.5 bg-accent-light/70 rounded text-[10px] font-medium text-primary/60 pointer-events-none"
        >
            <span class="scale-75">⌘</span>
            <span class="scale-75">K</span>
        </div>
    </div>
</div>
