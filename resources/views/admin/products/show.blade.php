@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = $product->name;
@endphp

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
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
                    {{ $product->name }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $product->slug }}
                </p>
            </div>
        </div>

        @can('manage products')
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.edit', $product) }}"
               class="inline-flex items-center gap-2 px-4 py-2
                      bg-primary text-white
                      rounded-lg
                      hover:bg-primary/90
                      transition-colors duration-200
                      font-medium text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                {{ __('admin.edit_product') }}
            </a>
        </div>
        @endcan
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Column: Product Details --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Product Images Gallery --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ __('admin.product_images') }}
                </h2>
                @can('manage products')
                <a href="{{ route('admin.products.edit', $product) }}"
                   class="text-sm text-primary hover:text-primary/80
                          font-medium">
                    {{ __('admin.manage_images') }}
                </a>
                @endcan
            </div>

            @if($product->images && $product->images->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($product->images->sortBy('sort_order') as $image)
                        <div class="relative group">
                            <img src="{{ $image->image_url }}"
                                 alt="{{ $product->name }}"
                                 class="w-full aspect-square object-cover rounded-lg
                                        border border-gray-200">
                            @if($image->is_primary)
                                <span class="absolute top-2 {{ $direction === 'rtl' ? 'left-2' : 'right-2' }}
                                               inline-flex items-center px-2 py-1
                                               rounded-full text-xs font-medium
                                               bg-primary text-white">
                                    {{ __('admin.is_primary') }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 bg-gray-50 rounded-lg">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-gray-500">
                        {{ __('admin.no_images_found') }}
                    </p>
                </div>
            @endif
        </div>

        {{-- Product Information Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">
                {{ __('admin.product_details') }}
            </h2>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Brand --}}
                <div>
                    <dt class="text-sm font-medium text-gray-500">
                        {{ __('admin.product_brand') }}
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $product->brand->name ?? '-' }}
                    </dd>
                </div>

                {{-- Category --}}
                <div>
                    <dt class="text-sm font-medium text-gray-500">
                        {{ __('admin.product_category') }}
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $product->category->name ?? '-' }}
                    </dd>
                </div>

                {{-- Size --}}
                <div>
                    <dt class="text-sm font-medium text-gray-500">
                        {{ __('admin.product_size') }}
                    </dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center
                                      px-2.5 py-1
                                      rounded-md
                                      text-xs font-medium
                                      bg-blue-50 text-blue-700">
                            {{ $product->size }}
                        </span>
                    </dd>
                </div>

                {{-- Gender --}}
                <div>
                    <dt class="text-sm font-medium text-gray-500">
                        {{ __('admin.product_gender') }}
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
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
                    </dd>
                </div>

                {{-- Status --}}
                <div>
                    <dt class="text-sm font-medium text-gray-500">
                        {{ __('admin.product_status') }}
                    </dt>
                    <dd class="mt-1">
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
                    </dd>
                </div>

                {{-- Created At --}}
                <div>
                    <dt class="text-sm font-medium text-gray-500">
                        {{ __('admin.created_at') }}
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $product->created_at->format('Y-m-d H:i') }}
                    </dd>
                </div>

                {{-- Description --}}
                @if($product->description)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">
                        {{ __('admin.product_description') }}
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">
                        {{ $product->description }}
                    </dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Inventory Transactions History --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ __('admin.inventory') }}
                </h2>
                <a href="{{ route('admin.inventory.by-product', $product->id) }}"
                   class="text-sm text-primary hover:text-primary/80 font-medium">
                    {{ __('admin.view_inventory') }} →
                </a>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <dt class="text-xs font-medium text-gray-500 uppercase">
                        {{ __('admin.current_stock') }}
                    </dt>
                    <dd class="mt-2 text-2xl font-semibold {{ $stockQuantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $stockQuantity }}
                    </dd>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <dt class="text-xs font-medium text-gray-500 uppercase">
                        {{ __('admin.transaction_type') }}
                    </dt>
                    <dd class="mt-2 text-sm text-gray-900">
                        {{ $product->inventoryTransactions->count() }} {{ __('admin.transactions') ?? 'transactions' }}
                    </dd>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <dt class="text-xs font-medium text-gray-500 uppercase">
                        {{ __('admin.product_status') }}
                    </dt>
                    <dd class="mt-2 text-sm text-gray-900">
                        {{ $stockQuantity > 0 ? __('admin.status_active') : __('admin.status_inactive') }}
                    </dd>
                </div>
            </div>

            {{-- Recent Transactions --}}
            @if($product->inventoryTransactions->isNotEmpty())
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-700 mb-3">
                    {{ __('admin.recent_transactions') ?? 'Recent Transactions' }}
                </h3>
                <div class="space-y-2">
                    @foreach($product->inventoryTransactions->take(5) as $transaction)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                @switch($transaction->type)
                                    @case('supply')
                                        <span class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </span>
                                    @break
                                    @case('sale')
                                        <span class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                            </svg>
                                        </span>
                                    @break
                                    @case('return')
                                        <span class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                            </svg>
                                        </span>
                                    @break
                                    @case('damage')
                                        <span class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        </span>
                                    @break
                                    @case('adjustment')
                                        <span class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                            </svg>
                                        </span>
                                    @break
                                @endswitch
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ __('admin.type_' . $transaction->type) }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $transaction->created_at->format('Y-m-d H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="{{ $direction === 'rtl' ? 'text-left' : 'text-right' }}">
                                <p class="text-sm font-semibold
                                   @if(in_array($transaction->type, ['supply', 'return'])) text-green-600
                                   @elseif(in_array($transaction->type, ['sale', 'damage'])) text-red-600
                                   @else text-gray-600 @endif">
                                    @if(in_array($transaction->type, ['sale', 'damage'])) -@endif
                                    {{ $transaction->quantity }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Right Column: Pricing & Actions --}}
    <div class="lg:col-span-1 space-y-6">
        {{-- Pricing Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">
                {{ __('admin.prices') }}
            </h2>

            <div class="space-y-4">
                {{-- Cost Price --}}
                <div>
                    <dt class="text-sm font-medium text-gray-500">
                        {{ __('admin.product_cost_price') }}
                    </dt>
                    <dd class="mt-1 text-2xl font-semibold text-gray-900">
                        {{ number_format($product->cost_price, 2) }}
                        <span class="text-sm font-normal text-gray-500">
                            @if($locale === 'ar') د.إ @else $ @endif
                        </span>
                    </dd>
                </div>

                {{-- Sale Price --}}
                <div>
                    <dt class="text-sm font-medium text-gray-500">
                        {{ __('admin.product_sale_price') }}
                    </dt>
                    <dd class="mt-1 text-2xl font-semibold text-gray-900">
                        {{ number_format($product->sale_price, 2) }}
                        <span class="text-sm font-normal text-gray-500">
                            @if($locale === 'ar') د.إ @else $ @endif
                        </span>
                    </dd>
                </div>

                {{-- Offer Price --}}
                @if($product->offer_price)
                <div class="p-4 bg-green-50 rounded-lg border border-green-100">
                    <dt class="text-sm font-medium text-green-700">
                        {{ __('admin.product_offer_price') }}
                    </dt>
                    <dd class="mt-1 text-2xl font-semibold text-green-600">
                        {{ number_format($product->offer_price, 2) }}
                        <span class="text-sm font-normal text-green-700">
                            @if($locale === 'ar') د.إ @else $ @endif
                        </span>
                    </dd>
                    <p class="mt-1 text-xs text-green-600">
                        {{ __('admin.has_offer') }}
                        ({{ round((1 - $product->offer_price / $product->sale_price) * 100) }}% {{ __('admin.off') ?? 'off' }})
                    </p>
                </div>
                @endif

                {{-- Profit Margin --}}
                @if($product->cost_price > 0)
                <div class="pt-4 border-t border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">
                        {{ __('admin.profit_margin') }}
                    </dt>
                    <dd class="mt-1 text-xl font-semibold {{ $product->profit_margin > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $product->profit_margin }}%
                    </dd>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        @can('manage products')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                {{ __('admin.actions') }}
            </h2>
            <div class="space-y-3">
                <a href="{{ route('admin.products.edit', $product) }}"
                   class="w-full flex items-center justify-center gap-2 px-4 py-2.5
                          bg-blue-50 text-blue-700
                          rounded-lg
                          hover:bg-blue-100
                          transition-colors duration-200
                          font-medium text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ __('admin.manage_images') }}
                </a>

                <a href="{{ route('admin.inventory.by-product', $product->id) }}"
                   class="w-full flex items-center justify-center gap-2 px-4 py-2.5
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
        </div>
        @endcan
    </div>
</div>
@endsection
