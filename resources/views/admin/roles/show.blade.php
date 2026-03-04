@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = $role->name;
@endphp

<div class="mb-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.roles.index') }}"
           class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ $role->name }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.role_details') }}
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Role Info --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
            {{ __('admin.information') }}
        </h2>
        <dl class="space-y-3">
            <div class="flex justify-between py-2 border-b border-gray-100">
                <dt class="text-sm text-gray-500">{{ __('admin.role_name') }}</dt>
                <dd class="text-sm font-medium text-gray-900">{{ $role->name }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
                <dt class="text-sm text-gray-500">{{ __('admin.permissions_count') }}</dt>
                <dd class="text-sm font-medium text-gray-900">{{ $role->permissions->count() }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
                <dt class="text-sm text-gray-500">{{ __('admin.created_at') }}</dt>
                <dd class="text-sm font-medium text-gray-900">{{ $role->created_at->format('Y M d') }}</dd>
            </div>
            <div class="flex justify-between py-2">
                <dt class="text-sm text-gray-500">{{ __('admin.updated_at') }}</dt>
                <dd class="text-sm font-medium text-gray-900">{{ $role->updated_at->format('Y M d') }}</dd>
            </div>
        </dl>

        @can('manage roles')
        <div class="mt-6 pt-6 border-t border-gray-200 flex items-center gap-3">
            <a href="{{ route('admin.roles.edit', $role) }}"
               class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg
                      hover:bg-primary/90 text-center font-medium text-sm">
                {{ __('admin.edit_role') }}
            </a>
        </div>
        @endcan
    </div>

    {{-- Permissions List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
            {{ __('admin.permissions') }}
        </h2>

        @if($role->permissions->count() > 0)
        <div class="space-y-2">
            @foreach($role->permissions as $permission)
            <div class="flex items-center justify-between
                        p-3 rounded-lg
                        bg-gray-50
                        border border-gray-100">
                <span class="text-sm font-medium text-gray-900">
                    {{ __('permissions.' . $permission->name) }}
                </span>
                <span class="text-xs text-gray-500 font-mono">
                    {{ $permission->name }}
                </span>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <p class="text-sm text-gray-500">
                {{ __('admin.no_permissions_assigned') }}
            </p>
        </div>
        @endif
    </div>
</div>
@endsection
