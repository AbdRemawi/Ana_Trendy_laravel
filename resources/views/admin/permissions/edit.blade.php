@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.edit_permission');
@endphp

<div class="max-w-2xl mx-auto">
    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.permissions.index') }}"
               class="p-2 text-gray-400 hover:text-gray-600
                      rounded-lg hover:bg-gray-100
                      transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ __('admin.edit_permission') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $permission->name }}
                </p>
            </div>
        </div>
    </div>

    {{-- Usage Warning --}}
    @if($permission->roles_count > 0)
    <div class="mb-6 p-4 bg-amber-50 border border-amber-100 rounded-lg">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="text-sm text-amber-800">
                <strong>{{ __('admin.in_use') }}:</strong>
                {{ __('admin.permission_in_use_description', ['count' => $permission->roles_count]) }}
            </div>
        </div>
    </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.permissions.update', $permission) }}">
            @csrf
            @method('PUT')

            {{-- Permission Name --}}
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                    {{ __('admin.permission_name') }}
                    <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $permission->name) }}"
                    placeholder="manage products"
                    class="w-full px-4 py-2.5
                           rounded-lg
                           border border-gray-200
                           focus:ring-2 focus:ring-primary/20 focus:border-primary
                           transition-all duration-200
                           text-sm
                           {{ $errors->has('name') ? 'border-red-300' : '' }}"
                    @if($errors->has('name')) aria-invalid="true" aria-describedby="name-error" @endif
                >
                <p class="mt-1.5 text-xs text-gray-500">
                    {{ __('admin.permission_name_help') }}
                </p>
                @error('name')
                    <p id="name-error" class="mt-1.5 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Roles Using This Permission --}}
            @if($permission->roles_count > 0)
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-2">
                    {{ __('admin.roles_using_permission') }}
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($permission->roles as $role)
                    <span class="inline-flex items-center px-2.5 py-1
                                  rounded-full text-xs font-medium
                                  bg-primary/10 text-primary">
                        {{ $role->name }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                <button
                    type="submit"
                    class="flex-1 px-4 py-2.5
                           bg-primary text-white
                           rounded-lg
                           hover:bg-primary/90
                           focus:ring-2 focus:ring-primary/20
                           transition-all duration-200
                           font-medium text-sm">
                    {{ __('admin.update_permission') }}
                </button>
                <a href="{{ route('admin.permissions.index') }}"
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
@endsection
