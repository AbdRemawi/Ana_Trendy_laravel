<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Ana Trendy') }} - تسجيل الدخول</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=times-new-roman:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css'])
</head>
<body class="font-arabic bg-secondary text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="Ana Trendy" class="h-20 mx-auto mb-4">
            </div>

            <!-- Content -->
            @yield('content')

            <!-- Footer -->
            <div class="mt-8 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} Ana Trendy. جميع الحقوق محفوظة.
            </div>
        </div>
    </div>
</body>
</html>
