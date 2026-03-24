@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.user_details');
    $userRole = $user->roles->first()?->name ?? '';
@endphp

<div class="mb-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.users.index') }}"
           class="p-2 text-gray-400 hover:text-gray-600
                  rounded-lg hover:bg-gray-100
                  focus:ring-2 focus:ring-primary/20 focus:outline-none
                  transition-colors duration-200"
           aria-label="{{ __('admin.back_to_users') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('admin.user_details') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.viewing_user_details', ['name' => $user->name]) }}
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="p-4 sm:p-6 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full
                                bg-primary/10
                                flex items-center justify-center
                                text-primary font-semibold text-2xl"
                         aria-hidden="true">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold text-gray-900">
                            {{ $user->name }}
                            @if($user->id === auth()->id())
                            <span class="ml-2 text-sm font-normal text-gray-400">
                                ({{ __('admin.you') }})
                            </span>
                            @endif
                        </h2>
                        <p class="text-sm text-gray-500" dir="ltr">
                            {{ $user->mobile }}
                        </p>
                    </div>
                    @if($userRole === 'super_admin')
                    <span class="inline-flex items-center gap-1.5
                                  px-3 py-1.5
                                  rounded-full
                                  text-sm font-medium
                                  bg-amber-100 text-amber-700"
                          role="status">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        {{ __('admin.super_admin') }}
                    </span>
                    @endif
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">
                    {{ __('admin.user_information') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('admin.user_name') }}
                        </label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $user->name }}
                        </p>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('admin.user_mobile') }}
                        </label>
                        <p class="mt-1 text-sm text-gray-900" dir="ltr">
                            {{ $user->mobile }}
                        </p>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('admin.user_email') }}
                        </label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($user->email)
                                {{ $user->email }}
                            @else
                                <span class="text-gray-400">{{ __('admin.not_provided') }}</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('admin.user_status') }}
                        </label>
                        <p class="mt-1" role="status">
                            @if($user->status === 'active')
                                <span class="inline-flex items-center
                                              px-2.5 py-1
                                              rounded-full
                                              text-xs font-medium
                                              bg-green-100 text-green-700">
                                    {{ __('admin.status_active') }}
                                </span>
                            @elseif($user->status === 'inactive')
                                <span class="inline-flex items-center
                                              px-2.5 py-1
                                              rounded-full
                                              text-xs font-medium
                                              bg-gray-100 text-gray-700">
                                    {{ __('admin.status_inactive') }}
                                </span>
                            @else
                                <span class="inline-flex items-center
                                              px-2.5 py-1
                                              rounded-full
                                              text-xs font-medium
                                              bg-red-100 text-red-700">
                                    {{ __('admin.status_suspended') }}
                                </span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('admin.user_role') }}
                        </label>
                        <p class="mt-1" role="status">
                            @if($userRole)
                                @if($userRole === 'super_admin')
                                    <span class="inline-flex items-center
                                                  px-2.5 py-1
                                                  rounded-full
                                                  text-xs font-medium
                                                  bg-amber-100 text-amber-700">
                                        {{ __('admin.super_admin') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center
                                                  px-2.5 py-1
                                                  rounded-full
                                                  text-xs font-medium
                                                  bg-primary/10 text-primary">
                                        {{ $userRole }}
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400">{{ __('admin.no_role') }}</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('admin.created_at') }}
                        </label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $user->created_at->format('Y M d H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">
                {{ __('admin.actions') }}
            </h3>
            <div class="flex flex-col sm:flex-row flex-wrap gap-3">
                @can('manage users')
                    <a href="{{ route('admin.users.edit', $user) }}"
                       class="inline-flex items-center gap-2 px-4 py-2
                              bg-primary text-white
                              rounded-lg
                              hover:bg-primary/90
                              focus:ring-2 focus:ring-primary/20 focus:outline-none
                              transition-colors duration-200
                              font-medium text-sm"
                       aria-label="{{ __('admin.edit_user') }}: {{ $user->name }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{ __('admin.edit_user') }}
                    </a>

                    @if(!$user->hasRole('super_admin') && $user->id !== auth()->id())
                    <button type="button"
                            class="delete-user-btn inline-flex items-center gap-2 px-4 py-2
                                   bg-red-600 text-white
                                   rounded-lg
                                   hover:bg-red-700
                                   focus:ring-2 focus:ring-red-500/20 focus:outline-none
                                   transition-colors duration-200
                                   font-medium text-sm"
                            data-url="{{ route('admin.users.destroy', $user) }}"
                            data-item-name="{{ $user->name }}"
                            data-modal-confirm="{{ __('admin.confirm_delete_user') }}"
                            aria-label="{{ __('admin.delete_user') }}: {{ $user->name }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{ __('admin.delete_user') }}
                    </button>
                    @endif
                @endcan
            </div>
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">
                {{ __('admin.permissions') }}
            </h3>
            @if($user->permissions->isNotEmpty())
                <div class="space-y-2">
                    @foreach($user->permissions->take(10) as $permission)
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm text-gray-700">
                            {{ $permission->name }}
                        </span>
                    </div>
                    @endforeach
                    @if($user->permissions->count() > 10)
                    <p class="text-xs text-gray-500">
                        + {{ $user->permissions->count() - 10 }} {{ __('admin.more_permissions') }}
                    </p>
                    @endif
                </div>
            @else
                <p class="text-sm text-gray-400">
                    {{ __('admin.no_direct_permissions') }}
                </p>
            @endif
        </div>

        @if($user->roles->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">
                {{ __('admin.role_permissions') }}
            </h3>
            <div class="space-y-2">
                @foreach($user->roles as $role)
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full
                                bg-primary/10
                                flex items-center justify-center
                                text-primary text-xs font-semibold"
                         aria-hidden="true">
                        {{ strtoupper(substr($role->name, 0, 1)) }}
                    </div>
                    <span class="text-sm text-gray-700">
                        {{ $role->name }}
                    </span>
                    <span class="text-xs text-gray-400">
                        ({{ $role->permissions->count() }} {{ __('admin.permissions') }})
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
