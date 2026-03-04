@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.coupons_management');
@endphp

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('admin.coupons_management') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.coupons_description') }}
            </p>
        </div>

        @can('manage orders')
        <a href="{{ route('admin.coupons.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2
                  bg-primary text-white
                  rounded-lg
                  hover:bg-primary/90
                  transition-colors duration-200
                  font-medium text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('admin.create_coupon') }}
        </a>
        @endcan
    </div>
</div>

{{-- Search & Filter --}}
<div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="{{ route('admin.coupons.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            {{-- Search --}}
            <div class="sm:col-span-2">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="{{ __('admin.search_coupons') }}"
                       class="w-full px-4 py-2
                              rounded-lg
                              border border-gray-200
                              focus:ring-2 focus:ring-primary/20 focus:border-primary
                              transition-all duration-200
                              text-sm">
            </div>

            {{-- Type Filter --}}
            <div>
                <select name="type"
                        class="w-full px-4 py-2
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               bg-white">
                    <option value="">{{ __('admin.all_types') }}</option>
                    <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>
                        {{ __('admin.type_fixed') }}
                    </option>
                    <option value="percentage" {{ request('type') == 'percentage' ? 'selected' : '' }}>
                        {{ __('admin.type_percentage') }}
                    </option>
                    <option value="free_delivery" {{ request('type') == 'free_delivery' ? 'selected' : '' }}>
                        {{ __('admin.type_free_delivery') }}
                    </option>
                </select>
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
                    <option value="">{{ __('admin.filter_by_status') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                        {{ __('admin.filter_active') }}
                    </option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                        {{ __('admin.filter_inactive') }}
                    </option>
                </select>
            </div>
        </div>

        @if(request()->hasAny(['search', 'type', 'status']))
        <div>
            <a href="{{ route('admin.coupons.index') }}"
               class="inline-flex items-center px-4 py-2
                      border border-gray-200
                      rounded-lg
                      hover:bg-gray-50
                      transition-colors duration-200
                      text-sm text-gray-700">
                {{ __('admin.clear') }}
            </a>
        </div>
        @endif
    </form>
</div>

{{-- Coupons Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.coupon_code') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.coupon_type') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.coupon_value') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.coupon_used_count') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.coupon_valid_from') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.coupon_is_active') }}
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
                @forelse($coupons as $coupon)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        {{-- Code --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg
                                            bg-primary/10
                                            flex items-center justify-center">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7A2 2 0 018 20H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-mono font-semibold text-gray-900">
                                        {{ $coupon->code }}
                                    </div>
                                    @if($coupon->orders_count > 0)
                                    <div class="text-xs text-gray-500">
                                        {{ $coupon->orders_count }} {{ __('admin.coupon_orders_count') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Type --}}
                        <td class="px-6 py-4">
                            @switch($coupon->type)
                                @case('fixed')
                                    <span class="inline-flex items-center
                                                  px-2.5 py-1
                                                  rounded-md
                                                  text-xs font-medium
                                                  bg-blue-50 text-blue-700">
                                        {{ __('admin.type_fixed') }}
                                    </span>
                                @break

                                @case('percentage')
                                    <span class="inline-flex items-center
                                                  px-2.5 py-1
                                                  rounded-md
                                                  text-xs font-medium
                                                  bg-purple-50 text-purple-700">
                                        {{ __('admin.type_percentage') }}
                                    </span>
                                @break

                                @case('free_delivery')
                                    <span class="inline-flex items-center
                                                  px-2.5 py-1
                                                  rounded-md
                                                  text-xs font-medium
                                                  bg-green-50 text-green-700">
                                        {{ __('admin.type_free_delivery') }}
                                    </span>
                                @break
                            @endswitch
                        </td>

                        {{-- Value --}}
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900">
                                @if($coupon->type === 'percentage')
                                    {{ number_format($coupon->value, 0) }}%
                                @elseif($coupon->type === 'free_delivery')
                                    -
                                @else
                                    {{ number_format($coupon->value, 2) }} JOD
                                @endif
                            </div>
                            @if($coupon->minimum_order_amount > 0)
                            <div class="text-xs text-gray-500">
                                Min: {{ number_format($coupon->minimum_order_amount, 2) }} JOD
                            </div>
                            @endif
                        </td>

                        {{-- Usage --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                @if($coupon->max_uses)
                                    {{ $coupon->used_count }} / {{ $coupon->max_uses }}
                                @else
                                    {{ $coupon->used_count }}
                                @endif
                            </div>
                            @if($coupon->remaining_uses !== null)
                            <div class="text-xs {{ $coupon->remaining_uses > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $coupon->remaining_uses }} {{ __('admin.coupon_remaining_uses') }}
                            </div>
                            @endif
                        </td>

                        {{-- Validity --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600">
                                <div>{{ $coupon->valid_from->format('Y-m-d') }}</div>
                                @if($coupon->valid_until)
                                <div class="text-xs text-gray-400">
                                    → {{ $coupon->valid_until->format('Y-m-d') }}
                                </div>
                                @else
                                <div class="text-xs text-gray-400">
                                    {{ __('admin.coupon_unlimited') }}
                                </div>
                                @endif
                            </div>

                            {{-- Status Badge --}}
                            @if($coupon->isExpired())
                                <div class="mt-1">
                                    <span class="inline-flex items-center
                                                  px-2 py-0.5
                                                  rounded text-xs font-medium
                                                  bg-red-100 text-red-700">
                                        {{ __('admin.coupon_is_expired') }}
                                    </span>
                                </div>
                            @elseif($coupon->isNotYetStarted())
                                <div class="mt-1">
                                    <span class="inline-flex items-center
                                                  px-2 py-0.5
                                                  rounded text-xs font-medium
                                                  bg-yellow-100 text-yellow-700">
                                        {{ __('admin.coupon_not_started') }}
                                    </span>
                                </div>
                            @endif
                        </td>

                        {{-- Active Status --}}
                        <td class="px-6 py-4">
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
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2
                                        {{ $direction === 'rtl' ? 'flex-row-reverse' : '' }}">
                                @can('manage orders')
                                    {{-- View --}}
                                    <a href="{{ route('admin.coupons.show', $coupon) }}"
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

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                       class="p-2 text-gray-400 hover:text-primary
                                              rounded-lg hover:bg-gray-100
                                              transition-colors duration-150"
                                       title="{{ __('admin.edit') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    {{-- Toggle Status --}}
                                    <form method="POST" action="{{ route('admin.coupons.toggle-status', $coupon) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="p-2 {{ $coupon->is_active ? 'text-yellow-500 hover:text-yellow-600' : 'text-green-500 hover:text-green-600' }}
                                                       rounded-lg hover:bg-gray-100
                                                       transition-colors duration-150"
                                                title="{{ $coupon->is_active ? __('admin.deactivate_coupon') : __('admin.activate_coupon') }}">
                                            @if($coupon->is_active)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                @endcan

                                @can('manage orders')
                                    @if($coupon->orders_count === 0)
                                        {{-- Delete --}}
                                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-2 text-gray-400 hover:text-red-600
                                                           rounded-lg hover:bg-red-50
                                                           transition-colors duration-150"
                                                    onclick="return confirm('{{ __('admin.confirm_delete_coupon') }}')"
                                                    title="{{ __('admin.delete') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7A2 2 0 018 20H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">
                                    {{ __('admin.no_coupons_found') }}
                                </h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    {{ __('admin.no_coupons_description') }}
                                </p>
                                @can('manage orders')
                                <a href="{{ route('admin.coupons.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2
                                          bg-primary text-white
                                          rounded-lg
                                          hover:bg-primary/90
                                          transition-colors duration-200
                                          font-medium text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('admin.create_first_coupon') }}
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
    @if($coupons->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-600">
            {{ __('admin.showing', ['from' => $coupons->firstItem(), 'to' => $coupons->lastItem(), 'total' => $coupons->total()]) }}
        </div>
        {{ $coupons->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
