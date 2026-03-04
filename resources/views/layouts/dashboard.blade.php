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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen w-full bg-accent-light text-gray-900 antialiased overflow-hidden">
    {{--
        Dashboard Layout Structure:
        - body: h-screen + overflow-hidden fixes body at viewport height, prevents body scroll
        - Outer flex: h-full to fill the body height
        - min-h-0 on flex children prevents them from growing beyond container (fixes flex item overflow bug)
        - Main content uses flex-col to stack header and scrollable content
    --}}
    <div class="flex h-full min-h-0 overflow-x-hidden">
        {{-- Sidebar --}}
        <x-dashboard.sidebar />

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-accent-light">
            {{-- Header --}}
            <x-dashboard.header />

            {{-- Page Content - Only this area scrolls --}}
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- Scripts stack for page-specific JavaScript --}}
    @stack('scripts')

    {{-- Delete Confirmation Modal with built-in delete handling --}}
    <x-admin.delete-modal />
</body>
</html>
