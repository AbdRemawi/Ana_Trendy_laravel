@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.delivery_courier_fees_management');
@endphp

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('admin.delivery_courier_fees') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.delivery_courier_fees_description') }}
            </p>
        </div>

        @can('manage delivery courier fees')
        <a href="{{ route('admin.delivery-courier-fees.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2
                  bg-primary text-white
                  rounded-lg
                  hover:bg-primary/90
                  transition-colors duration-200
                  font-medium text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('admin.create_fee') }}
        </a>
        @endcan
    </div>
</div>

{{-- Search & Filter --}}
<div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="{{ route('admin.delivery-courier-fees.index') }}" class="flex flex-col sm:flex-row gap-4">
        {{-- Search --}}
        <div class="flex-1">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="{{ __('admin.search_fees') }}"
                   class="w-full px-4 py-2
                          rounded-lg
                          border border-gray-200
                          focus:ring-2 focus:ring-primary/20 focus:border-primary
                          transition-all duration-200
                          text-sm">
        </div>

        {{-- Courier Filter --}}
        <div class="sm:w-56">
            <select name="delivery_courier_id"
                    class="w-full px-4 py-2
                           rounded-lg
                           border border-gray-200
                           focus:ring-2 focus:ring-primary/20 focus:border-primary
                           transition-all duration-200
                           text-sm
                           bg-white">
                <option value="">{{ __('admin.all_couriers') }}</option>
                @foreach($couriers as $courier)
                    <option value="{{ $courier->id }}"
                            {{ request('delivery_courier_id') == $courier->id ? 'selected' : '' }}>
                        {{ $courier->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- City Filter --}}
        <div class="sm:w-48">
            <select name="city_id"
                    class="w-full px-4 py-2
                           rounded-lg
                           border border-gray-200
                           focus:ring-2 focus:ring-primary/20 focus:border-primary
                           transition-all duration-200
                           text-sm
                           bg-white">
                <option value="">{{ __('admin.all_cities') }}</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}"
                            {{ request('city_id') == $city->id ? 'selected' : '' }}>
                        {{ $city->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Status Filter --}}
        <div class="sm:w-40">
            <select name="is_active"
                    class="w-full px-4 py-2
                           rounded-lg
                           border border-gray-200
                           focus:ring-2 focus:ring-primary/20 focus:border-primary
                           transition-all duration-200
                           text-sm
                           bg-white">
                <option value="">{{ __('admin.all_statuses') }}</option>
                <option value="1" {{ request('is_active') === '1' || request('is_active') === 'true' ? 'selected' : '' }}>
                    {{ __('admin.status_active') }}
                </option>
                <option value="0" {{ request('is_active') === '0' || request('is_active') === 'false' ? 'selected' : '' }}>
                    {{ __('admin.status_inactive') }}
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

        @if(request()->hasAny(['search', 'delivery_courier_id', 'city_id', 'is_active']))
        <a href="{{ route('admin.delivery-courier-fees.index') }}"
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

{{-- Fees Table Card --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.courier') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.city') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.real_fee_amount') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.currency') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.status') }}
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
                @forelse($fees as $fee)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        {{-- Courier --}}
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $fee->courier->name }}
                            </div>
                        </td>

                        {{-- City --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $fee->city->name }}
                            </div>
                        </td>

                        {{-- Real Fee Amount --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 font-medium font-mono">
                                {{ number_format($fee->real_fee_amount, 3) }}
                            </div>
                        </td>

                        {{-- Currency --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600">
                                {{ $fee->currency }}
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4">
                            @if($fee->is_active)
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
                                @can('view delivery courier fees')
                                    {{-- View --}}
                                    <a href="{{ route('admin.delivery-courier-fees.show', $fee) }}"
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

                                @can('manage delivery courier fees')
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.delivery-courier-fees.edit', $fee) }}"
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
                                    <form method="POST"
                                          action="{{ route('admin.delivery-courier-fees.toggle-status', $fee) }}"
                                          class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="p-2 text-gray-400 hover:text-yellow-600
                                                       rounded-lg hover:bg-yellow-50
                                                       transition-colors duration-150"
                                                title="{{ $fee->is_active ? __('admin.deactivate') : __('admin.activate') }}">
                                            @if($fee->is_active)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M10 14l2-2m0 0l-4-4m4 4l4-4m6 6"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>

                                    {{-- Delete --}}
                                    <button type="button"
                                            class="delete-fee-btn p-2 text-gray-400 hover:text-red-600
                                                   rounded-lg hover:bg-red-50
                                                   transition-colors duration-150"
                                            data-url="{{ route('admin.delivery-courier-fees.destroy', $fee) }}"
                                            data-item-name="{{ $fee->courier->name }} - {{ $fee->city->name }}"
                                            data-modal-confirm="{{ __('admin.confirm_delete_fee') }}"
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
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 rounded-full
                                            bg-gray-100
                                            flex items-center justify-center
                                            mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M12 8c-1.657 0-3 .447-3-1-1.657 0-3 3.007-3 6.68-1.667 1-6.68-3 .447-3 3-.447-6.68 3-3zm0 2.91c.507 0 1.116.123 1.554.333l-1.554 2.223a5.987 5.987 0 002.828 3.528 3.528 3.528.0 002.828-3.528l-1.554-2.223C13.116 5.924 12.507 5.791 12 5.791zm-2 4.586l-1.554 2.223a5.987 5.987 0 002.828 3.528 3.528 3.528.0 002.828-3.528l-1.554-2.223c.123-.507.333-1.116.333-1.554l1.554-2.223a5.987 5.987 0 002.828-3.528 3.528 3.528.0 002.828 3.528l1.554 2.223c-.123.507-.333 1.116-.333 1.554zM12 14a2 2 0 100-4 0 2 2 0 004 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">
                                    {{ __('admin.no_fees_found') }}
                                </h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    {{ __('admin.no_fees_description') }}
                                </p>
                                @can('manage delivery courier fees')
                                <a href="{{ route('admin.delivery-courier-fees.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2
                                          bg-primary text-white
                                          rounded-lg
                                          hover:bg-primary/90
                                          transition-colors duration-200
                                          font-medium text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('admin.create_first_fee') }}
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
    @if($fees->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <div class="text-sm text-gray-600">
            {{ __('admin.showing', [
                'from' => $fees->firstItem(),
                'to' => $fees->lastItem(),
                'total' => $fees->total()
            ]) }}
        </div>
        {{ $fees->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
