@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.create_coupon');
@endphp

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('admin.create_coupon') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.create_coupon_description') }}
            </p>
        </div>
    </div>
</div>

<div class="max-w-3xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <form method="POST" action="{{ route('admin.coupons.store') }}" class="p-6">
            @csrf

            {{-- Code --}}
            <div class="mb-4">
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('admin.coupon_code') }}
                    <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="code"
                       id="code"
                       value="{{ old('code') }}"
                       required
                       uppercase
                       class="w-full px-4 py-2
                              rounded-lg
                              border border-gray-200
                              focus:ring-2 focus:ring-primary/20 focus:border-primary
                              transition-all duration-200
                              text-sm
                              font-mono"
                       placeholder="SUMMER2024">
                <p class="mt-1 text-xs text-gray-500">
                    {{ __('admin.coupon_code_help') }}
                </p>
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Type --}}
            <div class="mb-4">
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('admin.coupon_type') }}
                    <span class="text-red-500">*</span>
                </label>
                <select name="type"
                        id="type"
                        required
                        class="w-full px-4 py-2
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               bg-white">
                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>
                        {{ __('admin.type_fixed') }}
                    </option>
                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>
                        {{ __('admin.type_percentage') }}
                    </option>
                    <option value="free_delivery" {{ old('type') == 'free_delivery' ? 'selected' : '' }}>
                        {{ __('admin.type_free_delivery') }}
                    </option>
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    {{ __('admin.coupon_type_help') }}
                </p>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Value --}}
            <div class="mb-4" id="value-field">
                <label for="value" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('admin.coupon_value') }}
                    <span class="text-red-500">*</span>
                </label>
                <input type="number"
                       name="value"
                       id="value"
                       value="{{ old('value') }}"
                       required
                       min="0"
                       step="0.01"
                       class="w-full px-4 py-2
                              rounded-lg
                              border border-gray-200
                              focus:ring-2 focus:ring-primary/20 focus:border-primary
                              transition-all duration-200
                              text-sm">
                <p class="mt-1 text-xs text-gray-500">
                    {{ __('admin.coupon_value_help') }}
                </p>
                @error('value')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Minimum Order Amount --}}
            <div class="mb-4">
                <label for="minimum_order_amount" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('admin.coupon_minimum_order') }}
                    <span class="text-red-500">*</span>
                </label>
                <input type="number"
                       name="minimum_order_amount"
                       id="minimum_order_amount"
                       value="{{ old('minimum_order_amount', 0) }}"
                       required
                       min="0"
                       step="0.01"
                       class="w-full px-4 py-2
                              rounded-lg
                              border border-gray-200
                              focus:ring-2 focus:ring-primary/20 focus:border-primary
                              transition-all duration-200
                              text-sm">
                <p class="mt-1 text-xs text-gray-500">
                    {{ __('admin.coupon_minimum_order_help') }}
                </p>
                @error('minimum_order_amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Max Uses --}}
            <div class="mb-4">
                <label for="max_uses" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('admin.coupon_max_uses') }}
                </label>
                <input type="number"
                       name="max_uses"
                       id="max_uses"
                       value="{{ old('max_uses') }}"
                       min="1"
                       class="w-full px-4 py-2
                              rounded-lg
                              border border-gray-200
                              focus:ring-2 focus:ring-primary/20 focus:border-primary
                              transition-all duration-200
                              text-sm">
                <p class="mt-1 text-xs text-gray-500">
                    {{ __('admin.coupon_max_uses_help') }}
                </p>
                @error('max_uses')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Valid From --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="valid_from" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.coupon_valid_from') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local"
                           name="valid_from"
                           id="valid_from"
                           required
                           class="w-full px-4 py-2
                                  rounded-lg
                                  border border-gray-200
                                  focus:ring-2 focus:ring-primary/20 focus:border-primary
                                  transition-all duration-200
                                  text-sm">
                    <p class="mt-1 text-xs text-gray-500">
                        {{ __('admin.coupon_valid_from_help') }}
                    </p>
                    @error('valid_from')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.coupon_valid_until') }}
                    </label>
                    <input type="datetime-local"
                           name="valid_until"
                           id="valid_until"
                           class="w-full px-4 py-2
                                  rounded-lg
                                  border border-gray-200
                                  focus:ring-2 focus:ring-primary/20 focus:border-primary
                                  transition-all duration-200
                                  text-sm">
                    <p class="mt-1 text-xs text-gray-500">
                        {{ __('admin.coupon_valid_until_help') }}
                    </p>
                    @error('valid_until')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Is Active --}}
            <div class="mb-6">
                <div class="flex items-center gap-3">
                    <input type="checkbox"
                           name="is_active"
                           id="is_active"
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4
                                  rounded
                                  border-gray-300
                                  text-primary
                                  focus:ring-2 focus:ring-primary/20">
                    <label for="is_active" class="text-sm font-medium text-gray-700">
                        {{ __('admin.status_active') }}
                    </label>
                </div>
                <p class="mt-1 text-xs text-gray-500 ml-7">
                    {{ __('admin.is_active_help') }}
                </p>
            </div>

            {{-- Buttons --}}
            <div class="flex items-center gap-3">
                <button type="submit"
                        class="px-6 py-2.5
                               bg-primary text-white
                               rounded-lg
                               hover:bg-primary/90
                               transition-colors duration-200
                               font-medium text-sm">
                    {{ __('admin.save') }}
                </button>
                <a href="{{ route('admin.coupons.index') }}"
                   class="px-6 py-2.5
                          border border-gray-200
                          text-gray-700
                          rounded-lg
                          hover:bg-gray-50
                          transition-colors duration-200
                          font-medium text-sm">
                    {{ __('admin.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const valueField = document.getElementById('value-field');
    const valueInput = document.getElementById('value');

    function toggleValueField() {
        if (typeSelect.value === 'free_delivery') {
            valueField.style.display = 'none';
            valueInput.removeAttribute('required');
        } else {
            valueField.style.display = 'block';
            valueInput.setAttribute('required', 'required');
        }
    }

    typeSelect.addEventListener('change', toggleValueField);
    toggleValueField();
});
</script>
@endsection
