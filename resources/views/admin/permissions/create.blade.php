@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.create_permission');
@endphp

<div class="max-w-2xl mx-auto">
    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.permissions.index') }}"
               class="p-2 text-gray-400 hover:text-gray-600
                      rounded-lg hover:bg-gray-100
                      transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ __('admin.create_permission') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('admin.create_permission_description') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.permissions.store') }}">
            @csrf

            {{-- Permission Name --}}
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                    {{ __('admin.permission_name') }}
                    <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="manage products"
                    class="w-full px-4 py-2.5
                           rounded-lg
                           border border-gray-200
                           focus:ring-2 focus:ring-primary/20 focus:border-primary
                           transition-all duration-200
                           text-sm
                           {{ $errors->has('name') ? 'border-red-300' : '' }}"
                    @if($errors->has('name')) aria-invalid="true" aria-describedby="name-error" @endif
                >
                <p class="mt-1.5 text-xs text-gray-500">
                    {{ __('admin.permission_name_help') }}
                </p>
                @error('name')
                    <p id="name-error" class="mt-1.5 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Info Box --}}
            <div class="mb-6 p-4 bg-blue-50 border border-blue-100 rounded-lg">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <strong>{{ __('admin.auto_format') }}:</strong>
                        {{ __('admin.permission_auto_format_description') }}
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                <button
                    type="submit"
                    class="flex-1 px-4 py-2.5
                           bg-primary text-white
                           rounded-lg
                           hover:bg-primary/90
                           focus:ring-2 focus:ring-primary/20
                           transition-all duration-200
                           font-medium text-sm">
                    {{ __('admin.create_permission') }}
                </button>
                <a href="{{ route('admin.permissions.index') }}"
                   class="px-4 py-2.5
                          border border-gray-200
                          rounded-lg
                          hover:bg-gray-50
                          transition-colors duration-200
                          font-medium text-sm text-gray-700">
                    {{ __('admin.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
