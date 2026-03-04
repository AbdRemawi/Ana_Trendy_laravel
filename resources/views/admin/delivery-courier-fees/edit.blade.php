@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.edit_fee');
@endphp

<div class="max-w-2xl mx-auto">
    {{-- Page Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-900">
            {{ __('admin.edit_fee') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('admin.edit_fee_description') }}
        </p>
    </div>

    {{-- Important Note --}}
    <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-2 0 1 1 0 012 0z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    {{ __('admin.important_note') }}
                </h3>
                <div class="mt-1 text-sm text-yellow-700">
                    <p>{{ __('admin.fee_unique_constraint_note') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <form method="POST" action="{{ route('admin.delivery-courier-fees.update', $fee) }}" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Delivery Courier --}}
                <div class="md:col-span-2">
                    <label for="delivery_courier_id" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.courier') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <select id="delivery_courier_id"
                            name="delivery_courier_id"
                            required
                            class="w-full px-4 py-2
                                   rounded-lg
                                   border border-gray-200
                                   focus:ring-2 focus:ring-primary/20 focus:border-primary
                                   transition-all duration-200
                                   text-sm
                                   bg-white">
                        <option value="">{{ __('admin.select_courier') }}</option>
                        @foreach($couriers as $courier)
                            <option value="{{ $courier->id }}"
                                    {{ old('delivery_courier_id', $fee->delivery_courier_id) == $courier->id ? 'selected' : '' }}>
                                {{ $courier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('delivery_courier_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- City --}}
                <div class="md:col-span-2">
                    <label for="city_id" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.city') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <select id="city_id"
                            name="city_id"
                            required
                            class="w-full px-4 py-2
                                   rounded-lg
                                   border border-gray-200
                                   focus:ring-2 focus:ring-primary/20 focus:border-primary
                                   transition-all duration-200
                                   text-sm
                                   bg-white">
                        <option value="">{{ __('admin.select_city') }}</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}"
                                    {{ old('city_id', $fee->city_id) == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('city_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Real Fee Amount --}}
                <div>
                    <label for="real_fee_amount" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.real_fee_amount') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           id="real_fee_amount"
                           name="real_fee_amount"
                           value="{{ old('real_fee_amount', number_format($fee->real_fee_amount, 3, '.', '')) }}"
                           step="0.001"
                           min="0"
                           placeholder="0.000"
                           required
                           class="w-full px-4 py-2
                                  rounded-lg
                                  border border-gray-200
                                  focus:ring-2 focus:ring-primary/20 focus:border-primary
                                  transition-all duration-200
                                  text-sm font-mono">
                    @error('real_fee_amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Display Fee Amount --}}
                <div>
                    <label for="display_fee_amount" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.display_fee_amount') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           id="display_fee_amount"
                           name="display_fee_amount"
                           value="{{ old('display_fee_amount', number_format($fee->display_fee_amount, 3, '.', '')) }}"
                           step="0.001"
                           min="0"
                           placeholder="0.000"
                           required
                           class="w-full px-4 py-2
                                  rounded-lg
                                  border border-gray-200
                                  focus:ring-2 focus:ring-primary/20 focus:border-primary
                                  transition-all duration-200
                                  text-sm font-mono">
                    @error('display_fee_amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Currency --}}
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.currency') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="currency"
                           name="currency"
                           value="{{ old('currency', $fee->currency) }}"
                           placeholder="JOD"
                           maxlength="3"
                           required
                           class="w-full px-4 py-2
                                  rounded-lg
                                  border border-gray-200
                                  focus:ring-2 focus:ring-primary/20 focus:border-primary
                                  transition-all duration-200
                                  text-sm uppercase">
                    @error('currency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('admin.fee_status') }}
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
                        <option value="1" {{ $fee->is_active ? 'selected' : '' }}>
                            {{ __('admin.status_active') }}
                        </option>
                        <option value="0" {{ !$fee->is_active ? 'selected' : '' }}>
                            {{ __('admin.status_inactive') }}
                        </option>
                    </select>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-gray-100">
                <a href="{{ route('admin.delivery-courier-fees.index') }}"
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
