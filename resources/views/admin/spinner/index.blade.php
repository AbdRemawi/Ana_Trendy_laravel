@extends('layouts.dashboard')

@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ __('app.spinner') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('app.spinner_page_description') }}
            </p>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="text-xs font-medium text-gray-500">{{ __('app.spinner_total_plays') }}</div>
        <div class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($stats['total']) }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="text-xs font-medium text-gray-500">{{ __('app.spinner_wins') }}</div>
        <div class="mt-1 text-2xl font-semibold text-green-600">{{ number_format($stats['wins']) }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="text-xs font-medium text-gray-500">{{ __('app.spinner_redeemed') }}</div>
        <div class="mt-1 text-2xl font-semibold text-primary">{{ number_format($stats['redeemed']) }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="text-xs font-medium text-gray-500">{{ __('app.spinner_today') }}</div>
        <div class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($stats['today']) }}</div>
    </div>
</div>

{{-- Filters --}}
<div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="{{ route('admin.spinner.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="{{ __('app.spinner_search_placeholder') }}"
                       class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200 text-sm">
            </div>
            <div>
                <select name="result" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200 text-sm bg-white">
                    <option value="">{{ __('app.spinner_all_results') }}</option>
                    <option value="win" {{ request('result') == 'win' ? 'selected' : '' }}>{{ __('app.spinner_result_win') }}</option>
                    <option value="redeemed" {{ request('result') == 'redeemed' ? 'selected' : '' }}>{{ __('app.spinner_result_redeemed') }}</option>
                    <option value="try_again" {{ request('result') == 'try_again' ? 'selected' : '' }}>{{ __('app.spinner_result_try_again') }}</option>
                    <option value="no_prize" {{ request('result') == 'no_prize' ? 'selected' : '' }}>{{ __('app.spinner_result_no_prize') }}</option>
                </select>
            </div>
            <div class="flex gap-2">
                <select name="date" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200 text-sm bg-white">
                    <option value="">{{ __('app.spinner_all_time') }}</option>
                    <option value="today" {{ request('date') == 'today' ? 'selected' : '' }}>{{ __('app.spinner_today') }}</option>
                    <option value="week" {{ request('date') == 'week' ? 'selected' : '' }}>{{ __('app.spinner_this_week') }}</option>
                    <option value="month" {{ request('date') == 'month' ? 'selected' : '' }}>{{ __('app.spinner_this_month') }}</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm font-medium whitespace-nowrap">
                    {{ __('app.spinner_filter') }}
                </button>
            </div>
        </div>
        @if(request()->hasAny(['search', 'result', 'date']))
        <div class="mt-3">
            <a href="{{ route('admin.spinner.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-sm text-gray-700">
                {{ __('app.spinner_clear') }}
            </a>
        </div>
        @endif
    </form>
</div>

{{-- Spins Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    @foreach([
                        __('app.spinner_col_date'),
                        __('app.spinner_col_phone'),
                        __('app.spinner_col_prize'),
                        __('app.spinner_col_coupon'),
                        __('app.spinner_col_status'),
                        __('app.spinner_col_device'),
                    ] as $heading)
                    <th class="px-6 py-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ $heading }}</span>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($spins as $spin)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        {{-- Date --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $spin->created_at->format('Y-m-d') }}</div>
                            <div class="text-xs text-gray-400">{{ $spin->created_at->format('H:i') }}</div>
                        </td>

                        {{-- Phone --}}
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900" dir="ltr">
                                {{ $spin->phone_number ?? '—' }}
                            </div>
                        </td>

                        {{-- Prize --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                @if($spin->prize?->color)
                                    <span class="w-3 h-3 rounded-full flex-shrink-0" style="background: {{ $spin->prize->color }}"></span>
                                @endif
                                <span class="text-sm text-gray-900">{{ $spin->prize_label }}</span>
                            </div>
                        </td>

                        {{-- Coupon --}}
                        <td class="px-6 py-4">
                            @if($spin->coupon)
                                <span class="text-sm font-mono font-semibold text-gray-900">{{ $spin->coupon->code }}</span>
                                <div class="text-xs text-gray-500">
                                    @if($spin->coupon->type === 'percentage')
                                        {{ number_format($spin->coupon->value, 0) }}%
                                    @else
                                        {{ number_format($spin->coupon->value, 2) }} JOD
                                    @endif
                                </div>
                            @else
                                <span class="text-sm text-gray-400">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4">
                            @if($spin->is_redeemed)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                    {{ __('app.spinner_status_redeemed') }}
                                </span>
                            @elseif($spin->is_win)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    {{ __('app.spinner_status_won') }}
                                </span>
                            @elseif($spin->can_respin)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                    {{ __('app.spinner_status_try_again') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    {{ __('app.spinner_status_no_prize') }}
                                </span>
                            @endif
                        </td>

                        {{-- Device --}}
                        <td class="px-6 py-4">
                            <span class="text-xs font-mono text-gray-400" title="{{ $spin->device_id }}">
                                {{ \Illuminate\Support\Str::limit($spin->device_id, 12) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 0v10l6.5 3.75"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">{{ __('app.spinner_empty_title') }}</h3>
                                <p class="text-sm text-gray-500">{{ __('app.spinner_empty_description') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($spins->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-600">
            {{ __('admin.showing', ['from' => $spins->firstItem(), 'to' => $spins->lastItem(), 'total' => $spins->total()]) }}
        </div>
        {{ $spins->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
