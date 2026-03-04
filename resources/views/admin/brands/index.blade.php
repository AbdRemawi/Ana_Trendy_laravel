@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.brands_management');
@endphp

{{-- Page Header --}}
<x-admin.page-header
    :title="'admin.brands'"
    :description="'admin.brands_description'"
    :createRoute="route('admin.brands.create')"
    :createPermission="'manage brands'"
    :createText="'admin.create_brand'"
/>

{{-- Search & Filter --}}
<div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="{{ route('admin.brands.index') }}" class="flex flex-col sm:flex-row gap-4">
        {{-- Search --}}
        <div class="flex-1">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="{{ __('admin.search_brands') }}"
                   class="w-full px-4 py-2
                          rounded-lg
                          border border-gray-200
                          focus:ring-2 focus:ring-primary/20 focus:border-primary
                          transition-all duration-200
                          text-sm">
        </div>

        {{-- Status Filter --}}
        <div class="sm:w-48">
            <select name="status"
                    class="w-full px-4 py-2
                           rounded-lg
                           border border-gray-200
                           focus:ring-2 focus:ring-primary/20 focus:border-primary
                           transition-all duration-200
                           text-sm
                           bg-white">
                <option value="">{{ __('admin.all_statuses') }}</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                    {{ __('admin.status_active') }}
                </option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
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

        @if(request()->hasAny(['search', 'status']))
        <a href="{{ route('admin.brands.index') }}"
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

{{-- Brands Table Card --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.brand_name') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.brand_logo') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.brand_slug') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.brand_status') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.created_at') }}
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
                @forelse($brands as $brand)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        {{-- Name --}}
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $brand->name }}
                            </div>
                        </td>

                        {{-- Logo --}}
                        <td class="px-6 py-4">
                            @if($brand->logo)
                                <img src="{{ $brand->logo_url }}"
                                     alt="{{ $brand->name }}"
                                     class="w-12 h-12 object-cover rounded-lg
                                            border border-gray-200
                                            bg-white">
                            @else
                                <div class="w-12 h-12 rounded-lg
                                            bg-gray-100
                                            flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </td>

                        {{-- Slug --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 font-mono">
                                {{ $brand->slug }}
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4">
                            <x-admin.status-badge :status="$brand->status" />
                        </td>

                        {{-- Created At --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600">
                                {{ $brand->created_at->format('Y-m-d') }}
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 {{ $direction === 'rtl' ? 'flex-row-reverse' : '' }}">
                                <x-admin.action-buttons
                                    :model="$brand"
                                    :viewRoute="'admin.brands.show'"
                                    :editRoute="'admin.brands.edit'"
                                    :deleteRoute="'admin.brands.destroy'"
                                    :viewPermission="'view brands'"
                                    :editPermission="'manage brands'"
                                    :deletePermission="'delete brands'"
                                />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <x-admin.table-empty-state
                                :title="'admin.no_brands_found'"
                                :description="'admin.no_brands_description'"
                                :icon="'heroicon-o-inbox'"
                                :actionText="'admin.create_first_brand'"
                                :actionRoute="route('admin.brands.create')"
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($brands->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <div class="text-sm text-gray-600">
            {{ __('admin.showing', ['from' => $brands->firstItem(), 'to' => $brands->lastItem(), 'total' => $brands->total()]) }}
        </div>
        {{ $brands->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
