@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.create_brand');
@endphp

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.brands.index') }}"
           class="p-2 text-gray-400 hover:text-gray-600
                  rounded-lg hover:bg-gray-100
                  transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('admin.create_brand') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.create_brand_description') }}
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Column: Brand Details Form --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">
                {{ __('admin.brand_details') }}
            </h2>

            <form method="POST" action="{{ route('admin.brands.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Name --}}
                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.brand_name') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="{{ __('admin.brand_name_placeholder') }}"
                        class="w-full px-4 py-2.5
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               {{ $errors->has('name') ? 'border-red-300' : '' }}"
                        @if($errors->has('name')) aria-invalid="true" aria-describedby="name-error" @endif
                    >
                    @error('name')
                        <p id="name-error" class="mt-1.5 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Logo --}}
                <div class="mb-5">
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.brand_logo') }}
                        <span class="text-gray-400 text-xs">{{ __('admin.optional') ?? '(Optional)' }}</span>
                    </label>
                    <input
                        type="file"
                        id="logo"
                        name="logo"
                        accept="image/jpeg,image/png,image/jpg,image/webp"
                        class="w-full px-4 py-2.5
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               file:mr-4 file:py-2 file:px-4
                               file:rounded-full file:border-0
                               file:text-sm file:font-semibold
                               file:bg-primary/10 file:text-primary
                               hover:file:bg-primary/20
                               {{ $errors->has('logo') ? 'border-red-300' : '' }}"
                        @if($errors->has('logo')) aria-invalid="true" aria-describedby="logo-error" @endif
                    >
                    <p class="mt-1.5 text-xs text-gray-500">
                        {{ __('admin.brand_logo_help') }}
                    </p>
                    @error('logo')
                        <p id="logo-error" class="mt-1.5 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Logo Preview --}}
                <div id="logo-preview" class="mb-5 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.preview') ?? 'Preview' }}
                    </label>
                    <div class="inline-block relative">
                        <img id="logo-preview-image" src="" alt="Logo preview"
                             class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                        <button type="button" id="remove-logo"
                                class="absolute -top-2 -right-2 p-1
                                       bg-red-500 text-white
                                       rounded-full
                                       hover:bg-red-600
                                       transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Status --}}
                <div class="mb-5">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.brand_status') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="status"
                        name="status"
                        class="w-full px-4 py-2.5
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               bg-white
                               {{ $errors->has('status') ? 'border-red-300' : '' }}"
                        @if($errors->has('status')) aria-invalid="true" aria-describedby="status-error" @endif
                    >
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>
                            {{ __('admin.status_active') }}
                        </option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>
                            {{ __('admin.status_inactive') }}
                        </option>
                    </select>
                    @error('status')
                        <p id="status-error" class="mt-1.5 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Submit Buttons --}}
                <div class="flex items-center gap-3 pt-4">
                    <button
                        type="submit"
                        class="flex-1 px-4 py-2.5
                               bg-primary text-white
                               rounded-lg
                               hover:bg-primary/90
                               focus:ring-2 focus:ring-primary/20
                               transition-all duration-200
                               font-medium text-sm">
                        {{ __('admin.create_brand') }}
                    </button>
                    <a href="{{ route('admin.brands.index') }}"
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

    {{-- Right Column: Info & Help --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full
                            bg-blue-100
                            flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900">
                    {{ __('admin.information') }}
                </h3>
            </div>

            <div class="space-y-3 text-sm text-gray-600">
                <div>
                    <strong class="text-gray-900">{{ __('admin.brand_name') }}:</strong>
                    <p>{{ __('admin.brand_name_help') ?? 'The brand name will be used to automatically generate a unique slug.' }}</p>
                </div>
                <div>
                    <strong class="text-gray-900">{{ __('admin.brand_logo') }}:</strong>
                    <p>{{ __('admin.brand_logo_help') }}</p>
                </div>
                <div>
                    <strong class="text-gray-900">{{ __('admin.brand_slug') }}:</strong>
                    <p>{{ __('admin.slug_auto_generated') ?? 'Slug will be automatically generated from the brand name.' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logoInput = document.getElementById('logo');
        const logoPreview = document.getElementById('logo-preview');
        const logoPreviewImage = document.getElementById('logo-preview-image');
        const removeLogoBtn = document.getElementById('remove-logo');

        if (logoInput && logoPreview && logoPreviewImage) {
            logoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        logoPreviewImage.src = e.target.result;
                        logoPreview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    logoPreview.classList.add('hidden');
                }
            });
        }

        if (removeLogoBtn) {
            removeLogoBtn.addEventListener('click', function() {
                logoInput.value = '';
                logoPreview.classList.add('hidden');
                logoPreviewImage.src = '';
            });
        }
    });
</script>
@endpush
@endsection
