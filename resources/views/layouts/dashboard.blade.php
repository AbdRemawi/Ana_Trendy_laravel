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
