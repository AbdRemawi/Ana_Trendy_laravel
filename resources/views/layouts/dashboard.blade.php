<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $direction }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Ana Trendy') }} - {{ $pageTitle ?? __('app.dashboard') }}</title>

    @if($locale === 'en')
        {{-- Font for English: Minion Variable Concept --}}
        <link rel="preconnect" href="https://fonts.cdnfonts.com" crossorigin>
        <link href="https://fonts.cdnfonts.com/css/minion-variable-concept&display=swap" rel="stylesheet">
    @endif
    {{-- Arabic font (Times New Roman) is set in CSS via [lang="ar"] selector --}}

    @stack('styles')

    {{-- jQuery for Select2 --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen w-full bg-accent-light text-gray-900 antialiased overflow-x-clip lg:h-[100dvh] lg:overflow-hidden">
    {{--
        Dashboard Layout Structure (responsive scroll model):
        - Mobile (< lg): body grows with content (min-h-screen) and scrolls naturally;
          overflow-x-CLIP (not hidden) prevents sideways scroll WITHOUT turning <body>
          into a scroll container — hidden would force overflow-y:auto, making the body
          itself scroll and bottom out before the end on mobile. Inner panes don't lock
          height, so the last table rows + pagination are always reachable.
        - Desktop (lg+): body is a fixed 100dvh frame (lg:h-[100dvh] + lg:overflow-hidden);
          the sidebar stays fixed and only <main> scrolls (inner-scroll layout).
        - min-h-0 on flex children prevents them from growing beyond container (flex overflow bug).
        - 100dvh (dynamic viewport height) keeps the mobile browser chrome from eating content.
    --}}
    <div class="flex min-h-0 overflow-x-clip lg:h-full">
        {{-- Sidebar --}}
        <x-dashboard.sidebar />

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col min-w-0 lg:h-full lg:overflow-hidden bg-accent-light">
            {{-- Header --}}
            <x-dashboard.header />

            {{-- Page Content - scrolls with the page on mobile, inner-scrolls on lg+ --}}
            <main class="flex-1 overflow-y-visible lg:overflow-y-auto p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-800 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if(session('warning'))
                        <div class="mb-6 p-4 bg-yellow-100 border border-yellow-200 text-yellow-800 rounded-lg">
                            {{ session('warning') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- Delete Confirmation Modal with built-in delete handling --}}
    <x-admin.delete-modal />

    {{-- Scripts stack for page-specific JavaScript --}}
    @stack('scripts')
</body>
</html>
