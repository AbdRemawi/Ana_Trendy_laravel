@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.create_city');
@endphp

<div class="max-w-2xl mx-auto">
    {{-- Page Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-900">
            {{ __('admin.create_city') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('admin.create_city_description') }}
        </p>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <form method="POST" action="{{ route('admin.cities.store') }}" class="p-6">
            @csrf

            {{-- Name --}}
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('admin.city_name') }}
                    <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name') }}"
                       placeholder="{{ __('admin.city_name_placeholder') }}"
                       required
                       class="w-full px-4 py-2
                              rounded-lg
                              border border-gray-200
                              focus:ring-2 focus:ring-primary/20 focus:border-primary
                              transition-all duration-200
                              text-sm">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div class="mb-6">
                <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('admin.city_status') }}
                    <span class="text-red-500">*</span>
                </label>
                <select id="is_active"
                        name="is_active"
                        required
                        class="w-full px-4 py-2
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               bg-white">
                    <option value="1" selected>{{ __('admin.status_active') }}</option>
                    <option value="0">{{ __('admin.status_inactive') }}</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.cities.index') }}"
                   class="px-4 py-2
                          border border-gray-200
                          rounded-lg
                          hover:bg-gray-50
                          transition-colors duration-200
                          font-medium text-sm text-gray-700">
                    {{ __('admin.cancel') }}
                </a>
                <button type="submit"
                        class="px-6 py-2
                               bg-primary text-white
                               rounded-lg
                               hover:bg-primary/90
                               transition-colors duration-200
                               font-medium text-sm">
                    {{ __('admin.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
