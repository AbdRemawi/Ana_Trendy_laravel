@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.create_transaction');
@endphp

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.inventory.index') }}"
           class="p-2 text-gray-400 hover:text-gray-600
                  rounded-lg hover:bg-gray-100
                  transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('admin.create_transaction') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.create_transaction_description') }}
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Column: Transaction Form --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('admin.inventory.store') }}" class="space-y-6">
                @csrf

                {{-- Product --}}
                <div>
                    <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.product_name') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="product_id"
                        name="product_id"
                        class="w-full px-4 py-2.5
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               bg-white
                               {{ $errors->has('product_id') ? 'border-red-300' : '' }}"
                        @if($errors->has('product_id')) aria-invalid="true" aria-describedby="product_id-error" @endif
                    >
                        <option value="">{{ __('admin.select_product') ?? 'Select Product' }}</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>
                                {{ $prod->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p id="product_id-error" class="mt-1.5 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Transaction Type --}}
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.transaction_type') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="type"
                        name="type"
                        class="w-full px-4 py-2.5
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               bg-white
                               {{ $errors->has('type') ? 'border-red-300' : '' }}"
                        @if($errors->has('type')) aria-invalid="true" aria-describedby="type-error" @endif
                    >
                        <option value="">{{ __('admin.select_type') ?? 'Select Type' }}</option>
                        <option value="supply" {{ old('type') == 'supply' ? 'selected' : '' }}>
                            {{ __('admin.type_supply') }}
                        </option>
                        <option value="sale" {{ old('type') == 'sale' ? 'selected' : '' }}>
                            {{ __('admin.type_sale') }}
                        </option>
                        <option value="return" {{ old('type') == 'return' ? 'selected' : '' }}>
                            {{ __('admin.type_return') }}
                        </option>
                        <option value="damage" {{ old('type') == 'damage' ? 'selected' : '' }}>
                            {{ __('admin.type_damage') }}
                        </option>
                        <option value="adjustment" {{ old('type') == 'adjustment' ? 'selected' : '' }}>
                            {{ __('admin.type_adjustment') }}
                        </option>
                    </select>
                    <p class="mt-1.5 text-xs text-gray-500">
                        {{ __('admin.transaction_type_help') }}
                    </p>
                    @error('type')
                        <p id="type-error" class="mt-1.5 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Quantity --}}
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.transaction_quantity') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        id="quantity"
                        name="quantity"
                        value="{{ old('quantity') }}"
                        min="1"
                        placeholder="0"
                        class="w-full px-4 py-2.5
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               {{ $errors->has('quantity') ? 'border-red-300' : '' }}"
                        @if($errors->has('quantity')) aria-invalid="true" aria-describedby="quantity-error" @endif
                    >
                    <p class="mt-1.5 text-xs text-gray-500">
                        {{ __('admin.transaction_quantity_help') }}
                    </p>
                    @error('quantity')
                        <p id="quantity-error" class="mt-1.5 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.transaction_notes') }}
                        <span class="text-gray-400 text-xs">{{ __('admin.optional') ?? '(Optional)' }}</span>
                    </label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="3"
                        placeholder="{{ __('admin.transaction_notes_help') }}"
                        class="w-full px-4 py-2.5
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               resize-none
                               {{ $errors->has('notes') ? 'border-red-300' : '' }}"
                    >{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1.5 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
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
                        {{ __('admin.create_transaction') }}
                    </button>
                    <a href="{{ route('admin.inventory.index') }}"
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

    {{-- Right Column: Transaction Types Info --}}
    <div class="lg:col-span-1 space-y-6">
        {{-- Transaction Types Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">
                {{ __('admin.transaction_types') ?? 'Transaction Types' }}
            </h3>

            <div class="space-y-3">
                <div class="flex items-start gap-3 p-3 bg-green-50 rounded-lg">
                    <span class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-green-900">
                            {{ __('admin.type_supply') }}
                        </p>
                        <p class="text-xs text-green-700">
                            {{ __('admin.supply_desc') ?? 'Add stock to inventory' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg">
                    <span class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-blue-900">
                            {{ __('admin.type_sale') }}
                        </p>
                        <p class="text-xs text-blue-700">
                            {{ __('admin.sale_desc') ?? 'Record a sale (removes stock)' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 bg-yellow-50 rounded-lg">
                    <span class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-yellow-900">
                            {{ __('admin.type_return') }}
                        </p>
                        <p class="text-xs text-yellow-700">
                            {{ __('admin.return_desc') ?? 'Customer return (adds stock)' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 bg-red-50 rounded-lg">
                    <span class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-red-900">
                            {{ __('admin.type_damage') }}
                        </p>
                        <p class="text-xs text-red-700">
                            {{ __('admin.damage_desc') ?? 'Damaged goods (removes stock)' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 bg-purple-50 rounded-lg">
                    <span class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-purple-900">
                            {{ __('admin.type_adjustment') }}
                        </p>
                        <p class="text-xs text-purple-700">
                            {{ __('admin.adjustment_desc') ?? 'Manual stock correction' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
