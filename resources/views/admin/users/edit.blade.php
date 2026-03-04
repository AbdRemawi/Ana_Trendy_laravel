@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.edit_user');
    $currentUserRole = $user->roles->first()?->name ?? '';
    $isAffiliate = $currentUserRole === 'affiliate';
@endphp

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.users.index') }}"
           class="p-2 text-gray-400 hover:text-gray-600
                  rounded-lg hover:bg-gray-100
                  transition-colors duration-200"
           aria-label="{{ __('admin.back_to_users') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('admin.edit_user') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.edit_user_description') }}
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Column: User Details Form --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">
                {{ __('admin.user_details') }}
            </h2>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" novalidate>
                @csrf
                @method('PUT')

                {{-- Name --}}
                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.user_name') }}
                        <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        placeholder="{{ __('admin.user_name') }}"
                        required
                        autocomplete="name"
                        class="w-full px-3 sm:px-4 py-2.5
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               {{ $errors->has('name') ? 'border-red-300' : '' }}"
                        @if($errors->has('name')) aria-invalid="true" aria-describedby="name-error" @endif
                    >
                    @error('name')
                        <p id="name-error" class="mt-1.5 text-sm text-red-600" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Mobile --}}
                <div class="mb-5">
                    <label for="mobile" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.user_mobile') }}
                        <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <input
                        type="tel"
                        id="mobile"
                        name="mobile"
                        value="{{ old('mobile', $user->mobile) }}"
                        placeholder="{{ __('admin.mobile_placeholder') }}"
                        dir="ltr"
                        required
                        autocomplete="tel"
                        class="w-full px-3 sm:px-4 py-2.5
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               {{ $errors->has('mobile') ? 'border-red-300' : '' }}"
                        @if($errors->has('mobile')) aria-invalid="true" aria-describedby="mobile-error" @endif
                    >
                    <p class="mt-1.5 text-xs text-gray-500" id="mobile-help">
                        {{ __('admin.mobile_help') }}
                    </p>
                    @error('mobile')
                        <p id="mobile-error" class="mt-1.5 text-sm text-red-600" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.user_email') }}
                        <span class="text-gray-400 text-xs">{{ __('admin.optional') }}</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        placeholder="user@example.com"
                        autocomplete="email"
                        class="w-full px-3 sm:px-4 py-2.5
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               {{ $errors->has('email') ? 'border-red-300' : '' }}"
                        @if($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif
                    >
                    @error('email')
                        <p id="email-error" class="mt-1.5 text-sm text-red-600" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password (Optional on Edit) --}}
                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.user_password') }}
                        <span class="text-gray-400 text-xs">{{ __('admin.optional') }}</span>
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="{{ __('admin.password_optional') }}"
                        autocomplete="new-password"
                        class="w-full px-3 sm:px-4 py-2.5
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               {{ $errors->has('password') ? 'border-red-300' : '' }}"
                        @if($errors->has('password')) aria-invalid="true" aria-describedby="password-error" @endif
                        aria-describedby="password-help @if($errors->has('password'))password-error @endif"
                    >
                    <p class="mt-1.5 text-xs text-gray-500" id="password-help">
                        {{ __('admin.leave_empty_keep_password') }}
                    </p>
                    @error('password')
                        <p id="password-error" class="mt-1.5 text-sm text-red-600" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Role --}}
                <div class="mb-5">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.user_role') }}
                        <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    @if($user->id === auth()->id())
                        <input type="hidden" name="role" value="{{ $currentUserRole }}">
                        <input
                            type="text"
                            id="role"
                            value="{{ $currentUserRole === 'super_admin' ? __('admin.super_admin') : $currentUserRole }}"
                            disabled
                            class="w-full px-3 sm:px-4 py-2.5
                                   rounded-lg
                                   border border-gray-200
                                   bg-gray-50
                                   cursor-not-allowed
                                   text-gray-600
                                   text-sm"
                        >
                        <p class="mt-1.5 text-xs text-gray-500">
                            {{ __('admin.cannot_modify_own_role') }}
                        </p>
                    @else
                        <select
                            id="role"
                            name="role"
                            required
                            class="w-full px-3 sm:px-4 py-2.5
                                   rounded-lg
                                   border border-gray-200
                                   focus:ring-2 focus:ring-primary/20 focus:border-primary
                                   transition-all duration-200
                                   text-sm
                                   bg-white
                                   {{ $errors->has('role') ? 'border-red-300' : '' }}"
                            @if($errors->has('role')) aria-invalid="true" aria-describedby="role-error" @endif
                        >
                            <option value="">{{ __('admin.select_role') }}</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role', $currentUserRole) === $role ? 'selected' : '' }}>
                                    @if($role === 'super_admin')
                                        {{ __('admin.super_admin') }}
                                    @else
                                        {{ $role }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('role')
                        <p id="role-error" class="mt-1.5 text-sm text-red-600" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="mb-5">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.user_status') }}
                        <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <select
                        id="status"
                        name="status"
                        required
                        class="w-full px-3 sm:px-4 py-2.5
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm
                               bg-white
                               {{ $errors->has('status') ? 'border-red-300' : '' }}"
                        @if($errors->has('status')) aria-invalid="true" aria-describedby="status-error" @endif
                    >
                        <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>
                            {{ __('admin.status_active') }}
                        </option>
                        <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>
                            {{ __('admin.status_inactive') }}
                        </option>
                        <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>
                            {{ __('admin.status_suspended') }}
                        </option>
                    </select>
                    @error('status')
                        <p id="status-error" class="mt-1.5 text-sm text-red-600" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Commission Rate (conditional field, shown for affiliates) --}}
                <div class="mb-5 {{ !$isAffiliate ? 'hidden' : '' }}" id="commission-rate-field" aria-hidden="{{ !$isAffiliate ? 'true' : 'false' }}">
                    <label for="commission_rate" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('admin.commission_rate') }}
                        <span class="text-gray-400 text-xs">{{ __('admin.optional') }}</span>
                    </label>
                    <input
                        type="number"
                        id="commission_rate"
                        name="commission_rate"
                        value="{{ old('commission_rate', $user->commission_rate) }}"
                        min="0"
                        max="100"
                        step="0.01"
                        placeholder="0.00"
                        class="w-full px-3 sm:px-4 py-2.5
                               rounded-lg
                               border border-gray-200
                               focus:ring-2 focus:ring-primary/20 focus:border-primary
                               transition-all duration-200
                               text-sm"
                        aria-describedby="commission-rate-help"
                    >
                    <p class="mt-1.5 text-xs text-gray-500" id="commission-rate-help">
                        {{ __('admin.commission_rate_help') }}
                    </p>
                </div>

                {{-- Submit Buttons --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-4">
                    <button
                        type="submit"
                        class="flex-1 px-4 py-2.5
                               bg-primary text-white
                               rounded-lg
                               hover:bg-primary/90
                               focus:ring-2 focus:ring-primary/20 focus:ring-offset-2
                               transition-all duration-200
                               font-medium text-sm">
                        {{ __('admin.update_user') }}
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                       class="px-4 py-2.5
                              border border-gray-200
                              rounded-lg
                              hover:bg-gray-50
                              transition-colors duration-200
                              font-medium text-sm text-gray-700
                              text-center">
                        {{ __('admin.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Right Column: Info & Help --}}
    <div class="lg:col-span-1">
        {{-- User Info Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="font-semibold text-gray-900 mb-4">
                {{ __('admin.current_user_info') }}
            </h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('admin.user_name') }}:</span>
                    <span class="font-medium text-gray-900">{{ $user->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('admin.user_mobile') }}:</span>
                    <span class="font-medium text-gray-900" dir="ltr">{{ $user->mobile }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('admin.user_role') }}:</span>
                    <span class="font-medium text-gray-900">
                        @if($currentUserRole === 'super_admin')
                            {{ __('admin.super_admin') }}
                        @else
                            {{ $currentUserRole }}
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('admin.user_status') }}:</span>
                    <span class="font-medium">
                        @if($user->status === 'active')
                            <span class="text-green-600">{{ __('admin.status_active') }}</span>
                        @elseif($user->status === 'inactive')
                            <span class="text-gray-600">{{ __('admin.status_inactive') }}</span>
                        @else
                            <span class="text-red-600">{{ __('admin.status_suspended') }}</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Help Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full
                            bg-blue-100
                            flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900">
                    {{ __('admin.information') }}
                </h3>
            </div>

            <div class="space-y-3 text-sm text-gray-600">
                <div>
                    <strong class="text-gray-900">{{ __('admin.password_optional') }}:</strong>
                    <p>{{ __('admin.leave_empty_keep_password') }}</p>
                </div>
                @if($user->id === auth()->id())
                <div class="bg-amber-50 rounded-lg p-3 border border-amber-100" role="alert">
                    <strong class="text-amber-900">{{ __('admin.warning') }}:</strong>
                    <p class="text-amber-800">
                        {{ __('admin.cannot_modify_own_role') }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
