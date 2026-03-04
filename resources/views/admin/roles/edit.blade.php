@extends('layouts.dashboard')

@section('content')
@php
    $isEditing = true;
    $formAction = route('admin.roles.update', $role);
    $formMethod = 'PUT';

    // Dashboard permission that should always be active
    $dashboardPermission = 'view dashboard';
@endphp

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.roles.index') }}"
           class="p-2 text-gray-400 hover:text-gray-600
                  rounded-lg hover:bg-gray-100
                  transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('admin.edit_role') }}: {{ $role->name }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.edit_role_description') }}
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Column: Role Details (Read-only) --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                {{ __('admin.role_details') }}
            </h2>

            <form id="details-form" method="POST" action="{{ $formAction }}">
                @csrf
                @method('PUT')

                {{-- Role Name (Read-only) --}}
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.role_name') }}
                    </label>
                    <div class="w-full px-4 py-2.5
                           rounded-lg
                           border border-gray-200
                           bg-gray-50
                           text-gray-600
                           text-sm
                           cursor-not-allowed">
                        {{ $role->name }}
                    </div>
                    <input type="hidden" name="name" value="{{ $role->name }}">
                    <p class="mt-1.5 text-xs text-gray-500">
                        {{ __('admin.role_name_readonly') }}
                    </p>
                </div>

                {{-- Submit Button --}}
                <div class="flex items-center gap-3 pt-2">
                    <button
                        type="submit"
                        form="permissions-form"
                        class="flex-1 px-4 py-2.5
                               bg-primary text-white
                               rounded-lg
                               hover:bg-primary/90
                               focus:ring-2 focus:ring-primary/20
                               transition-all duration-200
                               font-medium text-sm">
                        {{ __('admin.update_role') }}
                    </button>
                    <a href="{{ route('admin.roles.index') }}"
                       class="px-4 py-2.5
                              border border-gray-200
                              rounded-lg
                              hover:bg-gray-50
                              transition-colors duration-200
                              font-medium text-sm text-gray-700">
                        {{ __('admin.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Right Column: Permissions Assignment --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ __('admin.permissions') }}
                </h2>
                <span class="text-sm text-gray-500">
                    {{ count($rolePermissions) }} / {{ count($allPermissions) }} {{ __('admin.assigned') }}
                </span>
            </div>

            <p class="text-sm text-gray-500 mb-6">
                {{ __('admin.toggle_permissions_help') }}
            </p>

            {{-- Permission Groups Form --}}
            <form id="permissions-form" method="POST" action="{{ $formAction }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="name" value="{{ old('name', $role->name) }}">

                @php
                    // Group permissions by category
                    $permissionGroups = [
                        'dashboard' => [
                            'icon' => 'M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z',
                            'title' => __('admin.dashboard'),
                            'permissions' => ['view dashboard']
                        ],
                        'users' => [
                            'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                            'title' => __('admin.users'),
                            'permissions' => ['view users', 'manage users', 'delete users']
                        ],
                        'products' => [
                            'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                            'title' => __('admin.products'),
                            'permissions' => ['view products', 'manage products', 'delete products']
                        ],
                        'orders' => [
                            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                            'title' => __('admin.orders'),
                            'permissions' => ['view orders', 'manage orders', 'delete orders']
                        ],
                        'affiliate' => [
                            'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                            'title' => __('admin.affiliate'),
                            'permissions' => ['view commissions', 'view own performance', 'manage own coupon']
                        ],
                        'system' => [
                            'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                            'title' => __('admin.system'),
                            'permissions' => ['manage roles', 'manage permissions', 'view system config']
                        ],
                    ];
                @endphp

                <div class="space-y-6">
                    @foreach($permissionGroups as $groupKey => $group)
                        @php
                            $groupPermissions = array_intersect($group['permissions'], $allPermissions);
                            if (empty($groupPermissions)) continue;
                        @endphp

                        {{-- Permission Group --}}
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            {{-- Group Header --}}
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200
                                        flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $group['icon'] }}"/>
                                </svg>
                                <h3 class="font-medium text-gray-900">
                                    {{ $group['title'] }}
                                </h3>
                            </div>

                            {{-- Permissions List --}}
                            <div class="p-4 space-y-2">
                                @foreach($groupPermissions as $permission)
                                    @if($permission === $dashboardPermission)
                                        {{-- Dashboard permission: Always enabled, read-only --}}
                                        <div class="flex items-start gap-3
                                                    {{ $direction === 'rtl' ? 'flex-row-reverse' : '' }}
                                                    p-3 rounded-lg bg-primary/5 border border-primary/20
                                                    transition-colors duration-200">
                                            {{-- Locked Toggle Indicator --}}
                                            <div class="relative flex-shrink-0 mt-0.5">
                                                <div class="block w-11 h-6
                                                           rounded-full
                                                           bg-primary
                                                           cursor-not-allowed
                                                           opacity-75
                                                           relative">
                                                    <span class="absolute
                                                                  top-0.5
                                                                  {{ $direction === 'rtl' ? 'right-0.5' : 'left-0.5' }}
                                                                  w-5 h-5
                                                                  bg-white
                                                                  rounded-full
                                                                  shadow-sm
                                                                  {{ $direction === 'rtl' ? 'translate-x-5' : '' }}
                                                                  {{ $direction === 'rtl' ? '' : 'translate-x-5' }}">
                                                    </span>
                                                </div>
                                                {{-- Lock Icon Overlay --}}
                                                <svg class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-4 h-4 text-white/80 pointer-events-none"
                                                     fill="currentColor"
                                                     viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>

                                            {{-- Label & Description --}}
                                            <div class="flex-1 min-w-0">
                                                <label class="block text-sm font-semibold text-gray-900">
                                                    {{ __('permissions.' . $permission) }}
                                                </label>
                                                <p class="mt-0.5 text-xs text-primary/70 font-medium">
                                                    {{ __('admin.dashboard_permission_required') }}
                                                </p>
                                            </div>

                                            {{-- Hidden input to always submit dashboard permission --}}
                                            <input type="hidden" name="permissions[]" value="{{ $permission }}">
                                        </div>
                                    @else
                                        {{-- Regular permissions: Editable --}}
                                        <x-admin.permission-toggle
                                            :permission="$permission"
                                            :label="__('permissions.' . $permission)"
                                            :checked="in_array($permission, $rolePermissions)"
                                            :name="'permissions[]'"
                                        />
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Sync Permissions Button --}}
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <button
                        type="submit"
                        form="permissions-form"
                        class="w-full px-4 py-3
                               bg-primary text-white
                               rounded-lg
                               hover:bg-primary/90
                               focus:ring-2 focus:ring-primary/20
                               transition-all duration-200
                               font-medium text-sm
                               flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ __('admin.sync_permissions') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
