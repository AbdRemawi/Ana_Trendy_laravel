@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.product_inventory');
@endphp

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.products.show', $product) }}"
           class="p-2 text-gray-400 hover:text-gray-600
                  rounded-lg hover:bg-gray-100
                  transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('admin.product_inventory') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $product->name }}
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Column: Stock Summary & Transactions --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Stock Summary Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">
                {{ __('admin.current_stock') }}
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <dt class="text-xs font-medium text-gray-500 uppercase">
                        {{ __('admin.current_stock') }}
                    </dt>
                    <dd class="mt-2 text-3xl font-semibold {{ $product->stock_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $product->stock_quantity }}
                    </dd>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <dt class="text-xs font-medium text-gray-500 uppercase">
                        {{ __('admin.total_transactions') ?? 'Total Transactions' }}
                    </dt>
                    <dd class="mt-2 text-3xl font-semibold text-gray-900">
                        {{ $transactions->count() }}
                    </dd>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <dt class="text-xs font-medium text-gray-500 uppercase">
                        {{ __('admin.status') }}
                    </dt>
                    <dd class="mt-2 text-lg font-semibold {{ $product->stock_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $product->stock_quantity > 0 ? __('admin.status_active') : __('admin.out_of_stock') ?? 'Out of Stock' }}
                    </dd>
                </div>
            </div>
        </div>

        {{-- Transactions History --}}
        @can('manage products')
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">
                {{ __('admin.transaction_history') ?? 'Transaction History' }}
            </h2>
            <a href="{{ route('admin.inventory.create') }}?product_id={{ $product->id }}"
               class="text-sm text-primary hover:text-primary/80 font-medium">
                {{ __('admin.create_transaction') }}
            </a>
        </div>
        @endcan

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
                                    <div class="text-sm text-gray-600">
                                        {{ $transaction->notes ?? '-' }}
                                    </div>
                                </td>

                                {{-- Date --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">
                                        {{ $transaction->created_at->format('Y-m-d H:i') }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <p class="text-sm text-gray-500">
                                        {{ __('admin.no_transactions_found') }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $transactions->links('pagination::tailwind') }}
            </div>
            @endif
        </div>
    </div>

    {{-- Right Column: Quick Actions --}}
    <div class="lg:col-span-1 space-y-6">
        {{-- Add Transaction Card --}}
        @can('manage products')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">
                {{ __('admin.add_transaction') ?? 'Add Transaction' }}
            </h3>
            <p class="text-sm text-gray-600 mb-4">
                {{ __('admin.create_transaction_description') }}
            </p>
            <a href="{{ route('admin.inventory.create') }}?product_id={{ $product->id }}"
               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5
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
        </div>
        @endcan

        {{-- Transaction Types Reference --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">
                {{ __('admin.transaction_types') ?? 'Transaction Types' }}
            </h3>

            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ __('admin.type_supply') }}</p>
                        <p class="text-xs text-gray-500">+ Stock</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ __('admin.type_sale') }}</p>
                        <p class="text-xs text-gray-500">- Stock</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ __('admin.type_return') }}</p>
                        <p class="text-xs text-gray-500">+ Stock</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ __('admin.type_damage') }}</p>
                        <p class="text-xs text-gray-500">- Stock</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ __('admin.type_adjustment') }}</p>
                        <p class="text-xs text-gray-500">+/- Stock</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
