@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.create_category');
@endphp

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.categories.index') }}"
           class="p-2 text-gray-400 hover:text-gray-600
                  rounded-lg hover:bg-gray-100
                  transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="{{ $direction === 'rtl' ? 'M9 5l7 7-7 7' : 'M15 19l-7-7 7-7' }}"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('admin.create_category') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.create_category_description') }}
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Column: Category Details Form --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">
                {{ __('admin.category_details') }}
            </h2>

            <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Name --}}
                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.category_name') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="{{ __('admin.category_name_placeholder') }}"
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

                {{-- Parent Category --}}
                <div class="mb-5">
                    <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.category_parent') }}
                        <span class="text-gray-400 text-xs">({{ __('admin.optional') ?? 'Optional' }})</span>
                    </label>
                    <select
                        id="parent_id"
                        name="parent_id"
                        class="w-full px-4 py-2.5
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               bg-white
                               {{ $errors->has('parent_id') ? 'border-red-300' : '' }}"
                        @if($errors->has('parent_id')) aria-invalid="true" aria-describedby="parent_id-error" @endif
                    >
                        <option value="">
                            {{ __('admin.no_parent') }}
                        </option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}"
                                    {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <p id="parent_id-error" class="mt-1.5 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Image --}}
                <div class="mb-5">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.category_image') }}
                        <span class="text-gray-400 text-xs">({{ __('admin.optional') ?? 'Optional' }})</span>
                    </label>
                    <input
                        type="file"
                        id="image"
                        name="image"
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
                               {{ $errors->has('image') ? 'border-red-300' : '' }}"
                        @if($errors->has('image')) aria-invalid="true" aria-describedby="image-error" @endif
                    >
                    <p class="mt-1.5 text-xs text-gray-500">
                        {{ __('admin.category_image_help') }}
                    </p>
                    @error('image')
                        <p id="image-error" class="mt-1.5 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Image Preview --}}
                <div id="image-preview" class="mb-5 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.preview') ?? 'Preview' }}
                    </label>
                    <div class="inline-block relative">
                        <img id="image-preview-image" src="" alt="Image preview"
                             class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                        <button type="button" id="remove-image"
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
                        {{ __('admin.category_status') }}
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

                {{-- Sort Order --}}
                <div class="mb-5">
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.category_sort_order') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        id="sort_order"
                        name="sort_order"
                        value="{{ old('sort_order', 0) }}"
                        min="0"
                        class="w-full px-4 py-2.5
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               {{ $errors->has('sort_order') ? 'border-red-300' : '' }}"
                        @if($errors->has('sort_order')) aria-invalid="true" aria-describedby="sort_order-error" @endif
                    >
                    @error('sort_order')
                        <p id="sort_order-error" class="mt-1.5 text-sm text-red-600">
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
                        {{ __('admin.create_category') }}
                    </button>
                    <a href="{{ route('admin.categories.index') }}"
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
                    <strong class="text-gray-900">{{ __('admin.category_name') }}:</strong>
                    <p>{{ __('admin.brand_name_help') ?? 'The category name will be used to automatically generate a unique slug.' }}</p>
                </div>
                <div>
                    <strong class="text-gray-900">{{ __('admin.category_parent') }}:</strong>
                    <p>{{ __('admin.category_parent_help') ?? 'Select a parent category to create a hierarchy. Leave empty for top-level categories.' }}</p>
                </div>
                <div>
                    <strong class="text-gray-900">{{ __('admin.category_image') }}:</strong>
                    <p>{{ __('admin.category_image_help') }}</p>
                </div>
                <div>
                    <strong class="text-gray-900">{{ __('admin.category_sort_order') }}:</strong>
                    <p>{{ __('admin.sort_order_help') ?? 'Lower numbers will appear first in the list.' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('image-preview');
        const imagePreviewImage = document.getElementById('image-preview-image');
        const removeImageBtn = document.getElementById('remove-image');

        if (imageInput && imagePreview && imagePreviewImage) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreviewImage.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.classList.add('hidden');
                }
            });
        }

        if (removeImageBtn) {
            removeImageBtn.addEventListener('click', function() {
                imageInput.value = '';
                imagePreview.classList.add('hidden');
                imagePreviewImage.src = '';
            });
        }
    });
</script>
@endpush
@endsection
