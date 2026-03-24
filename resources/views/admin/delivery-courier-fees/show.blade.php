@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.fee_details');
@endphp

<div class="max-w-4xl mx-auto">
    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ __('admin.fee_details') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('admin.fee_details_description') }}
                </p>
            </div>

            @can('manage delivery courier fees')
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.delivery-courier-fees.edit', $fee) }}"
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
                    {{ __('admin.edit_fee') }}
                </a>
            </div>
            @endcan
        </div>
    </div>

    {{-- Details Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                {{ __('admin.fee_information') }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Courier --}}
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">
                        {{ __('admin.courier') }}
                    </p>
                    <p class="text-sm text-gray-900">
                        {{ $fee->courier->name }}
                    </p>
                </div>

                {{-- City --}}
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">
                        {{ __('admin.city') }}
                    </p>
                    <p class="text-sm text-gray-900">
                        {{ $fee->city->name }}
                    </p>
                </div>

                {{-- Real Fee Amount --}}
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">
                        {{ __('admin.real_fee_amount') }}
                    </p>
                    <p class="text-lg font-semibold text-gray-900 font-mono">
                        {{ number_format($fee->real_fee_amount, 3) }} {{ $fee->currency }}
                    </p>
                </div>

                {{-- Currency --}}
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">
                        {{ __('admin.currency') }}
                    </p>
                    <p class="text-sm text-gray-900">
                        {{ $fee->currency }}
                    </p>
                </div>

                {{-- Status --}}
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">
                        {{ __('admin.fee_status') }}
                    </p>
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
                </div>

                {{-- Created At --}}
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">
                        {{ __('admin.created_at') }}
                    </p>
                    <p class="text-sm text-gray-900">
                        {{ $fee->created_at->format('Y-m-d H:i') }}
                    </p>
                </div>

                {{-- Updated At --}}
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">
                        {{ __('admin.updated_at') }}
                    </p>
                    <p class="text-sm text-gray-900">
                        {{ $fee->updated_at->format('Y-m-d H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="mt-6">
        <a href="{{ route('admin.delivery-courier-fees.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900
                  transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7"/>
            </svg>
            {{ __('admin.back_to_fees') }}
        </a>
    </div>
</div>
