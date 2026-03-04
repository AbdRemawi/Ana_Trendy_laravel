@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.roles_management');
@endphp

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('admin.roles') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.roles_description') }}
            </p>
        </div>

        @can('manage roles')
        <a href="{{ route('admin.roles.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2
                  bg-primary text-white
                  rounded-lg
                  hover:bg-primary/90
                  transition-colors duration-200
                  font-medium text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('admin.create_role') }}
        </a>
        @endcan
    </div>
</div>

{{-- Roles Table Card --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.role_name') }}
                        </span>
                    </th>
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.permissions_count') }}
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
                @forelse($roles as $role)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        {{-- Role Name --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($role->name === 'super_admin')
                                    <span class="inline-flex items-center gap-1.5
                                                  px-2.5 py-1
                                                  rounded-full
                                                  text-xs font-medium
                                                  bg-amber-100 text-amber-700">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        {{ __('admin.super_admin') }}
                                    </span>
                                @else
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ $role->name }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Permissions Count --}}
                        <td class="px-6 py-4">
                            @if($role->permissions_count > 0)
                                <span class="inline-flex items-center px-2.5 py-1
                                              rounded-full
                                              text-xs font-medium
                                              bg-primary/10 text-primary">
                                    {{ $role->permissions_count }} {{ __('admin.permissions') }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400">
                                    {{ __('admin.no_permissions') }}
                                </span>
                            @endif
                        </td>

                        {{-- Created At --}}
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">
                                {{ $role->created_at->format('Y M d') }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2
                                        {{ $direction === 'rtl' ? 'flex-row-reverse' : '' }}">
                                @can('manage roles')
                                    {{-- View --}}
                                    <a href="{{ route('admin.roles.show', $role) }}"
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
                                    <a href="{{ route('admin.roles.edit', $role) }}"
                                       class="p-2 text-gray-400 hover:text-primary
                                              rounded-lg hover:bg-gray-100
                                              transition-colors duration-150"
                                       title="{{ __('admin.edit') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    {{-- Delete (prevent super_admin deletion) --}}
                                    @if($role->name !== 'super_admin')
                                    <button type="button"
                                            class="delete-role-btn p-2 text-gray-400 hover:text-red-600
                                                   rounded-lg hover:bg-red-50
                                                   transition-colors duration-150"
                                            data-url="{{ route('admin.roles.destroy', $role) }}"
                                            data-item-name="{{ $role->name }}"
                                            data-modal-confirm="{{ __('admin.confirm_delete_role') }}"
                                            title="{{ __('admin.delete') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    @endif
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
                                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">
                                    {{ __('admin.no_roles_found') }}
                                </h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    {{ __('admin.no_roles_description') }}
                                </p>
                                @can('manage roles')
                                <a href="{{ route('admin.roles.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2
                                          bg-primary text-white
                                          rounded-lg
                                          hover:bg-primary/90
                                          transition-colors duration-200
                                          font-medium text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('admin.create_first_role') }}
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
    @if($roles->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <div class="text-sm text-gray-600">
            {{ __('admin.showing', ['from' => $roles->firstItem(), 'to' => $roles->lastItem(), 'total' => $roles->total()]) }}
        </div>
        {{ $roles->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
