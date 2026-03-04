@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = $city->name;
@endphp

<div class="max-w-4xl mx-auto">
    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ $city->name }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('admin.city_details') }}
                </p>
            </div>

            @can('manage cities')
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.cities.edit', $city) }}"
                   class="inline-flex items-center gap-2 px-4 py-2
                          bg-primary text-white
                          rounded-lg
                          hover:bg-primary/90
                          transition-colors duration-200
                          font-medium text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    {{ __('admin.edit_city') }}
                </a>
            </div>
            @endcan
        </div>
    </div>

    {{-- Details Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                {{ __('admin.city_details') }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Name --}}
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">
                        {{ __('admin.city_name') }}
                    </p>
                    <p class="text-sm text-gray-900">
                        {{ $city->name }}
                    </p>
                </div>

                {{-- Status --}}
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">
                        {{ __('admin.city_status') }}
                    </p>
                    @if($city->is_active)
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

                {{-- Created At --}}
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">
                        {{ __('admin.created_at') }}
                    </p>
                    <p class="text-sm text-gray-900">
                        {{ $city->created_at->format('Y-m-d H:i') }}
                    </p>
                </div>

                {{-- Updated At --}}
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">
                        {{ __('admin.updated_at') }}
                    </p>
                    <p class="text-sm text-gray-900">
                        {{ $city->updated_at->format('Y-m-d H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Delivery Fees Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                {{ __('admin.delivery_fees_for_city') }} ({{ $city->deliveryFees->count() }})
            </h2>

            @if($city->deliveryFees->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-2 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ __('admin.courier') }}
                                    </span>
                                </th>
                                <th class="px-4 py-2 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ __('admin.real_fee_amount') }}
                                    </span>
                                </th>
                                <th class="px-4 py-2 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ __('admin.display_fee_amount') }}
                                    </span>
                                </th>
                                <th class="px-4 py-2 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ __('admin.status') }}
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($city->deliveryFees as $fee)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $fee->courier->name }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-600">
                                            {{ number_format($fee->real_fee_amount, 3) }} {{ $fee->currency }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-600">
                                            {{ number_format($fee->display_fee_amount, 3) }} {{ $fee->currency }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($fee->is_active)
                                            <span class="inline-flex items-center
                                                          px-2 py-0.5
                                                          rounded-full
                                                          text-xs font-medium
                                                          bg-green-100 text-green-700">
                                                {{ __('admin.status_active') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center
                                                          px-2 py-0.5
                                                          rounded-full
                                                          text-xs font-medium
                                                          bg-gray-100 text-gray-700">
                                                {{ __('admin.status_inactive') }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-12 h-12 rounded-full
                                bg-gray-100
                                flex items-center justify-center
                                mx-auto mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-.21.221l-2.14-2.14a1 1 0 01-.364-.586l-5.736-5.736a1 1 0 01-.364-.586l1.415 1.415a2 2 0 002.828 0l1.414 1.414a1 1 0 01-.364.586l5.736 5.736a1 1 0 01.586.364l1.415-1.414a2 2 0 010 2.828l-1.414-1.414A1 1 0 0117.657 16.657zM16 9a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900 mb-1">
                        {{ __('admin.no_delivery_fees_found') }}
                    </h3>
                    <p class="text-sm text-gray-500">
                        {{ __('admin.no_delivery_fees_description') }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- Back Button --}}
    <div class="mt-6">
        <a href="{{ route('admin.cities.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900
                  transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7"/>
            </svg>
            {{ __('admin.back_to_cities') }}
        </a>
    </div>
</div>
