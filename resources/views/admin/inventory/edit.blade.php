@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.edit_transaction');
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
                {{ __('admin.edit_transaction') }}
            </h1>
        </div>
    </div>
</div>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.inventory.update', $inventory) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Transaction Info (Read-only) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 p-4 bg-gray-50 rounded-lg">
                <div>
                    <span class="text-xs font-medium text-gray-500 uppercase">{{ __('admin.product_name') }}</span>
                    <p class="mt-1 text-sm font-medium text-gray-900">
                        {{ $inventory->product->name }}
                    </p>
                </div>
                <div>
                    <span class="text-xs font-medium text-gray-500 uppercase">{{ __('admin.transaction_type') }}</span>
                    <p class="mt-1 text-sm font-medium text-gray-900">
                        {{ __('admin.type_' . $inventory->type) }}
                    </p>
                </div>
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
                    value="{{ old('quantity', $inventory->quantity) }}"
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
                >{{ old('notes', $inventory->notes) }}</textarea>
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
                    {{ __('admin.save') }}
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
@endsection
