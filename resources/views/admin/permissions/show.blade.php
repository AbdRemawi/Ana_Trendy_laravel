@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = $permission->name;
@endphp

<div class="max-w-4xl mx-auto">
    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.permissions.index') }}"
               class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ __('admin.permission_details') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $permission->name }}
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Permission Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                {{ __('admin.information') }}
            </h2>
            <dl class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm text-gray-500">{{ __('admin.permission_name') }}</dt>
                    <dd class="text-sm font-medium text-gray-900 {{ $direction === 'rtl' ? 'text-left' : 'text-right' }}">{{ $permission->name }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm text-gray-500">{{ __('admin.roles_count') }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $permission->roles->count() }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm text-gray-500">{{ __('admin.created_at') }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $permission->created_at->format('Y M d') }}</dd>
                </div>
                <div class="flex justify-between py-2">
                    <dt class="text-sm text-gray-500">{{ __('admin.updated_at') }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $permission->updated_at->format('Y M d') }}</dd>
                </div>
            </dl>

            @can('manage permissions')
            <div class="mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.permissions.edit', $permission) }}"
                   class="block w-full px-4 py-2.5 bg-primary text-white rounded-lg
                          text-center hover:bg-primary/90 font-medium text-sm">
                    {{ __('admin.edit_permission') }}
                </a>
            </div>
            @endcan
        </div>

        {{-- Roles Using This Permission --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                {{ __('admin.assigned_roles') }}
            </h2>

            @if($permission->roles->count() > 0)
            <div class="space-y-2">
                @foreach($permission->roles as $role)
                <div class="flex items-center justify-between
                            p-3 rounded-lg
                            bg-gray-50
                            border border-gray-100">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-900">
                            {{ $role->name }}
                        </span>
                        @if($role->name === 'super_admin')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5
                                      rounded text-xs font-medium
                                      bg-amber-100 text-amber-700">
                            {{ __('admin.super_admin') }}
                        </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <p class="text-sm text-gray-500">
                    {{ __('admin.not_assigned_to_any_role') }}
                </p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
