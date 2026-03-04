@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.edit_coupon') . ' - ' . $coupon->code;
@endphp

{{-- Back Button --}}
<div class="mb-6">
    <a href="{{ route('admin.coupons.show', $coupon) }}"
       class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900
              transition-colors duration-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        {{ __('admin.back') }}
    </a>
</div>

{{-- Page Header --}}
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">
        {{ __('admin.edit_coupon') }}: {{ $coupon->code }}
    </h1>
</div>

<div class="max-w-3xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}" class="p-6">
            @csrf
            @method('PUT')

            {{-- Usage Warning if coupon has been used --}}
            @if($coupon->used_count > 0)
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-sm text-yellow-800 font-medium">
                            {{ __('admin.important_note') }}
                        </p>
                        <p class="text-sm text-yellow-700 mt-1">
                            {{ __('admin.coupon_already_used', ['count' => $coupon->used_count]) }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Code --}}
            <div class="mb-4">
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('admin.coupon_code') }}
                    <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="code"
                       id="code"
                       value="{{ old('code', $coupon->code) }}"
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
                    <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>
                        {{ __('admin.type_fixed') }}
                    </option>
                    <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>
                        {{ __('admin.type_percentage') }}
                    </option>
                    <option value="free_delivery" {{ old('type', $coupon->type) == 'free_delivery' ? 'selected' : '' }}>
                        {{ __('admin.type_free_delivery') }}
                    </option>
                </select>
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
                       value="{{ old('value', $coupon->value) }}"
                       required
                       min="0"
                       step="0.01"
                       class="w-full px-4 py-2
                              rounded-lg
                              border border-gray-200
                              focus:ring-2 focus:ring-primary/20 focus:border-primary
                              transition-all duration-200
                              text-sm">
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
                       value="{{ old('minimum_order_amount', $coupon->minimum_order_amount) }}"
                       required
                       min="0"
                       step="0.01"
                       class="w-full px-4 py-2
                              rounded-lg
                              border border-gray-200
                              focus:ring-2 focus:ring-primary/20 focus:border-primary
                              transition-all duration-200
                              text-sm">
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
                       value="{{ old('max_uses', $coupon->max_uses) }}"
                       min="1"
                       class="w-full px-4 py-2
                              rounded-lg
                              border border-gray-200
                              focus:ring-2 focus:ring-primary/20 focus:border-primary
                              transition-all duration-200
                              text-sm">
                @error('max_uses')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Valid From/Until --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="valid_from" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.coupon_valid_from') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local"
                           name="valid_from"
                           id="valid_from"
                           value="{{ old('valid_from', $coupon->valid_from->format('Y-m-d\TH:i')) }}"
                           required
                           class="w-full px-4 py-2
                                  rounded-lg
                                  border border-gray-200
                                  focus:ring-2 focus:ring-primary/20 focus:border-primary
                                  transition-all duration-200
                                  text-sm">
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
                           value="{{ old('valid_until', $coupon->valid_until ? $coupon->valid_until->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-4 py-2
                                  rounded-lg
                                  border border-gray-200
                                  focus:ring-2 focus:ring-primary/20 focus:border-primary
                                  transition-all duration-200
                                  text-sm">
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
                           {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}
                           class="w-4 h-4
                                  rounded
                                  border-gray-300
                                  text-primary
                                  focus:ring-2 focus:ring-primary/20">
                    <label for="is_active" class="text-sm font-medium text-gray-700">
                        {{ __('admin.status_active') }}
                    </label>
                </div>
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
                <a href="{{ route('admin.coupons.show', $coupon) }}"
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
