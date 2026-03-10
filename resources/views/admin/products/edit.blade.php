@extends('layouts.dashboard')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 42px;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.5rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 0;
        line-height: 32px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
    .select2-results__option {
        padding: 8px 12px;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: #e0f2fe;
    }
    .select2-dropdown {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }
</style>
@endpush

@section('content')
@php
    $pageTitle = __('admin.edit_product');
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
                {{ __('admin.edit_product') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.edit_product_description') }}
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Column: Product Details Form --}}
    <div class="lg:col-span-2">
        <form method="POST" action="{{ route('admin.products.update', $product) }}"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @method('PUT')

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
                            value="{{ old('name', $product->name) }}"
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
                            class="brand-select w-full px-4 py-2.5
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
                                <option value="{{ $brand->id }}"
                                        {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}
                                        data-image="{{ $brand->logo_url }}">
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
                            class="category-select w-full px-4 py-2.5
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
                                <option value="{{ $category->id }}"
                                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}
                                        data-image="{{ $category->image_url }}">
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
                        >{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="mt-1.5 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Images Management Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ __('admin.product_images') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('admin.select_primary_image_help') }}
                        </p>
                    </div>
                </div>

                {{-- All Images Container (Existing + New will be merged here) --}}
                <div id="all-images-container" class="mb-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="images-grid">
                        {{-- Existing Images --}}
                        @foreach($product->images->sortBy('sort_order') as $image)
                            <div class="image-card-wrapper relative" data-image-id="{{ $image->id }}" data-is-existing="true">
                                {{-- Radio button for primary selection --}}
                                <input type="radio"
                                       name="primary_image_id"
                                       value="{{ $image->id }}"
                                       id="image_{{ $image->id }}"
                                       class="peer sr-only primary-radio"
                                       {{ $image->is_primary ? 'checked' : '' }}>

                                <label for="image_{{ $image->id }}"
                                       class="block relative cursor-pointer group image-card-label">
                                    <div class="aspect-square rounded-lg overflow-hidden border-2 transition-all duration-300 ease-in-out transform
                                                {{ $image->is_primary ? 'border-blue-500 ring-2 ring-blue-500/20 scale-105 shadow-lg' : 'border-gray-200 hover:border-blue-400 hover:shadow-md' }}"
                                         data-image-card="true">
                                        <img src="{{ $image->image_url }}"
                                             alt="{{ $product->name }}"
                                             class="w-full h-full object-cover">

                                        {{-- Primary badge --}}
                                        <div class="absolute top-2 left-2 bg-blue-500 text-white text-xs px-2 py-1 rounded font-medium flex items-center gap-1 {{ $image->is_primary ? '' : 'hidden' }}"
                                             data-primary-badge="true">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>{{ __('admin.primary') }}</span>
                                        </div>

                                        {{-- Check icon --}}
                                        <div class="absolute top-2 right-2 w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center shadow-lg {{ $image->is_primary ? '' : 'hidden' }}"
                                             data-primary-check="true">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>

                                        {{-- Remove button --}}
                                        <button type="button"
                                                class="absolute bottom-2 right-2 w-8 h-8 rounded-full
                                                       bg-red-500 text-white flex items-center justify-center
                                                       opacity-0 group-hover:opacity-100 transition-opacity
                                                       hover:bg-red-600 shadow-lg remove-image-btn"
                                                data-image-id="{{ $image->id }}"
                                                title="{{ __('admin.remove_image') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </label>

                                {{-- Hidden input for tracking removal --}}
                                <input type="hidden"
                                       name="remove_images[]"
                                       value=""
                                       class="remove-image-hidden-input"
                                       data-image-id="{{ $image->id }}">
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Upload New Images Section --}}
                <div>
                    <label for="images" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.upload_new_images') }}
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
                               hover:file:bg-primary/90"
                    >
                    <p class="mt-1.5 text-xs text-gray-500">
                        {{ __('admin.images_help') }}
                    </p>
                </div>

                @error('images')
                    <p class="mt-3 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
                @error('primary_image_id')
                    <p class="mt-3 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
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
                            @foreach($sizes as $sizeValue => $sizeLabel)
                                <option value="{{ $sizeValue }}" {{ old('size', $product->size) == $sizeValue ? 'selected' : '' }}>
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
                            <option value="male" {{ old('gender', $product->gender) == 'male' ? 'selected' : '' }}>
                                {{ __('admin.gender_male') }}
                            </option>
                            <option value="female" {{ old('gender', $product->gender) == 'female' ? 'selected' : '' }}>
                                {{ __('admin.gender_female') }}
                            </option>
                            <option value="unisex" {{ old('gender', $product->gender) == 'unisex' ? 'selected' : '' }}>
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
                                value="{{ old('cost_price', $product->cost_price) }}"
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
                                value="{{ old('sale_price', $product->sale_price) }}"
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
                                value="{{ old('offer_price', $product->offer_price) }}"
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

                {{-- Profit Margin Display --}}
                @if($product->cost_price > 0 && $product->sale_price > 0)
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">{{ __('admin.profit_margin') }}:</span>
                        <span class="text-sm font-semibold {{ $product->profit_margin > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $product->profit_margin }}%
                        </span>
                    </div>
                </div>
                @endif
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
                            <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>
                                {{ __('admin.status_active') }}
                            </option>
                            <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>
                                {{ __('admin.status_inactive') }}
                            </option>
                        </select>
                        @error('status')
                            <p id="status-error" class="mt-1.5 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Slug Display --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('admin.product_slug') }}
                        </label>
                        <div class="px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50">
                            <span class="text-sm text-gray-600 font-mono">
                                {{ $product->slug }}
                            </span>
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
                    {{ __('admin.update_product') }}
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

    {{-- Right Column: Quick Actions --}}
    <div class="lg:col-span-1 space-y-6">
        {{-- Inventory Management --}}
        @can('manage products')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">
                {{ __('admin.view_inventory') }}
            </h3>
            <p class="text-sm text-gray-600 mb-4">
                {{ __('admin.inventory_description') }}
            </p>
            <a href="{{ route('admin.inventory.by-product', $product->id) }}"
               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2
                      bg-green-50 text-green-700
                      rounded-lg
                      hover:bg-green-100
                      transition-colors duration-200
                      font-medium text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                {{ __('admin.view_inventory') }}
            </a>
        </div>
        @endcan
    </div>
</div>

@push('scripts')
<script>
(function() {
    'use strict';

    // Translation strings passed from controller
    const translations = {{ json_encode($translations) }};

    // DOM elements
    const elements = {
        imageInput: null,
        imagesGrid: null,
        form: null
    };

    // State for new images (temporary, before form submission)
    let newImagesData = []; // Store file objects and their metadata
    let selectedNewImageIndex = null; // Track which new image is selected as primary

    // Initialize
    function init() {
        elements.imageInput = document.getElementById('images');
        elements.imagesGrid = document.getElementById('images-grid');
        elements.form = document.querySelector('form');

        if (!elements.imageInput || !elements.imagesGrid) {
            console.error('Required DOM elements not found');
            return;
        }

        // Initialize existing image handlers
        initExistingImages();

        // Attach new images handler
        elements.imageInput.addEventListener('change', handleNewFiles);

        // Add form validation
        if (elements.form) {
            elements.form.addEventListener('submit', handleFormSubmit);
        }
    }

    // Initialize existing images
    function initExistingImages() {
        // Add event listeners to remove buttons
        const removeButtons = elements.imagesGrid.querySelectorAll('.remove-image-btn');
        removeButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation(); // Prevent label from being triggered
                handleRemoveExisting(this.dataset.imageId);
            });
        });

        // Add change listeners to radio buttons for visual updates
        const radioButtons = elements.imagesGrid.querySelectorAll('input[name="primary_image_id"]');
        radioButtons.forEach(radio => {
            radio.addEventListener('change', function() {
                selectedNewImageIndex = null; // Clear new image selection when existing is selected
                updateAllImagesVisuals();
            });
        });

        // Add click listeners to labels for immediate visual feedback
        const labels = elements.imagesGrid.querySelectorAll('.image-card-label');
        labels.forEach(label => {
            label.addEventListener('click', function(e) {
                // Get the associated radio button
                const radioId = label.getAttribute('for');
                const radio = document.getElementById(radioId);

                if (radio) {
                    // Check the radio button
                    radio.checked = true;

                    // Clear new image selection
                    selectedNewImageIndex = null;

                    // Trigger change event
                    radio.dispatchEvent(new Event('change'));

                    // Immediate visual update
                    updateAllImagesVisuals();
                }
            });
        });
    }

    // Handle new file selection
    function handleNewFiles(event) {
        const files = Array.from(event.target.files || []);

        if (files.length === 0) {
            clearNewImages();
            return;
        }

        // Clear previous new images
        clearNewImages();

        // Read and display new images
        newImagesData = files;
        let loadedCount = 0;

        files.forEach((file, index) => {
            const reader = new FileReader();

            reader.onload = function(e) {
                createNewImageCard(e.target.result, index);
                loadedCount++;

                // Auto-select first new image as primary if no existing primary is selected
                if (index === 0) {
                    const existingPrimary = elements.imagesGrid.querySelector('input[name="primary_image_id"]:checked');
                    if (!existingPrimary) {
                        selectNewImageAsPrimary(0);
                    }
                }
            };

            reader.onerror = function() {
                console.error('Error reading file:', file);
                loadedCount++;
            };

            reader.readAsDataURL(file);
        });
    }

    // Create a new image preview card
    function createNewImageCard(imgSrc, index) {
        const wrapper = document.createElement('div');
        wrapper.className = 'image-card-wrapper relative';
        wrapper.setAttribute('data-new-image-index', index);
        wrapper.setAttribute('data-is-new', 'true');

        // Card container with pointer-events-none on children to ensure clicks reach the card
        const card = document.createElement('div');
        card.className = 'aspect-square rounded-lg overflow-hidden border-2 cursor-pointer transition-all duration-300 ease-in-out transform border-gray-200 hover:border-blue-400 hover:shadow-md relative';
        card.setAttribute('data-new-image-card', 'true');
        card.setAttribute('data-new-image-index', index);
        card.style.position = 'relative';
        card.style.zIndex = '1';

        // Image container with pointer-events-none to let clicks pass through
        const imgContainer = document.createElement('div');
        imgContainer.className = 'w-full h-full';
        imgContainer.style.pointerEvents = 'none';

        // Image
        const img = document.createElement('img');
        img.src = imgSrc;
        img.alt = 'New image ' + (index + 1);
        img.className = 'w-full h-full object-cover';

        imgContainer.appendChild(img);
        card.appendChild(imgContainer);

        // Primary badge (hidden by default) - with pointer-events-none
        const badge = document.createElement('div');
        badge.className = 'absolute top-2 left-2 bg-blue-500 text-white text-xs px-2 py-1 rounded font-medium flex items-center gap-1 hidden';
        badge.setAttribute('data-new-primary-badge', 'true');
        badge.style.pointerEvents = 'none';
        badge.style.zIndex = '2';
        badge.innerHTML = `
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            <span>{{ __('admin.primary') }}</span>
        `;

        // Check icon (hidden by default) - with pointer-events-none
        const checkIcon = document.createElement('div');
        checkIcon.className = 'absolute top-2 right-2 w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center shadow-lg hidden';
        checkIcon.setAttribute('data-new-primary-check', 'true');
        checkIcon.style.pointerEvents = 'none';
        checkIcon.style.zIndex = '2';
        checkIcon.innerHTML = `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        `;

        // Index badge - with pointer-events-none
        const indexBadge = document.createElement('div');
        indexBadge.className = 'absolute bottom-2 left-2 bg-gray-900/70 text-white text-xs px-2 py-1 rounded font-medium';
        indexBadge.style.pointerEvents = 'none';
        indexBadge.style.zIndex = '2';
        indexBadge.textContent = 'New #' + (index + 1);

        // Add elements to card
        card.appendChild(badge);
        card.appendChild(checkIcon);
        card.appendChild(indexBadge);
        wrapper.appendChild(card);

        // Click handler to select as primary
        card.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            selectNewImageAsPrimary(index);
        });

        // Add to grid
        elements.imagesGrid.appendChild(wrapper);
    }

    // Select a new image as primary
    function selectNewImageAsPrimary(index) {
        selectedNewImageIndex = index;

        // Uncheck all existing radio buttons
        const allRadios = elements.imagesGrid.querySelectorAll('input[name="primary_image_id"]');
        allRadios.forEach(radio => radio.checked = false);

        // Update visuals for all images
        updateAllImagesVisuals();
    }

    // Update all images visual state (including new images)
    function updateAllImagesVisuals() {
        // First, handle existing images
        const selectedRadio = elements.imagesGrid.querySelector('input[name="primary_image_id"]:checked');
        const selectedValue = selectedRadio ? selectedRadio.value : null;

        const allWrappers = elements.imagesGrid.querySelectorAll('.image-card-wrapper');
        allWrappers.forEach(wrapper => {
            const isNew = wrapper.getAttribute('data-is-new') === 'true';

            if (isNew) {
                // Handle new image
                const newIndex = parseInt(wrapper.getAttribute('data-new-image-index'));
                const card = wrapper.querySelector('[data-new-image-card]');
                const badge = wrapper.querySelector('[data-new-primary-badge]');
                const checkIcon = wrapper.querySelector('[data-new-primary-check]');

                if (!card || !badge || !checkIcon) return;

                const isSelected = (newIndex === selectedNewImageIndex);

                // Update card border, scale, and shadow
                if (isSelected) {
                    card.classList.remove('border-gray-200', 'hover:border-blue-400', 'hover:shadow-md');
                    card.classList.add('border-blue-500', 'ring-2', 'ring-blue-500/20', 'scale-105', 'shadow-lg');
                } else {
                    card.classList.remove('border-blue-500', 'ring-2', 'ring-blue-500/20', 'scale-105', 'shadow-lg');
                    card.classList.add('border-gray-200', 'hover:border-blue-400', 'hover:shadow-md');
                }

                // Update badge and check icon
                if (isSelected) {
                    badge.classList.remove('hidden');
                    checkIcon.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                    checkIcon.classList.add('hidden');
                }
            } else {
                // Handle existing image
                const radio = wrapper.querySelector('input[name="primary_image_id"]');
                const card = wrapper.querySelector('[data-image-card]');
                const badge = wrapper.querySelector('[data-primary-badge]');
                const checkIcon = wrapper.querySelector('[data-primary-check]');

                if (!radio || !card || !badge || !checkIcon) return;

                const isSelected = (radio.value === selectedValue) && (selectedNewImageIndex === null);

                // Update card border, scale, and shadow
                if (isSelected) {
                    card.classList.remove('border-gray-200', 'hover:shadow-md');
                    card.classList.add('border-blue-500', 'ring-2', 'ring-blue-500/20', 'scale-105', 'shadow-lg');
                } else {
                    card.classList.remove('border-blue-500', 'ring-2', 'ring-blue-500/20', 'scale-105', 'shadow-lg');
                    card.classList.add('border-gray-200', 'hover:shadow-md');
                }

                // Update badge and check icon
                if (isSelected) {
                    badge.classList.remove('hidden');
                    checkIcon.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                    checkIcon.classList.add('hidden');
                }
            }
        });
    }

    // Clear new images
    function clearNewImages() {
        newImagesData = [];
        selectedNewImageIndex = null;

        // Remove all new image cards from DOM
        const newImageWrappers = elements.imagesGrid.querySelectorAll('[data-is-new="true"]');
        newImageWrappers.forEach(wrapper => wrapper.remove());

        // Reset file input
        if (elements.imageInput) {
            elements.imageInput.value = '';
        }
    }

    // Handle removal of existing image
    function handleRemoveExisting(imageId) {
        const wrapper = elements.imagesGrid.querySelector(`[data-image-id="${imageId}"]`);
        const removeInput = elements.imagesGrid.querySelector(`.remove-image-hidden-input[data-image-id="${imageId}"]`);
        const radio = wrapper ? wrapper.querySelector('input[name="primary_image_id"]') : null;

        if (!wrapper || !removeInput) return;

        const confirmMessage = translations.confirm_remove_image;

        if (confirm(confirmMessage)) {
            // Mark for removal
            removeInput.value = imageId;

            // Visual feedback
            wrapper.style.opacity = '0.3';
            wrapper.style.pointerEvents = 'none';

            // If this was the primary image, select another one
            if (radio && radio.checked) {
                // Try to select a new image first
                if (newImagesData.length > 0) {
                    selectNewImageAsPrimary(0);
                } else {
                    // Find the first remaining non-removed existing image
                    const allRadios = Array.from(elements.imagesGrid.querySelectorAll('input[name="primary_image_id"]'))
                        .filter(r => {
                            const rWrapper = r.closest('.image-card-wrapper');
                            const rRemoveInput = rWrapper ? rWrapper.querySelector('.remove-image-hidden-input') : null;
                            return rRemoveInput && !rRemoveInput.value;
                        });

                    if (allRadios.length > 0) {
                        allRadios[0].checked = true;
                        selectedNewImageIndex = null;
                        updateAllImagesVisuals();
                    }
                }
            }
        }
    }

    // Handle form submission validation
    function handleFormSubmit(e) {
        // Check if there's at least one image (existing or new)
        const existingCount = Array.from(elements.imagesGrid.querySelectorAll('.image-card-wrapper'))
            .filter(w => {
                const isNew = w.getAttribute('data-is-new') === 'true';
                if (isNew) return true; // New images count
                const removeInput = w.querySelector('.remove-image-hidden-input');
                return removeInput && !removeInput.value;
            }).length;

        if (existingCount === 0) {
            e.preventDefault();
            const requiredMessage = translations.at_least_one_image_required;
            alert(requiredMessage);
            return false;
        }

        // Check if primary image is selected (either existing or new)
        const primaryRadio = elements.imagesGrid.querySelector('input[name="primary_image_id"]:checked');
        const hasPrimarySelection = primaryRadio || (selectedNewImageIndex !== null);

        if (!hasPrimarySelection) {
            e.preventDefault();
            const selectPrimaryMessage = translations.select_primary_image;
            alert(selectPrimaryMessage);
            return false;
        }

        // If a new image is selected as primary, store the info for the server
        if (selectedNewImageIndex !== null) {
            // Create a hidden input to indicate which new image should be primary
            let primaryIndexInput = elements.form.querySelector('input[name="new_image_primary_index"]');
            if (!primaryIndexInput) {
                primaryIndexInput = document.createElement('input');
                primaryIndexInput.type = 'hidden';
                primaryIndexInput.name = 'new_image_primary_index';
                primaryIndexInput.value = selectedNewImageIndex;
                elements.form.appendChild(primaryIndexInput);
            } else {
                primaryIndexInput.value = selectedNewImageIndex;
            }
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 for brand select with images
    $('.brand-select').select2({
        templateResult: function(state) {
            if (!state.id) {
                return state.text;
            }

            const imageUrl = $(state.element).data('image');
            if (imageUrl) {
                return $(
                    '<div class="flex items-center gap-2">' +
                        '<img src="' + imageUrl + '" class="w-8 h-8 object-cover rounded" style="object-fit: contain;" />' +
                        '<span>' + state.text + '</span>' +
                    '</div>'
                );
            }

            return state.text;
        },
        templateSelection: function(state) {
            if (!state.id) {
                return state.text;
            }

            const imageUrl = $(state.element).data('image');
            if (imageUrl) {
                return $(
                    '<div class="flex items-center gap-2">' +
                        '<img src="' + imageUrl + '" class="w-6 h-6 object-cover rounded" style="object-fit: contain;" />' +
                        '<span>' + state.text + '</span>' +
                    '</div>'
                );
            }

            return state.text;
        },
        width: '100%'
    });

    // Initialize Select2 for category select with images
    $('.category-select').select2({
        templateResult: function(state) {
            if (!state.id) {
                return state.text;
            }

            const imageUrl = $(state.element).data('image');
            if (imageUrl) {
                return $(
                    '<div class="flex items-center gap-2">' +
                        '<img src="' + imageUrl + '" class="w-8 h-8 object-cover rounded" style="object-fit: cover;" />' +
                        '<span>' + state.text + '</span>' +
                    '</div>'
                );
            }

            return state.text;
        },
        templateSelection: function(state) {
            if (!state.id) {
                return state.text;
            }

            const imageUrl = $(state.element).data('image');
            if (imageUrl) {
                return $(
                    '<div class="flex items-center gap-2">' +
                        '<img src="' + imageUrl + '" class="w-6 h-6 object-cover rounded" style="object-fit: cover;" />' +
                        '<span>' + state.text + '</span>' +
                    '</div>'
                );
            }

            return state.text;
        },
        width: '100%'
    });
});
</script>
@endpush
@endsection
