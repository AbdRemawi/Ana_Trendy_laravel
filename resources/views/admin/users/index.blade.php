@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.users_management');
@endphp

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('admin.users') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.users_description') }}
            </p>
        </div>

        @can('manage users')
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2
                  bg-primary text-white
                  rounded-lg
                  hover:bg-primary/90
                  focus:ring-2 focus:ring-primary/20 focus:outline-none
                  transition-colors duration-200
                  font-medium text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('admin.create_user') }}
        </a>
        @endcan
    </div>
</div>

{{-- Search & Filter --}}
<div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-4">
        {{-- Search --}}
        <div class="flex-1">
            <label for="user-search" class="sr-only">{{ __('admin.search_users') }}</label>
            <input type="search"
                   id="user-search"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="{{ __('admin.search_users') }}"
                   aria-label="{{ __('admin.search_users') }}"
                   class="w-full px-4 py-2.5
                          rounded-lg
                          border border-gray-200
                          focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none
                          transition-all duration-200
                          text-sm">
        </div>

        {{-- Role Filter --}}
        <div class="sm:w-48">
            <label for="role-filter" class="sr-only">{{ __('admin.filter_by_role') }}</label>
            <select id="role-filter"
                    name="role"
                    aria-label="{{ __('admin.filter_by_role') }}"
                    class="w-full px-4 py-2.5
                           rounded-lg
                           border border-gray-200
                           focus:ring-2 focus:ring-primary/20 focus:border-primary focus:outline-none
                           transition-all duration-200
                           text-sm
                           bg-white">
                <option value="">{{ __('admin.all_roles') }}</option>
                @foreach($roles as $role)
                    <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>
                        {{ $role }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Submit Button --}}
        <button type="submit"
                class="px-6 py-2.5
                       bg-primary text-white
                       rounded-lg
                       hover:bg-primary/90
                       focus:ring-2 focus:ring-primary/20 focus:outline-none
                       transition-colors duration-200
                       font-medium text-sm
                       whitespace-nowrap">
            {{ __('admin.search') }}
        </button>

        @if(request()->hasAny(['search', 'role']))
        <a href="{{ route('admin.users.index') }}"
           class="px-6 py-2.5
                  border border-gray-200
                  rounded-lg
                  hover:bg-gray-50
                  focus:ring-2 focus:ring-gray-200 focus:outline-none
                  transition-colors duration-200
                  font-medium text-sm text-gray-700
                  whitespace-nowrap">
            {{ __('admin.clear') }}
        </a>
        @endif
    </form>
</div>

{{-- Users Table Card --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full" role="grid" aria-label="{{ __('admin.users_table') }}">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th scope="col" class="px-4 sm:px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.user_name') }}
                        </span>
                    </th>
                    <th scope="col" class="px-4 sm:px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.user_mobile') }}
                        </span>
                    </th>
                    <th scope="col" class="px-4 sm:px-6 py-3 hidden sm:table-cell {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.user_email') }}
                        </span>
                    </th>
                    <th scope="col" class="px-4 sm:px-6 py-3 hidden md:table-cell {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.user_role') }}
                        </span>
                    </th>
                    <th scope="col" class="px-4 sm:px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('admin.user_status') }}
                        </span>
                    </th>
                    <th scope="col" class="px-4 sm:px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="sr-only">{{ __('admin.actions') }}</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        {{-- Name --}}
                        <td class="px-4 sm:px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full
                                            bg-primary/10
                                            flex items-center justify-center
                                            text-primary font-semibold text-sm"
                                     aria-hidden="true">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $user->name }}
                                    </div>
                                    @if($user->id === auth()->id())
                                    <span class="text-xs text-gray-400">
                                        ({{ __('admin.you') }})
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Mobile --}}
                        <td class="px-4 sm:px-6 py-4">
                            <div class="text-sm text-gray-600" dir="ltr">
                                {{ $user->mobile }}
                            </div>
                        </td>

                        {{-- Email --}}
                        <td class="px-4 sm:px-6 py-4 hidden sm:table-cell">
                            @if($user->email)
                                <div class="text-sm text-gray-600">
                                    {{ $user->email }}
                                </div>
                            @else
                                <span class="text-sm text-gray-400">
                                    {{ __('admin.not_provided') }}
                                </span>
                            @endif
                        </td>

                        {{-- Role --}}
                        <td class="px-4 sm:px-6 py-4 hidden md:table-cell">
                            @if($user->roles->isNotEmpty())
                                @foreach($user->roles as $role)
                                    @if($role->name === 'super_admin')
                                        <span class="inline-flex items-center gap-1.5
                                                      px-2.5 py-1
                                                      rounded-full
                                                      text-xs font-medium
                                                      bg-amber-100 text-amber-700">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            {{ __('admin.super_admin') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center
                                                      px-2.5 py-1
                                                      rounded-full
                                                      text-xs font-medium
                                                      bg-primary/10 text-primary">
                                            {{ $role->name }}
                                        </span>
                                    @endif
                                @endforeach
                            @else
                                <span class="text-sm text-gray-400">
                                    {{ __('admin.no_role') }}
                                </span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-4 sm:px-6 py-4">
                            @if($user->status === 'active')
                                <span class="inline-flex items-center
                                              px-2.5 py-1
                                              rounded-full
                                              text-xs font-medium
                                              bg-green-100 text-green-700"
                                      role="status"
                                      aria-label="{{ __('admin.status_active') }}">
                                    {{ __('admin.status_active') }}
                                </span>
                            @elseif($user->status === 'inactive')
                                <span class="inline-flex items-center
                                              px-2.5 py-1
                                              rounded-full
                                              text-xs font-medium
                                              bg-gray-100 text-gray-700"
                                      role="status"
                                      aria-label="{{ __('admin.status_inactive') }}">
                                    {{ __('admin.status_inactive') }}
                                </span>
                            @else
                                <span class="inline-flex items-center
                                              px-2.5 py-1
                                              rounded-full
                                              text-xs font-medium
                                              bg-red-100 text-red-700"
                                      role="status"
                                      aria-label="{{ __('admin.status_suspended') }}">
                                    {{ __('admin.status_suspended') }}
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 sm:px-6 py-4">
                            <div class="flex items-center gap-2
                                        {{ $direction === 'rtl' ? 'flex-row-reverse' : '' }}"
                                 role="group"
                                 aria-label="{{ __('admin.user_actions') }}">
                                @can('manage users')
                                    {{-- View --}}
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="p-2 text-gray-400 hover:text-primary
                                              rounded-lg hover:bg-gray-100
                                              focus:ring-2 focus:ring-primary/20 focus:outline-none
                                              transition-colors duration-150"
                                       title="{{ __('admin.view') }}"
                                       aria-label="{{ __('admin.view_user', ['name' => $user->name]) }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="p-2 text-gray-400 hover:text-primary
                                              rounded-lg hover:bg-gray-100
                                              focus:ring-2 focus:ring-primary/20 focus:outline-none
                                              transition-colors duration-150"
                                       title="{{ __('admin.edit') }}"
                                       aria-label="{{ __('admin.edit_user', ['name' => $user->name]) }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    {{-- Delete (prevent self-deletion and super_admin deletion) --}}
                                    @if(!$user->hasRole('super_admin') && $user->id !== auth()->id())
                                    <button type="button"
                                            class="delete-user-btn p-2 text-gray-400 hover:text-red-600
                                                   rounded-lg hover:bg-red-50
                                                   focus:ring-2 focus:ring-red-200 focus:outline-none
                                                   transition-colors duration-150"
                                            data-url="{{ route('admin.users.destroy', $user) }}"
                                            data-item-name="{{ $user->name }}"
                                            data-modal-confirm="{{ __('admin.confirm_delete_user') }}"
                                            title="{{ __('admin.delete') }}"
                                            aria-label="{{ __('admin.delete_user', ['name' => $user->name]) }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
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
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 rounded-full
                                            bg-gray-100
                                            flex items-center justify-center
                                            mb-4"
                                     aria-hidden="true">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">
                                    {{ __('admin.no_users_found') }}
                                </h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    {{ __('admin.no_users_description') }}
                                </p>
                                @can('manage users')
                                <a href="{{ route('admin.users.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2
                                          bg-primary text-white
                                          rounded-lg
                                          hover:bg-primary/90
                                          focus:ring-2 focus:ring-primary/20 focus:outline-none
                                          transition-colors duration-200
                                          font-medium text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    {{ __('admin.create_first_user') }}
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
    @if($users->hasPages())
    <div class="px-4 sm:px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="text-sm text-gray-600" role="status" aria-live="polite">
            {{ __('admin.showing', ['from' => $users->firstItem(), 'to' => $users->lastItem(), 'total' => $users->total()]) }}
        </div>
        {{ $users->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
