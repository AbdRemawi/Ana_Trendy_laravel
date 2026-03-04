@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.edit_order') . ' - ' . $order->order_number;
@endphp

{{-- Back Button --}}
<div class="mb-6">
    <a href="{{ route('admin.orders.show', $order) }}"
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
        {{ __('admin.edit_order') }}: {{ $order->order_number }}
    </h1>
    <p class="mt-1 text-sm text-gray-500">
        {{ __('admin.order_date') }}: {{ $order->created_at->format('Y-m-d H:i') }}
    </p>
</div>

<div class="max-w-3xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="p-6">
            @csrf

            {{-- Customer Name --}}
            <div class="mb-4">
                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('admin.customer_name') }}
                </label>
                <input type="text"
                       name="full_name"
                       id="full_name"
                       value="{{ old('full_name', $order->full_name) }}"
                       required
                       class="w-full px-4 py-2
                              rounded-lg
                              border border-gray-200
                              focus:ring-2 focus:ring-primary/20 focus:border-primary
                              transition-all duration-200
                              text-sm">
            </div>

            {{-- City --}}
            <div class="mb-4">
                <label for="city_id" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('admin.order_city') }}
                </label>
                <select name="city_id"
                        id="city_id"
                        required
                        class="w-full px-4 py-2
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               bg-white">
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ $order->city_id == $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Address --}}
            <div class="mb-4">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('admin.customer_address') }}
                </label>
                <textarea name="address"
                          id="address"
                          rows="3"
                          required
                          class="w-full px-4 py-2
                                 rounded-lg
                                 border border-gray-200
                                 focus:ring-2 focus:ring-primary/20 focus:border-primary
                                 transition-all duration-200
                                 text-sm">{{ old('address', $order->address) }}</textarea>
            </div>

            {{-- Notes --}}
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('admin.order_notes') }}
                </label>
                <textarea name="notes"
                          id="notes"
                          rows="3"
                          class="w-full px-4 py-2
                                 rounded-lg
                                 border border-gray-200
                                 focus:ring-2 focus:ring-primary/20 focus:border-primary
                                 transition-all duration-200
                                 text-sm">{{ old('notes', $order->notes) }}</textarea>
            </div>

            {{-- Read-only Info Notice --}}
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm text-blue-800 font-medium">{{ __('admin.note') }}</p>
                        <p class="text-sm text-blue-600 mt-1">
                            {{ __('admin.order_not_editable_full') }}
                        </p>
                    </div>
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
                <a href="{{ route('admin.orders.show', $order) }}"
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
@endsection
