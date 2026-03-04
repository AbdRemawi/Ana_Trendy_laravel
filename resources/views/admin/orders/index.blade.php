@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.orders_management');
@endphp

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('admin.orders') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.orders_description') }}
            </p>
        </div>
    </div>
</div>

{{-- Search & Filter --}}
<div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            {{-- Search --}}
            <div class="lg:col-span-2">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="{{ __('admin.search_orders') }}"
                       class="w-full px-4 py-2
                              rounded-lg
                              border border-gray-200
                              focus:ring-2 focus:ring-primary/20 focus:border-primary
                              transition-all duration-200
                              text-sm">
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
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>
                        {{ __('admin.status_processing') }}
                    </option>
                    <option value="with_delivery_company" {{ request('status') == 'with_delivery_company' ? 'selected' : '' }}>
                        {{ __('admin.status_with_delivery_company') }}
                    </option>
                    <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>
                        {{ __('admin.status_received') }}
                    </option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                        {{ __('admin.status_cancelled') }}
                    </option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>
                        {{ __('admin.status_returned') }}
                    </option>
                </select>
            </div>

            {{-- City Filter --}}
            <div>
                <select name="city_id"
                        class="w-full px-4 py-2
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               bg-white">
                    <option value="">{{ __('admin.filter_by_city') }}</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
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

        {{-- Second Row: Courier + Coupon + Clear --}}
        @if(request()->hasAny(['search', 'status', 'city_id', 'courier_id', 'coupon_id']))
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Courier Filter --}}
            <div>
                <select name="courier_id"
                        class="w-full px-4 py-2
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               bg-white">
                    <option value="">{{ __('admin.filter_by_courier') }}</option>
                    @foreach($couriers as $courier)
                        <option value="{{ $courier->id }}" {{ request('courier_id') == $courier->id ? 'selected' : '' }}>
                            {{ $courier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Coupon Filter --}}
            <div>
                <select name="coupon_id"
                        class="w-full px-4 py-2
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               bg-white">
                    <option value="">{{ __('admin.filter_by_coupon') }}</option>
                    @foreach($coupons as $coupon)
                        <option value="{{ $coupon->id }}" {{ request('coupon_id') == $coupon->id ? 'selected' : '' }}>
                            {{ $coupon->code }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Clear Button --}}
            <div>
                <a href="{{ route('admin.orders.index') }}"
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
        </div>
        @endif
    </form>
</div>

{{-- Orders Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.order_number') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.customer_name') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.order_city') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.total_price') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.order_status') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.order_date') }}
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
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        {{-- Order Number --}}
                        <td class="px-6 py-4">
                            <div class="text-sm font-mono font-medium text-primary">
                                {{ $order->order_number }}
                            </div>
                        </td>

                        {{-- Customer Name --}}
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $order->full_name }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $order->mobiles->pluck('phone_number')->first() ?? '-' }}
                            </div>
                        </td>

                        {{-- City --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $order->city->name ?? '-' }}
                            </div>
                        </td>

                        {{-- Total Price --}}
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900">
                                {{ number_format($order->total_price_for_customer, 2) }} JOD
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4">
                            @switch($order->status)
                                @case('processing')
                                    <span class="inline-flex items-center
                                                  px-2.5 py-1
                                                  rounded-full
                                                  text-xs font-medium
                                                  bg-blue-100 text-blue-700">
                                        {{ __('admin.status_processing') }}
                                    </span>
                                @break

                                @case('with_delivery_company')
                                    <span class="inline-flex items-center
                                                  px-2.5 py-1
                                                  rounded-full
                                                  text-xs font-medium
                                                  bg-yellow-100 text-yellow-700">
                                        {{ __('admin.status_with_delivery_company') }}
                                    </span>
                                @break

                                @case('received')
                                    <span class="inline-flex items-center
                                                  px-2.5 py-1
                                                  rounded-full
                                                  text-xs font-medium
                                                  bg-green-100 text-green-700">
                                        {{ __('admin.status_received') }}
                                    </span>
                                @break

                                @case('cancelled')
                                    <span class="inline-flex items-center
                                                  px-2.5 py-1
                                                  rounded-full
                                                  text-xs font-medium
                                                  bg-red-100 text-red-700">
                                        {{ __('admin.status_cancelled') }}
                                    </span>
                                @break

                                @case('returned')
                                    <span class="inline-flex items-center
                                                  px-2.5 py-1
                                                  rounded-full
                                                  text-xs font-medium
                                                  bg-orange-100 text-orange-700">
                                        {{ __('admin.status_returned') }}
                                    </span>
                                @break
                            @endswitch
                        </td>

                        {{-- Date --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600">
                                {{ $order->created_at->format('Y-m-d') }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $order->created_at->format('H:i') }}
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2
                                        {{ $direction === 'rtl' ? 'flex-row-reverse' : '' }}">
                                @can('view orders')
                                    {{-- View --}}
                                    <a href="{{ route('admin.orders.show', $order) }}"
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

                                @if($order->isProcessing())
                                    @can('manage orders')
                                        {{-- Edit --}}
                                        <a href="{{ route('admin.orders.edit', $order) }}"
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
                                @endif

                                @can('delete orders')
                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 text-gray-400 hover:text-red-600
                                                       rounded-lg hover:bg-red-50
                                                       transition-colors duration-150"
                                                onclick="return confirm('{{ __('admin.confirm_delete_order') }}')"
                                                title="{{ __('admin.delete') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 rounded-full
                                            bg-gray-100
                                            flex items-center justify-center
                                            mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">
                                    {{ __('admin.no_orders_found') }}
                                </h3>
                                <p class="text-sm text-gray-500">
                                    {{ __('admin.no_orders_description') }}
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($orders->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-600">
            {{ __('admin.showing', ['from' => $orders->firstItem(), 'to' => $orders->lastItem(), 'total' => $orders->total()]) }}
        </div>
        {{ $orders->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
