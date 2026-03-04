@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.products_management');
@endphp

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('admin.products') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.products_description') }}
            </p>
        </div>

        @can('manage products')
        <a href="{{ route('admin.products.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2
                  bg-primary text-white
                  rounded-lg
                  hover:bg-primary/90
                  transition-colors duration-200
                  font-medium text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('admin.create_product') }}
        </a>
        @endcan
    </div>
</div>

{{-- Search & Filter --}}
<div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="{{ route('admin.products.index') }}" class="space-y-4">
        {{-- First Row: Search + Brand + Category --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Search --}}
            <div class="lg:col-span-1">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="{{ __('admin.search_products') }}"
                       class="w-full px-4 py-2
                              rounded-lg
                              border border-gray-200
                              focus:ring-2 focus:ring-primary/20 focus:border-primary
                              transition-all duration-200
                              text-sm">
            </div>

            {{-- Brand Filter --}}
            <div>
                <select name="brand"
                        class="w-full px-4 py-2
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               bg-white">
                    <option value="">{{ __('admin.all_brands') }}</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Category Filter --}}
            <div>
                <select name="category"
                        class="w-full px-4 py-2
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               bg-white">
                    <option value="">{{ __('admin.all_categories') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Submit Button --}}
            <div>
                <button type="submit"
                        class="w-full px-6 py-2
                               bg-primary text-white
                               rounded-lg
                               hover:bg-primary/90
                               transition-colors duration-200
                               font-medium text-sm">
                    {{ __('admin.search') }}
                </button>
            </div>
        </div>

        {{-- Second Row: Size + Gender + Status + Clear --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Size Filter --}}
            <div>
                <select name="size"
                        class="w-full px-4 py-2
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               bg-white">
                    <option value="">{{ __('admin.all_sizes') }}</option>
                    @foreach(['S', 'M', 'L', 'XL', 'XXL'] as $size)
                        <option value="{{ $size }}" {{ request('size') == $size ? 'selected' : '' }}>
                            {{ $size }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Gender Filter --}}
            <div>
                <select name="gender"
                        class="w-full px-4 py-2
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               bg-white">
                    <option value="">{{ __('admin.all_genders') }}</option>
                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>
                        {{ __('admin.gender_male') }}
                    </option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>
                        {{ __('admin.gender_female') }}
                    </option>
                    <option value="unisex" {{ request('gender') == 'unisex' ? 'selected' : '' }}>
                        {{ __('admin.gender_unisex') }}
                    </option>
                </select>
            </div>

            {{-- Status Filter --}}
            <div>
                <select name="status"
                        class="w-full px-4 py-2
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               bg-white">
                    <option value="">{{ __('admin.all_statuses') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                        {{ __('admin.status_active') }}
                    </option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                        {{ __('admin.status_inactive') }}
                    </option>
                </select>
            </div>

            {{-- Clear Button --}}
            @if(request()->hasAny(['search', 'brand', 'category', 'size', 'gender', 'status']))
            <div>
                <a href="{{ route('admin.products.index') }}"
                   class="w-full px-6 py-2
                          inline-flex items-center justify-center
                          border border-gray-200
                          rounded-lg
                          hover:bg-gray-50
                          transition-colors duration-200
                          font-medium text-sm text-gray-700">
                    {{ __('admin.clear') }}
                </a>
            </div>
            @else
            <div></div>
            @endif
        </div>
    </form>
</div>

{{-- Products Table Card --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.product_name') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.product_brand') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.product_category') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.product_size') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.product_gender') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.prices') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.product_status') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.actions') }}
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        {{-- Name with Primary Image --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($product->images && $product->images->isNotEmpty())
                                    @php $primaryImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first(); @endphp
                                    <img src="{{ $primaryImage->image_url }}"
                                         alt="{{ $product->name }}"
                                         class="w-12 h-12 object-cover rounded-lg
                                                border border-gray-200
                                                bg-white">
                                @else
                                    <div class="w-12 h-12 rounded-lg
                                                bg-gray-100
                                                flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-gray-900 truncate">
                                        {{ $product->name }}
                                    </div>
                                    <div class="text-xs text-gray-500 font-mono truncate">
                                        {{ $product->slug }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Brand --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $product->brand->name ?? '-' }}
                            </div>
                        </td>

                        {{-- Category --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $product->category->name ?? '-' }}
                            </div>
                        </td>

                        {{-- Size --}}
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center
                                          px-2.5 py-1
                                          rounded-md
                                          text-xs font-medium
                                          bg-blue-50 text-blue-700">
                                {{ $product->size }}
                            </span>
                        </td>

                        {{-- Gender --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                @switch($product->gender)
                                    @case('male')
                                        {{ __('admin.gender_male') }}
                                    @break
                                    @case('female')
                                        {{ __('admin.gender_female') }}
                                    @break
                                    @case('unisex')
                                        {{ __('admin.gender_unisex') }}
                                    @break
                                @endswitch
                            </div>
                        </td>

                        {{-- Prices --}}
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                @if($product->offer_price)
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-green-600">
                                            {{ number_format($product->offer_price, 2) }}
                                        </span>
                                        <span class="text-xs text-gray-400 line-through">
                                            {{ number_format($product->sale_price, 2) }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ number_format($product->sale_price, 2) }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4">
                            @if($product->status === 'active')
                                <span class="inline-flex items-center
                                              px-2.5 py-1
                                              rounded-full
                                              text-xs font-medium
                                              bg-green-100 text-green-700">
                                    {{ __('admin.status_active') }}
                                </span>
                            @else
                                <span class="inline-flex items-center
                                              px-2.5 py-1
                                              rounded-full
                                              text-xs font-medium
                                              bg-gray-100 text-gray-700">
                                    {{ __('admin.status_inactive') }}
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2
                                        {{ $direction === 'rtl' ? 'flex-row-reverse' : '' }}">
                                @can('view products')
                                    {{-- View --}}
                                    <a href="{{ route('admin.products.show', $product) }}"
                                       class="p-2 text-gray-400 hover:text-primary
                                              rounded-lg hover:bg-gray-100
                                              transition-colors duration-150"
                                       title="{{ __('admin.view') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                @endcan

                                @can('manage products')
                                    {{-- Inventory --}}
                                    <a href="{{ route('admin.inventory.by-product', $product->id) }}"
                                       class="p-2 text-gray-400 hover:text-green-600
                                              rounded-lg hover:bg-gray-100
                                              transition-colors duration-150"
                                       title="{{ __('admin.view_inventory') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="p-2 text-gray-400 hover:text-primary
                                              rounded-lg hover:bg-gray-100
                                              transition-colors duration-150"
                                       title="{{ __('admin.edit') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                @endcan

                                @can('delete products')
                                    {{-- Delete --}}
                                    <button type="button"
                                            class="delete-product-btn p-2 text-gray-400 hover:text-red-600
                                                   rounded-lg hover:bg-red-50
                                                   transition-colors duration-150"
                                            data-url="{{ route('admin.products.destroy', $product) }}"
                                            data-item-name="{{ $product->name }}"
                                            data-modal-confirm="{{ __('admin.confirm_delete_product') }}"
                                            title="{{ __('admin.delete') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 rounded-full
                                            bg-gray-100
                                            flex items-center justify-center
                                            mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">
                                    {{ __('admin.no_products_found') }}
                                </h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    {{ __('admin.no_products_description') }}
                                </p>
                                @can('manage products')
                                <a href="{{ route('admin.products.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2
                                          bg-primary text-white
                                          rounded-lg
                                          hover:bg-primary/90
                                          transition-colors duration-200
                                          font-medium text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('admin.create_first_product') }}
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-600">
            {{ __('admin.showing', ['from' => $products->firstItem(), 'to' => $products->lastItem(), 'total' => $products->total()]) }}
        </div>
        {{ $products->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
