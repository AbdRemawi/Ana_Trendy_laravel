<header class="h-16 bg-white border-b border-accent-light/50 shadow-sm flex items-center justify-between px-3 sm:px-6 lg:px-8 flex-shrink-0 relative z-40">
    {{-- Mobile Menu Button --}}
    <button
        id="mobile-menu-button"
        type="button"
        class="lg:hidden p-2 rounded-lg text-primary/60 hover:bg-accent-light/60 hover:text-accent transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent/50"
        aria-label="{{ __('app.toggle_menu') }}"
        aria-expanded="false"
        aria-controls="sidebar"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    {{-- Page Title and Search --}}
    <div class="flex-1 min-w-0 flex items-center gap-2 sm:gap-4 lg:gap-6 px-2">
        <h1 class="text-base sm:text-lg md:text-xl lg:text-2xl font-bold text-primary truncate" title="{{ $pageTitle ?? __('app.dashboard') }}">
            {{ $pageTitle ?? __('app.dashboard') }}
        </h1>

        <div class="hidden sm:block">
            <x-search-bar />
        </div>
    </div>

    {{-- Mobile Search Button --}}
    <button
        id="mobile-search-button"
        type="button"
        class="sm:hidden p-2 rounded-lg text-primary/60 hover:bg-accent-light/60 hover:text-accent transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent/50"
        aria-label="{{ __('app.search') }}"
        aria-expanded="false"
        aria-controls="mobile-search-overlay"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </button>

    <div class="flex items-center gap-2 sm:gap-3 md:gap-4">
        {{-- Language Switcher --}}
        <x-language-switcher />

        {{-- User Profile Dropdown --}}
        <div class="relative" x-data="{ open: false }" x-cloak>
            <button
                @click="open = !open"
                @keydown.escape.window="open = false"
                @keydown.enter.prevent="open = !open"
                x-ref="dropdownButton"
                type="button"
                class="flex items-center gap-3 p-2 rounded-lg hover:bg-accent-light/70 transition-colors focus:outline-none focus:ring-2 focus:ring-accent/50"
                :aria-expanded="open ? 'true' : 'false'"
                aria-haspopup="true"
                id="user-menu-button"
            >
                <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center ring-1 ring-primary/10 border border-primary/6">
                    <svg class="w-5 h-5 text-primary/80" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <svg class="w-4 h-4 text-primary/40 flex-shrink-0 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.outside="open = false"
                @keydown.escape="open = false"
                x-ref="dropdownMenu"
                class="absolute {{ $direction === 'rtl' ? 'left-0' : 'right-0' }} mt-2 w-56 max-w-[calc(100vw-2rem)] bg-white rounded-xl shadow-lg border border-accent-light py-2 z-[100] overflow-hidden focus:outline-none"
                tabindex="-1"
                role="menu"
                aria-labelledby="user-menu-button"
                style="display: none;"
            >
                {{-- User Info Section --}}
                <div class="px-4 py-3 border-b border-accent-light" role="none">
                    <p class="text-sm font-medium text-primary/90">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-primary/50 truncate">{{ Auth::user()->email }}</p>
                </div>

                {{-- Profile Link --}}
                <div class="py-2" role="none">
                    <a
                        href="#"
                        x-ref="firstMenuItem"
                        role="menuitem"
                        tabindex="0"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-primary/70 hover:bg-accent-light/60 transition-colors focus:outline-none focus:bg-accent-light/60"
                    >
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="truncate">{{ __('app.profile') }}</span>
                    </a>
                </div>

                {{-- Logout Form --}}
                <div class="border-t border-accent-light pt-2" role="none">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            role="menuitem"
                            tabindex="0"
                            class="w-full flex items-center gap-3 px-4 py-2 text-sm text-primary/70 hover:bg-accent-light/60 transition-colors {{ $direction === 'rtl' ? 'text-right' : 'text-left' }} cursor-pointer focus:outline-none focus:bg-accent-light/60"
                        >
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span class="truncate">{{ __('auth.logout') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- Mobile Search Overlay --}}
<div id="mobile-search-overlay" class="fixed inset-0 bg-white z-[100] sm:hidden hidden" role="dialog" aria-modal="true" aria-labelledby="mobile-search-title">
    <div class="flex items-center gap-3 p-4 border-b border-accent-light">
        <button
            id="close-mobile-search"
            type="button"
            class="p-2 rounded-lg text-primary/60 hover:bg-primary/6 transition-colors focus:outline-none focus:ring-2 focus:ring-accent/50"
            aria-label="{{ __('app.close') }}"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div class="flex-1">
            <x-search-bar />
        </div>
    </div>
</div>
