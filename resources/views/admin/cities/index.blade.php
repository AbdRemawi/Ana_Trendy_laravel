@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.cities_management');
@endphp

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('admin.cities') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.cities_description') }}
            </p>
        </div>

        @can('manage cities')
        <a href="{{ route('admin.cities.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2
                  bg-primary text-white
                  rounded-lg
                  hover:bg-primary/90
                  transition-colors duration-200
                  font-medium text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('admin.create_city') }}
        </a>
        @endcan
    </div>
</div>

{{-- Search & Filter --}}
<div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="{{ route('admin.cities.index') }}" class="flex flex-col sm:flex-row gap-4">
        {{-- Search --}}
        <div class="flex-1">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="{{ __('admin.search_cities') }}"
                   class="w-full px-4 py-2
                          rounded-lg
                          border border-gray-200
                          focus:ring-2 focus:ring-primary/20 focus:border-primary
                          transition-all duration-200
                          text-sm">
        </div>

        {{-- Status Filter --}}
        <div class="sm:w-48">
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

        @if(request()->hasAny(['search', 'is_active']))
        <a href="{{ route('admin.cities.index') }}"
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

{{-- Cities Table Card --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.city_name') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.city_status') }}
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
                @forelse($cities as $city)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        {{-- Name --}}
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $city->name }}
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4">
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
                        </td>

                        {{-- Created At --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600">
                                {{ $city->created_at->format('Y-m-d') }}
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2
                                        {{ $direction === 'rtl' ? 'flex-row-reverse' : '' }}">
                                @can('view cities')
                                    {{-- View --}}
                                    <a href="{{ route('admin.cities.show', $city) }}"
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

                                @can('manage cities')
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.cities.edit', $city) }}"
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
                                          action="{{ route('admin.cities.toggle-status', $city) }}"
                                          class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="p-2 text-gray-400 hover:text-yellow-600
                                                       rounded-lg hover:bg-yellow-50
                                                       transition-colors duration-150"
                                                title="{{ $city->is_active ? __('admin.deactivate') : __('admin.activate') }}">
                                            @if($city->is_active)
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
                                            class="delete-city-btn p-2 text-gray-400 hover:text-red-600
                                                   rounded-lg hover:bg-red-50
                                                   transition-colors duration-150"
                                            data-url="{{ route('admin.cities.destroy', $city) }}"
                                            data-item-name="{{ $city->name }}"
                                            data-modal-confirm="{{ __('admin.confirm_delete_city') }}"
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
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 rounded-full
                                            bg-gray-100
                                            flex items-center justify-center
                                            mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-.21.221l-2.14-2.14a1 1 0 01-.364-.586l-5.736-5.736a1 1 0 01-.364-.586l-1.415 1.415a2 2 0 002.828 0l1.414 1.414a1 1 0 01-.364.586l5.736 5.736a1 1 0 01.586.364l1.415-1.414a2 2 0 010 2.828l-1.414-1.414A1 1 0 0117.657 16.657zM16 9a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">
                                    {{ __('admin.no_cities_found') }}
                                </h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    {{ __('admin.no_cities_description') }}
                                </p>
                                @can('manage cities')
                                <a href="{{ route('admin.cities.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2
                                          bg-primary text-white
                                          rounded-lg
                                          hover:bg-primary/90
                                          transition-colors duration-200
                                          font-medium text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('admin.create_first_city') }}
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
    @if($cities->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <div class="text-sm text-gray-600">
            {{ __('admin.showing', [
                'from' => $cities->firstItem(),
                'to' => $cities->lastItem(),
                'total' => $cities->total()
            ]) }}
        </div>
        {{ $cities->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
