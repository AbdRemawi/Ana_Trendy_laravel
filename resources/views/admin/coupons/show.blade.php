@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.coupon_details') . ' - ' . $coupon->code;
@endphp

{{-- Back Button --}}
<div class="mb-6">
    <a href="{{ route('admin.coupons.index') }}"
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
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ $coupon->code }}
                </h1>
                @if($coupon->is_active)
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
            </div>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.created_at') }}: {{ $coupon->created_at->format('Y-m-d H:i') }}
            </p>
        </div>

        @can('manage orders')
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.coupons.edit', $coupon) }}"
               class="inline-flex items-center gap-2 px-4 py-2
                      bg-primary text-white
                      rounded-lg
                      hover:bg-primary/90
                      transition-colors duration-200
                      font-medium text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                {{ __('admin.edit_coupon') }}
            </a>
        </div>
        @endcan
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Column: Coupon Details --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Coupon Information --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                {{ __('admin.coupon_details') }}
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Code --}}
                <div>
                    <div class="text-sm text-gray-500 mb-1">{{ __('admin.coupon_code') }}</div>
                    <div class="text-sm font-mono font-semibold text-primary text-lg">
                        {{ $coupon->code }}
                    </div>
                </div>

                {{-- Type --}}
                <div>
                    <div class="text-sm text-gray-500 mb-1">{{ __('admin.coupon_type') }}</div>
                    <div>
                        @switch($coupon->type)
                            @case('fixed')
                                <span class="inline-flex items-center
                                              px-2.5 py-1
                                              rounded-md
                                              text-sm font-medium
                                              bg-blue-50 text-blue-700">
                                    {{ __('admin.type_fixed') }}
                                </span>
                            @break

                            @case('percentage')
                                <span class="inline-flex items-center
                                              px-2.5 py-1
                                              rounded-md
                                              text-sm font-medium
                                              bg-purple-50 text-purple-700">
                                    {{ __('admin.type_percentage') }}
                                </span>
                            @break

                            @case('free_delivery')
                                <span class="inline-flex items-center
                                              px-2.5 py-1
                                              rounded-md
                                              text-sm font-medium
                                              bg-green-50 text-green-700">
                                    {{ __('admin.type_free_delivery') }}
                                </span>
                            @break
                        @endswitch
                    </div>
                </div>

                {{-- Value --}}
                @if($coupon->type !== 'free_delivery')
                <div>
                    <div class="text-sm text-gray-500 mb-1">{{ __('admin.coupon_value') }}</div>
                    <div class="text-sm font-semibold text-gray-900">
                        @if($coupon->type === 'percentage')
                            {{ number_format($coupon->value, 0) }}%
                        @else
                            {{ number_format($coupon->value, 2) }} JOD
                        @endif
                    </div>
                </div>
                @endif

                {{-- Minimum Order --}}
                <div>
                    <div class="text-sm text-gray-500 mb-1">{{ __('admin.coupon_minimum_order') }}</div>
                    <div class="text-sm font-semibold text-gray-900">
                        @if($coupon->minimum_order_amount > 0)
                            {{ number_format($coupon->minimum_order_amount, 2) }} JOD
                        @else
                            {{ __('admin.optional') }}
                        @endif
                    </div>
                </div>

                {{-- Validity Period --}}
                <div class="sm:col-span-2">
                    <div class="text-sm text-gray-500 mb-1">{{ __('admin.coupon_valid_from') }}</div>
                    <div class="text-sm font-medium text-gray-900">
                        {{ $coupon->valid_from->format('Y-m-d H:i') }}
                        @if($coupon->valid_until)
                            → {{ $coupon->valid_until->format('Y-m-d H:i') }}
                        @else
                            ({{ __('admin.coupon_unlimited') }})
                        @endif
                    </div>
                </div>
            </div>

            {{-- Status Badges --}}
            <div class="mt-6 pt-6 border-t border-gray-100">
                <div class="flex flex-wrap gap-2">
                    @if($coupon->isExpired())
                        <span class="inline-flex items-center
                                      px-2.5 py-1
                                      rounded-full
                                      text-xs font-medium
                                      bg-red-100 text-red-700">
                            {{ __('admin.coupon_is_expired') }}
                        </span>
                    @elseif($coupon->isNotYetStarted())
                        <span class="inline-flex items-center
                                      px-2.5 py-1
                                      rounded-full
                                      text-xs font-medium
                                      bg-yellow-100 text-yellow-700">
                            {{ __('admin.coupon_not_started') }}
                        </span>
                    @elseif($coupon->isValidNow())
                        <span class="inline-flex items-center
                                      px-2.5 py-1
                                      rounded-full
                                      text-xs font-medium
                                      bg-green-100 text-green-700">
                            Valid Now
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Orders Using This Coupon --}}
        @if($coupon->orders_count > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                {{ __('admin.coupon_orders_count') }}
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-100">
                        <tr>
                            <th class="pb-3 text-left">
                                <span class="text-xs font-semibold text-gray-600 uppercase">
                                    {{ __('admin.order_number') }}
                                </span>
                            </th>
                            <th class="pb-3 text-left">
                                <span class="text-xs font-semibold text-gray-600 uppercase">
                                    {{ __('admin.customer_name') }}
                                </span>
                            </th>
                            <th class="pb-3 text-left">
                                <span class="text-xs font-semibold text-gray-600 uppercase">
                                    {{ __('admin.coupon_discount') }}
                                </span>
                            </th>
                            <th class="pb-3 text-left">
                                <span class="text-xs font-semibold text-gray-600 uppercase">
                                    {{ __('admin.order_date') }}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($coupon->orders()->latest()->limit(10)->get() as $order)
                        <tr>
                            <td class="py-3">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="text-sm font-mono text-primary hover:underline">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="py-3">
                                <div class="text-sm text-gray-900">{{ $order->full_name }}</div>
                            </td>
                            <td class="py-3">
                                <div class="text-sm font-medium text-red-600">
                                    -{{ number_format($order->coupon_discount_amount, 2) }} JOD
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="text-sm text-gray-600">
                                    {{ $order->created_at->format('Y-m-d') }}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($coupon->orders_count > 10)
            <div class="mt-4 text-center">
                <a href="{{ route('admin.orders.index', ['coupon_id' => $coupon->id]) }}"
                   class="text-sm text-primary hover:underline">
                    View all {{ $coupon->orders_count }} orders →
                </a>
            </div>
            @endif
        </div>
        @endif
    </div>

    {{-- Right Column: Usage Stats & Actions --}}
    <div class="space-y-6">
        {{-- Usage Statistics --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                {{ __('admin.coupon_usage_info') }}
            </h2>
            <div class="space-y-4">
                <div>
                    <div class="text-sm text-gray-500 mb-1">{{ __('admin.coupon_times_used') }}</div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $coupon->used_count }}
                    </div>
                </div>

                @if($coupon->max_uses)
                <div>
                    <div class="text-sm text-gray-500 mb-1">{{ __('admin.coupon_remaining_uses') }}</div>
                    <div class="text-2xl font-bold {{ $coupon->remaining_uses > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $coupon->remaining_uses }}
                    </div>
                </div>
                @else
                <div>
                    <div class="text-sm text-gray-500 mb-1">{{ __('admin.coupon_max_uses') }}</div>
                    <div class="text-sm font-medium text-gray-900">
                        {{ __('admin.coupon_unlimited') }}
                    </div>
                </div>
                @endif

                @if($totalDiscountGiven > 0)
                <div class="border-t border-gray-200 pt-4">
                    <div class="text-sm text-gray-500 mb-1">{{ __('admin.coupon_discount_given') }}</div>
                    <div class="text-xl font-bold text-red-600">
                        -{{ number_format($totalDiscountGiven, 2) }} JOD
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        @can('manage orders')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                {{ __('admin.actions') }}
            </h2>
            <div class="space-y-3">
                {{-- Toggle Status --}}
                <form method="POST" action="{{ route('admin.coupons.toggle-status', $coupon) }}">
                    @csrf
                    <button type="submit"
                            class="w-full px-4 py-2.5
                                   {{ $coupon->is_active ? 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}
                                   rounded-lg
                                   transition-colors duration-200
                                   font-medium text-sm
                                   flex items-center justify-center gap-2">
                        @if($coupon->is_active)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            {{ __('admin.deactivate_coupon') }}
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ __('admin.activate_coupon') }}
                        @endif
                    </button>
                </form>

                {{-- Delete (only if not used) --}}
                @if($coupon->orders_count === 0)
                <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}"
                      onsubmit="return confirm('{{ __('admin.confirm_delete_coupon') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-4 py-2.5
                                   bg-red-50 text-red-700 hover:bg-red-100
                                   rounded-lg
                                   transition-colors duration-200
                                   font-medium text-sm
                                   flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{ __('admin.delete_coupon') }}
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endcan

        {{-- Notes --}}
        @if(!$coupon->is_active)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm text-yellow-800 font-medium">
                        {{ __('admin.warning') }}
                    </p>
                    <p class="text-sm text-yellow-700 mt-1">
                        This coupon is currently inactive and won't be available to customers.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
