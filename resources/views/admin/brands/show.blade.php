@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.brand_details');
@endphp

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.brands.index') }}"
               class="p-2 text-gray-400 hover:text-gray-600
                      rounded-lg hover:bg-gray-100
                      transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ $brand->name }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('admin.brand_details') }}
                </p>
            </div>
        </div>

        @can('manage brands')
            <a href="{{ route('admin.brands.edit', $brand) }}"
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
                {{ __('admin.edit_brand') }}
            </a>
        @endcan
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Column: Brand Details --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">
                {{ __('admin.brand_details') }}
            </h2>

            <div class="space-y-6">
                {{-- Logo --}}
                <div class="flex items-start gap-6">
                    @if($brand->logo)
                        <img src="{{ $brand->logo_url }}"
                             alt="{{ $brand->name }}"
                             class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                    @else
                        <div class="w-32 h-32 rounded-lg bg-gray-100
                                    flex items-center justify-center
                                    border border-gray-200">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif

                    <div class="flex-1">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    {{ __('admin.brand_name') }}
                                </dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">
                                    {{ $brand->name }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    {{ __('admin.brand_slug') }}
                                </dt>
                                <dd class="mt-1 text-sm font-mono text-gray-900">
                                    {{ $brand->slug }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    {{ __('admin.brand_status') }}
                                </dt>
                                <dd class="mt-1">
                                    @if($brand->status === 'active')
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
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Information --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">
                {{ __('admin.information') }}
            </h3>

            <dl class="space-y-4 text-sm">
                <div>
                    <dt class="text-gray-500">
                        {{ __('admin.created_at') }}
                    </dt>
                    <dd class="mt-1 text-gray-900">
                        {{ $brand->created_at->format('Y-m-d') }}
                    </dd>
                </div>

                <div>
                    <dt class="text-gray-500">
                        {{ __('admin.updated_at') }}
                    </dt>
                    <dd class="mt-1 text-gray-900">
                        {{ $brand->updated_at->format('Y-m-d') }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Actions Card --}}
        @can('delete brands')
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">
                {{ __('admin.actions') }}
            </h3>
            <button type="button"
                    class="delete-brand-btn w-full px-4 py-2.5
                           bg-red-50 text-red-600
                           rounded-lg
                           hover:bg-red-100
                           transition-colors duration-200
                           font-medium text-sm
                           flex items-center justify-center gap-2"
                    data-url="{{ route('admin.brands.destroy', $brand) }}"
                    data-item-name="{{ $brand->name }}"
                    data-modal-confirm="{{ __('admin.confirm_delete_brand') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                {{ __('admin.delete_brand') }}
            </button>
        </div>
        @endcan
    </div>
</div>
@endsection
