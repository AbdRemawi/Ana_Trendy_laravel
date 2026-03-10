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
    $pageTitle = __('admin.inventory_management');
@endphp

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('admin.inventory') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.inventory_description') }}
            </p>
        </div>

        @can('manage products')
        <a href="{{ route('admin.inventory.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2
                  bg-primary text-white
                  rounded-lg
                  hover:bg-primary/90
                  transition-colors duration-200
                  font-medium text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('admin.create_transaction') }}
        </a>
        @endcan
    </div>
</div>

{{-- Search & Filter --}}
<div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="{{ route('admin.inventory.index') }}" class="flex flex-col sm:flex-row gap-4">
        {{-- Product Filter --}}
        <div class="flex-1">
            <select name="product"
                    id="product-filter-select"
                    class="w-full px-4 py-2
                           rounded-lg
                           border border-gray-200
                           focus:ring-2 focus:ring-primary/20 focus:border-primary
                           transition-all duration-200
                           text-sm
                           bg-white">
                <option value="">{{ __('admin.select_product') }}</option>
                @foreach($products as $prod)
                    @php
                        $primaryImage = $prod->images->firstWhere('is_primary', true) ?? $prod->images->first();
                        $imageUrl = $primaryImage ? $primaryImage->image_url : null;
                    @endphp
                    <option value="{{ $prod->id }}"
                            {{ request('product') == $prod->id ? 'selected' : '' }}
                            data-image="{{ $imageUrl }}">
                        {{ $prod->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Type Filter --}}
        <div class="sm:w-48">
            <select name="type"
                    class="w-full px-4 py-2
                           rounded-lg
                           border border-gray-200
                           focus:ring-2 focus:ring-primary/20 focus:border-primary
                           transition-all duration-200
                           text-sm
                           bg-white">
                <option value="">{{ __('admin.all_types') }}</option>
                <option value="supply" {{ request('type') == 'supply' ? 'selected' : '' }}>
                    {{ __('admin.type_supply') }}
                </option>
                <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>
                    {{ __('admin.type_sale') }}
                </option>
                <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>
                    {{ __('admin.type_return') }}
                </option>
                <option value="damage" {{ request('type') == 'damage' ? 'selected' : '' }}>
                    {{ __('admin.type_damage') }}
                </option>
                <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>
                    {{ __('admin.type_adjustment') }}
                </option>
            </select>
        </div>

        {{-- Submit Button --}}
        <button type="submit"
                class="px-6 py-2
                       bg-primary text-white
                       rounded-lg
                       hover:bg-primary/90
                       transition-colors duration-200
                       font-medium text-sm
                       whitespace-nowrap">
            {{ __('admin.search') }}
        </button>

        @if(request()->hasAny(['product', 'type']))
        <a href="{{ route('admin.inventory.index') }}"
           class="px-6 py-2
                  border border-gray-200
                  rounded-lg
                  hover:bg-gray-50
                  transition-colors duration-200
                  font-medium text-sm text-gray-700
                  whitespace-nowrap">
            {{ __('admin.clear') }}
        </a>
        @endif
    </form>
</div>

{{-- Transactions Table Card --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.transaction_type') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.product_name') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.transaction_quantity') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.transaction_notes') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.transaction_date') }}
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
                @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        {{-- Type --}}
                        <td class="px-6 py-4">
                            @switch($transaction->type)
                                @case('supply')
                                    <span class="inline-flex items-center gap-1.5
                                                  px-2.5 py-1
                                                  rounded-md
                                                  text-xs font-medium
                                                  bg-green-100 text-green-700">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        {{ __('admin.type_supply') }}
                                    </span>
                                @break

                                @case('sale')
                                    <span class="inline-flex items-center gap-1.5
                                                  px-2.5 py-1
                                                  rounded-md
                                                  text-xs font-medium
                                                  bg-blue-100 text-blue-700">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                        {{ __('admin.type_sale') }}
                                    </span>
                                @break

                                @case('return')
                                    <span class="inline-flex items-center gap-1.5
                                                  px-2.5 py-1
                                                  rounded-md
                                                  text-xs font-medium
                                                  bg-yellow-100 text-yellow-700">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                        </svg>
                                        {{ __('admin.type_return') }}
                                    </span>
                                @break

                                @case('damage')
                                    <span class="inline-flex items-center gap-1.5
                                                  px-2.5 py-1
                                                  rounded-md
                                                  text-xs font-medium
                                                  bg-red-100 text-red-700">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        {{ __('admin.type_damage') }}
                                    </span>
                                @break

                                @case('adjustment')
                                    <span class="inline-flex items-center gap-1.5
                                                  px-2.5 py-1
                                                  rounded-md
                                                  text-xs font-medium
                                                  bg-purple-100 text-purple-700">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                        </svg>
                                        {{ __('admin.type_adjustment') }}
                                    </span>
                                @break
                            @endswitch
                        </td>

                        {{-- Product --}}
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $transaction->product->name ?? '-' }}
                            </div>
                        </td>

                        {{-- Quantity --}}
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold
                                @if(in_array($transaction->type, ['supply', 'return'])) text-green-600
                                @elseif(in_array($transaction->type, ['sale', 'damage'])) text-red-600
                                @else text-gray-900 @endif">
                                @if(in_array($transaction->type, ['sale', 'damage'])) -@endif
                                {{ $transaction->quantity }}
                            </span>
                        </td>

                        {{-- Notes --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 max-w-xs truncate">
                                {{ $transaction->notes ?? '-' }}
                            </div>
                        </td>

                        {{-- Date --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600">
                                {{ $transaction->created_at->format('Y-m-d H:i') }}
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2
                                        {{ $direction === 'rtl' ? 'flex-row-reverse' : '' }}">
                                @can('manage products')
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.inventory.edit', $transaction) }}"
                                       class="p-2 text-gray-400 hover:text-primary
                                              rounded-lg hover:bg-gray-100
                                              transition-colors duration-150"
                                       title="{{ __('admin.edit') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    {{-- Delete --}}
                                    <button type="button"
                                            class="delete-transaction-btn p-2 text-gray-400 hover:text-red-600
                                                   rounded-lg hover:bg-red-50
                                                   transition-colors duration-150"
                                            data-url="{{ route('admin.inventory.destroy', $transaction) }}"
                                            data-modal-confirm="{{ __('admin.confirm_delete_inventory_transaction') }}"
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
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 rounded-full
                                            bg-gray-100
                                            flex items-center justify-center
                                            mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">
                                    {{ __('admin.no_transactions_found') }}
                                </h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    {{ __('admin.no_transactions_description') }}
                                </p>
                                @can('manage products')
                                <a href="{{ route('admin.inventory.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2
                                          bg-primary text-white
                                          rounded-lg
                                          hover:bg-primary/90
                                          transition-colors duration-200
                                          font-medium text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('admin.create_transaction') }}
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
    @if($transactions->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-600">
            {{ __('admin.showing', ['from' => $transactions->firstItem(), 'to' => $transactions->lastItem(), 'total' => $transactions->total()]) }}
        </div>
        {{ $transactions->links('pagination::tailwind') }}
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 for product filter with images
    $('#product-filter-select').select2({
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
