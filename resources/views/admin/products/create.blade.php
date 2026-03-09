@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.create_product');
    $sizes = \App\Models\Product::getAvailableSizes();
@endphp

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.products.index') }}"
           class="p-2 text-gray-400 hover:text-gray-600
                  rounded-lg hover:bg-gray-100
                  transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('admin.create_product') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.create_product_description') }}
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Column: Product Details Form --}}
    <div class="lg:col-span-2">
        <form method="POST" action="{{ route('admin.products.store') }}"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf

            {{-- Basic Information Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">
                    {{ __('admin.product_details') }}
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Name --}}
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('admin.product_name') }}
                            <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="{{ __('admin.product_name_placeholder') }}"
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

                    {{-- Brand --}}
                    <div>
                        <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('admin.product_brand') }}
                            <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="brand_id"
                            name="brand_id"
                            class="w-full px-4 py-2.5
                                   rounded-lg
                                   border border-gray-200
                                   focus:ring-2 focus:ring-primary/20 focus:border-primary
                                   transition-all duration-200
                                   text-sm
                                   bg-white
                                   {{ $errors->has('brand_id') ? 'border-red-300' : '' }}"
                            @if($errors->has('brand_id')) aria-invalid="true" aria-describedby="brand_id-error" @endif
                        >
                            <option value="">{{ __('admin.select_brand') }}</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <p id="brand_id-error" class="mt-1.5 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('admin.product_category') }}
                            <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="category_id"
                            name="category_id"
                            class="w-full px-4 py-2.5
                                   rounded-lg
                                   border border-gray-200
                                   focus:ring-2 focus:ring-primary/20 focus:border-primary
                                   transition-all duration-200
                                   text-sm
                                   bg-white
                                   {{ $errors->has('category_id') ? 'border-red-300' : '' }}"
                            @if($errors->has('category_id')) aria-invalid="true" aria-describedby="category_id-error" @endif
                        >
                            <option value="">{{ __('admin.select_category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p id="category_id-error" class="mt-1.5 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('admin.product_description') }}
                            <span class="text-gray-400 text-xs">({{ __('admin.optional') }})</span>
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            placeholder="{{ __('admin.product_description_placeholder') }}"
                            class="w-full px-4 py-2.5
                                   rounded-lg
                                   border border-gray-200
                                   focus:ring-2 focus:ring-primary/20 focus:border-primary
                                   transition-all duration-200
                                   text-sm
                                   resize-none
                                   {{ $errors->has('description') ? 'border-red-300' : '' }}"
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1.5 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Images Upload Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ __('admin.product_images') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('admin.primary_image_notice') }}
                        </p>
                    </div>
                    <span class="text-red-500">*</span>
                </div>

                <div class="space-y-6">
                    {{-- Image Upload Input --}}
                    <div>
                        <label for="images" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('admin.upload_images') }}
                            <span class="text-gray-400 text-xs">({{ __('admin.max_10_images') }})</span>
                        </label>
                        <input
                            type="file"
                            id="images"
                            name="images[]"
                            multiple
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
                                   file:bg-primary file:text-white
                                   hover:file:bg-primary/90
                                   {{ $errors->has('images') || $errors->has('images.*') ? 'border-red-300' : '' }}"
                            @if($errors->has('images') || $errors->has('images.*')) aria-invalid="true" @endif
                        >
                        <p class="mt-1.5 text-xs text-gray-500">
                            {{ __('admin.images_help') }}
                        </p>
                        @error('images')
                            <p class="mt-1.5 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                        @error('images.*')
                            <p class="mt-1.5 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Image Preview Container --}}
                    <div id="image-preview-container" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            {{ __('admin.image_preview') }}
                        </label>
                        <div id="image-preview-grid" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            {{-- Image cards will be dynamically inserted here --}}
                        </div>
                    </div>

                    {{-- No Images Message --}}
                    <div id="no-images-message" class="text-center py-8 px-4 border-2 border-dashed border-gray-200 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">
                            {{ __('admin.no_images_uploaded') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Attributes Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">
                    {{ __('admin.attributes') }}
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Size --}}
                    <div>
                        <label for="size" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('admin.product_size') }}
                            <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="size"
                            name="size"
                            class="w-full px-4 py-2.5
                                   rounded-lg
                                   border border-gray-200
                                   focus:ring-2 focus:ring-primary/20 focus:border-primary
                                   transition-all duration-200
                                   text-sm
                                   bg-white
                                   {{ $errors->has('size') ? 'border-red-300' : '' }}"
                            @if($errors->has('size')) aria-invalid="true" aria-describedby="size-error" @endif
                        >
                            <option value="">{{ __('admin.select_size') }}</option>
                            @foreach($sizes as $sizeValue => $sizeLabel)
                                <option value="{{ $sizeValue }}" {{ old('size') == $sizeValue ? 'selected' : '' }}>
                                    {{ $sizeLabel }}
                                </option>
                            @endforeach
                        </select>
                        @error('size')
                            <p id="size-error" class="mt-1.5 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Gender --}}
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('admin.product_gender') }}
                            <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="gender"
                            name="gender"
                            class="w-full px-4 py-2.5
                                   rounded-lg
                                   border border-gray-200
                                   focus:ring-2 focus:ring-primary/20 focus:border-primary
                                   transition-all duration-200
                                   text-sm
                                   bg-white
                                   {{ $errors->has('gender') ? 'border-red-300' : '' }}"
                            @if($errors->has('gender')) aria-invalid="true" aria-describedby="gender-error" @endif
                        >
                            <option value="">{{ __('admin.select_gender') }}</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>
                                {{ __('admin.gender_male') }}
                            </option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>
                                {{ __('admin.gender_female') }}
                            </option>
                            <option value="unisex" {{ old('gender') == 'unisex' ? 'selected' : '' }}>
                                {{ __('admin.gender_unisex') }}
                            </option>
                        </select>
                        @error('gender')
                            <p id="gender-error" class="mt-1.5 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Pricing Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">
                    {{ __('admin.prices') }}
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    {{-- Cost Price --}}
                    <div>
                        <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('admin.product_cost_price') }}
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 {{ app()->getLocale() === 'ar' ? 'right-0 pr-3' : 'left-0 pl-3' }} flex items-center text-gray-500">
                                @if(app()->getLocale() === 'ar') د.إ @else $ @endif
                            </span>
                            <input
                                type="number"
                                id="cost_price"
                                name="cost_price"
                                value="{{ old('cost_price') }}"
                                step="0.01"
                                min="0"
                                placeholder="0.00"
                                class="w-full px-4 py-2.5 {{ app()->getLocale() === 'ar' ? 'pr-8' : 'pl-8' }}
                                       rounded-lg
                                       border border-gray-200
                                       focus:ring-2 focus:ring-primary/20 focus:border-primary
                                       transition-all duration-200
                                       text-sm
                                       {{ $errors->has('cost_price') ? 'border-red-300' : '' }}"
                                @if($errors->has('cost_price')) aria-invalid="true" aria-describedby="cost_price-error" @endif
                            >
                        </div>
                        @error('cost_price')
                            <p id="cost_price-error" class="mt-1.5 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Sale Price --}}
                    <div>
                        <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('admin.product_sale_price') }}
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 {{ app()->getLocale() === 'ar' ? 'right-0 pr-3' : 'left-0 pl-3' }} flex items-center text-gray-500">
                                @if(app()->getLocale() === 'ar') د.إ @else $ @endif
                            </span>
                            <input
                                type="number"
                                id="sale_price"
                                name="sale_price"
                                value="{{ old('sale_price') }}"
                                step="0.01"
                                min="0"
                                placeholder="0.00"
                                class="w-full px-4 py-2.5 {{ app()->getLocale() === 'ar' ? 'pr-8' : 'pl-8' }}
                                       rounded-lg
                                       border border-gray-200
                                       focus:ring-2 focus:ring-primary/20 focus:border-primary
                                       transition-all duration-200
                                       text-sm
                                       {{ $errors->has('sale_price') ? 'border-red-300' : '' }}"
                                @if($errors->has('sale_price')) aria-invalid="true" aria-describedby="sale_price-error" @endif
                            >
                        </div>
                        @error('sale_price')
                            <p id="sale_price-error" class="mt-1.5 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Offer Price --}}
                    <div>
                        <label for="offer_price" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('admin.product_offer_price') }}
                            <span class="text-gray-400 text-xs">({{ __('admin.optional') }})</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 {{ app()->getLocale() === 'ar' ? 'right-0 pr-3' : 'left-0 pl-3' }} flex items-center text-gray-500">
                                @if(app()->getLocale() === 'ar') د.إ @else $ @endif
                            </span>
                            <input
                                type="number"
                                id="offer_price"
                                name="offer_price"
                                value="{{ old('offer_price') }}"
                                step="0.01"
                                min="0"
                                placeholder="0.00"
                                class="w-full px-4 py-2.5 {{ app()->getLocale() === 'ar' ? 'pr-8' : 'pl-8' }}
                                       rounded-lg
                                       border border-gray-200
                                       focus:ring-2 focus:ring-primary/20 focus:border-primary
                                       transition-all duration-200
                                       text-sm
                                       {{ $errors->has('offer_price') ? 'border-red-300' : '' }}"
                                @if($errors->has('offer_price')) aria-invalid="true" aria-describedby="offer_price-error" @endif
                            >
                        </div>
                        @error('offer_price')
                            <p id="offer_price-error" class="mt-1.5 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Status Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">
                    {{ __('admin.product_status') }}
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('admin.product_status') }}
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
                </div>
            </div>

            {{-- Initial Stock Card (CREATE ONLY - Not shown in edit) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ __('admin.initial_inventory') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('admin.initial_inventory_help') }}
                        </p>
                    </div>
                    <span class="text-red-500">*</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Initial Stock Quantity --}}
                    <div>
                        <label for="initial_quantity" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('admin.initial_stock_quantity') }}
                            <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="initial_quantity"
                            name="initial_quantity"
                            value="{{ old('initial_quantity', 0) }}"
                            min="0"
                            step="1"
                            placeholder="0"
                            class="w-full px-4 py-2.5
                                   rounded-lg
                                   border border-gray-200
                                   focus:ring-2 focus:ring-primary/20 focus:border-primary
                                   transition-all duration-200
                                   text-sm
                                   {{ $errors->has('initial_quantity') ? 'border-red-300' : '' }}"
                            @if($errors->has('initial_quantity')) aria-invalid="true" aria-describedby="initial_quantity-error" @endif
                        >
                        <p class="mt-1.5 text-xs text-gray-500">
                            {{ __('admin.initial_stock_help') }}
                        </p>
                        @error('initial_quantity')
                            <p id="initial_quantity-error" class="mt-1.5 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Info Box --}}
                    <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium">{{ __('admin.note') }}:</p>
                            <p class="mt-1">{{ __('admin.initial_stock_note') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit Buttons --}}
            <div class="flex items-center gap-3 pt-4">
                <button
                    type="submit"
                    class="flex-1 md:flex-none md:px-8 px-4 py-2.5
                           bg-primary text-white
                           rounded-lg
                           hover:bg-primary/90
                           focus:ring-2 focus:ring-primary/20
                           transition-all duration-200
                           font-medium text-sm">
                    {{ __('admin.create_product') }}
                </button>
                <a href="{{ route('admin.products.index') }}"
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

    {{-- Right Column: Info & Help --}}
    <div class="lg:col-span-1 space-y-6">
        {{-- Help Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
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
                    <strong class="text-gray-900">{{ __('admin.product_name') }}:</strong>
                    <p>{{ __('admin.product_name_help') }}</p>
                </div>
                <div>
                    <strong class="text-gray-900">{{ __('admin.product_images') }}:</strong>
                    <p>{{ __('admin.upload_images') }}. {{ __('admin.primary_image_notice') }}</p>
                </div>
                <div>
                    <strong class="text-gray-900">{{ __('admin.prices') }}:</strong>
                    <p>{{ __('admin.sale_price_help') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
window.ProductImageUploadI18n = {
    validationImagesMax: {{ Js::from(__('admin.validation_images_max')) }},
    primary: {{ Js::from(__('admin.primary')) }}
};
</script>

<script>
(function() {
    'use strict';

    // State management
    const state = {
        uploadedFiles: [],
        imagesLoadedCount: 0
    };

    // DOM elements cache
    const elements = {
        imageInput: null,
        previewContainer: null,
        previewGrid: null,
        noImagesMessage: null
    };

    // Initialize on DOM ready
    function init() {
        elements.imageInput = document.getElementById('images');
        elements.previewContainer = document.getElementById('image-preview-container');
        elements.previewGrid = document.getElementById('image-preview-grid');
        elements.noImagesMessage = document.getElementById('no-images-message');

        if (!elements.imageInput || !elements.previewContainer || !elements.previewGrid) {
            console.error('Required DOM elements not found');
            return;
        }

        elements.imageInput.addEventListener('change', handleFileSelect);
    }

    // Handle file selection
    function handleFileSelect(event) {
        const files = Array.from(event.target.files || []);

        if (files.length > 10) {
            alert(window.ProductImageUploadI18n.validationImagesMax);
            elements.imageInput.value = '';
            resetState();
            return;
        }

        if (files.length === 0) {
            resetState();
            return;
        }

        state.uploadedFiles = files;
        state.imagesLoadedCount = 0;

        renderImages();
    }

    // Reset state
    function resetState() {
        state.uploadedFiles = [];
        state.imagesLoadedCount = 0;
        elements.previewGrid.innerHTML = '';
        elements.previewContainer.classList.add('hidden');
        if (elements.noImagesMessage) {
            elements.noImagesMessage.classList.remove('hidden');
        }
    }

    // Render images for preview
    function renderImages() {
        elements.previewGrid.innerHTML = '';

        if (state.uploadedFiles.length === 0) {
            elements.previewContainer.classList.add('hidden');
            if (elements.noImagesMessage) {
                elements.noImagesMessage.classList.remove('hidden');
            }
            return;
        }

        elements.previewContainer.classList.remove('hidden');
        if (elements.noImagesMessage) {
            elements.noImagesMessage.classList.add('hidden');
        }

        // Process each file
        Array.from(state.uploadedFiles).forEach((file, index) => {
            const reader = new FileReader();

            reader.onload = function(e) {
                createImageCard(e.target.result, index);
                state.imagesLoadedCount++;
            };

            reader.onerror = function() {
                console.error('Error reading file:', file);
                state.imagesLoadedCount++;
            };

            reader.readAsDataURL(file);
        });
    }

    // Create image preview card
    function createImageCard(imgSrc, index) {
        const wrapper = document.createElement('div');
        wrapper.className = 'relative';

        // Card container
        const card = document.createElement('div');
        card.className = 'aspect-square rounded-lg overflow-hidden border-2 border-gray-200';

        // Image
        const img = document.createElement('img');
        img.src = imgSrc;
        img.alt = 'Image ' + (index + 1);
        img.className = 'w-full h-full object-cover';

        // Index badge (first image is marked as primary)
        const badge = document.createElement('div');
        if (index === 0) {
            badge.className = 'absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded font-medium flex items-center gap-1';
            badge.innerHTML = `
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span>${window.ProductImageUploadI18n.primary}</span>
            `;
        } else {
            badge.className = 'absolute bottom-2 left-2 bg-gray-900/70 text-white text-xs px-2 py-1 rounded font-medium';
            badge.textContent = '#' + (index + 1);
        }

        card.appendChild(img);
        card.appendChild(badge);
        wrapper.appendChild(card);

        elements.previewGrid.appendChild(wrapper);
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
@endpush
@endsection
