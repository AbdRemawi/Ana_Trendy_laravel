@php
    use App\Helpers\NavigationHelper;

    $currentRoute = request()->route()->getName();
    $navigationItems = NavigationHelper::getItems();

    // Helper function to check if route is active
    $isActiveRoute = function($route) use ($currentRoute) {
        if ($route === '#') return false;
        if ($currentRoute === $route) return true;

        // Check if current route starts with the route name (for nested routes)
        // e.g., 'admin.users.edit' should match 'admin.users'
        $routeParts = explode('.', $route);
        $currentParts = explode('.', $currentRoute);

        // Check if all parts of the item route match the beginning of current route
        for ($i = 0; $i < count($routeParts); $i++) {
            if (!isset($currentParts[$i]) || $currentParts[$i] !== $routeParts[$i]) {
                return false;
            }
        }

        return count($currentParts) >= count($routeParts);
    };
@endphp

{{-- Backdrop overlay for mobile --}}
<div id="sidebar-backdrop"
     class="fixed inset-0 bg-black/20 backdrop-blur-[2px] z-40 lg:hidden hidden opacity-0 transition-opacity duration-300"
     aria-hidden="true"
     tabindex="-1">
</div>

<aside
    id="sidebar"
    class="fixed top-0 bottom-0 z-50 w-80 max-w-[90vw]
           h-screen min-h-0 overflow-hidden
           bg-primary
           border-white/10
           {{ $direction === 'rtl' ? 'right-0 translate-x-full border-l border-r-0' : 'left-0 -translate-x-full border-r border-l-0' }}
           lg:static lg:translate-x-0 lg:w-72 lg:max-w-none lg:z-auto
           flex flex-col
           transition-transform duration-300 ease-out lg:transition-none
           shadow-2xl lg:shadow-none"
    aria-label="{{ __('app.sidebar_navigation') }}"
    role="navigation"
    data-sidebar
>
    {{-- Logo and App Name Section --}}
    <div class="relative flex-shrink-0 pt-8 pb-6 px-6 border-b border-white/10">
        {{-- Close button (mobile only) --}}
        <button
            id="close-sidebar"
            class="absolute top-6 {{ $direction === 'rtl' ? 'left-4' : 'right-4' }} lg:hidden
                   w-8 h-8 flex items-center justify-center
                   rounded-lg bg-white/10 hover:bg-white/20
                   text-white/70 hover:text-white
                   transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white/30"
            aria-label="{{ __('app.close') }}"
            type="button"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Logo --}}
        <div class="flex items-center justify-center">
            <div class="relative flex items-center justify-center
                        w-16 h-16
                        bg-white rounded-xl
                        shadow-sm
                        border border-white/20">
                <img src="{{ asset('images/logo.png') }}"
                     alt="{{ __('app.name') }}"
                     class="w-11 h-11 object-contain"
                     loading="lazy">
            </div>
        </div>

        {{-- App Name --}}
        <div class="text-center mt-4">
            <h1 class="text-base font-medium text-white/90 tracking-tight">
                {{ config('app.name', 'Ana Trendy') }}
            </h1>
        </div>
    </div>

    {{-- Navigation Items --}}
    <nav class="flex-1 px-4 py-5 min-h-0 overflow-y-auto" role="navigation" aria-label="{{ __('app.main_navigation') }}">
        <div class="space-y-1" role="list">
            @foreach($navigationItems as $item)
                @can($item['permission'])
                    @php
                        $isPlaceholder = $item['route'] === '#';
                        $href = $isPlaceholder ? '#' : route($item['route']);
                        $isActive = $isActiveRoute($item['route']);
                        $borderClass = $direction === 'rtl' ? 'border-r-2' : 'border-l-2';
                    @endphp

                    <a
                        href="{{ $href }}"
                        @if($isPlaceholder) onclick="return false;" @endif
                        class="group flex items-center gap-3
                               px-3 py-2.5
                               rounded-lg
                               transition-all duration-200 ease-out
                               {{ $isActive ? 'bg-white/20 text-white ' . $borderClass . ' border-accent' : 'text-white/90 hover:bg-white/10 hover:text-white' }}
                               {{ $isPlaceholder ? 'opacity-35 cursor-not-allowed' : '' }}
                               focus:outline-none focus:ring-2 focus:ring-white/30
                               focus:bg-white/10"
                        role="listitem"
                        @if(!$isPlaceholder) aria-current="{{ $isActive ? 'page' : 'false' }}" @endif
                        @if($isPlaceholder) aria-disabled="true" @endif
                    >
                        {{-- Icon --}}
                        <svg class="w-[18px] h-[18px] flex-shrink-0 text-white
                                       transition-colors duration-200"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24"
                             aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}"/>
                        </svg>

                        {{-- Label --}}
                        <span class="text-sm font-medium truncate">
                            {{ __('app.' . $item['name']) }}
                        </span>
                    </a>
                @endcan
            @endforeach
        </div>
    </nav>

    {{-- User Profile Section --}}
    <div class="flex-shrink-0 p-4 border-t border-white/10">
        <div class="flex items-center gap-3">
            {{-- Avatar --}}
            <div class="w-10 h-10 rounded-full
                        bg-white
                        flex items-center justify-center
                        ring-2 ring-white/20
                        text-primary shadow-sm"
                 aria-hidden="true">
                <span class="text-sm font-semibold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </span>
            </div>

            {{-- User Info --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate">
                    {{ Auth::user()->name }}
                </p>
                <p class="text-xs text-white/50 truncate">
                    {{ Auth::user()->email }}
                </p>
            </div>
        </div>
    </div>
</aside>
