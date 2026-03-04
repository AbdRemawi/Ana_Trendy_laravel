@extends('layouts.auth')

@section('content')
    <div class="w-full max-w-[440px]">
        <!-- Login Surface -->
        <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl shadow-primary/10 overflow-hidden">
            <!-- Header Section -->
            <div class="pt-12 pb-8 px-10 text-center">
                <!-- Logo -->
                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="{{ __('app.name') }}"
                    class="h-16 mx-auto mb-8"
                >

                <!-- Language Switcher -->
                <div class="flex justify-center mb-8">
                    <x-language-switcher />
                </div>

                <!-- Title & Subtitle -->
                <h1 class="text-3xl font-bold text-primary mb-3">
                    {{ __('auth.login') }}
                </h1>
                <p class="text-base text-gray-500 font-light">
                    {{ __('auth.login_subtitle') }}
                </p>
            </div>

            <!-- Form Section -->
            <div class="px-10 pb-10">
                <!-- Error Alert -->
                @if (session('errors'))
                    <div class="mb-6 bg-red-50/80 backdrop-blur-sm rounded-xl p-4" role="alert">
                        <div class="text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <p class="mb-1 last:mb-0">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login.process') }}" class="space-y-6">
                    @csrf

                    <!-- Email/Mobile Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2.5">
                            {{ __('auth.email') }}
                        </label>
                        <input
                            id="email"
                            type="text"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            class="w-full px-5 py-4 bg-gray-50/50 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:bg-white transition-all duration-200"
                            placeholder="{{ __('auth.email_placeholder') }}"
                        >
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2.5">
                            {{ __('auth.password') }}
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="w-full px-5 py-4 bg-gray-50/50 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:bg-white transition-all duration-200"
                            placeholder="{{ __('auth.password_placeholder') }}"
                        >
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer group">
                            <input
                                type="checkbox"
                                name="remember"
                                class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-2 focus:ring-primary/30 focus:ring-offset-0 cursor-pointer transition-all duration-200"
                            >
                            <span class="ms-3 text-sm text-gray-600 select-none group-hover:text-gray-700 transition-colors">
                                {{ __('auth.remember_me') }}
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full bg-primary hover:bg-primary/95 active:bg-primary/90 text-white font-semibold py-4 px-6 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 focus:ring-offset-2 transition-all duration-200 shadow-lg shadow-primary/25 hover:shadow-xl hover:shadow-primary/30"
                    >
                        {{ __('auth.login_button') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500/80">
                &copy; {{ date('Y') }} {{ __('app.name') }} · {{ __('app.copyright') }}
            </p>
        </div>
    </div>
@endsection
