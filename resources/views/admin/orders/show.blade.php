@extends('layouts.dashboard')

@section('content')
@php
    $pageTitle = __('admin.order_details') . ' - ' . $order->order_number;
@endphp

{{-- Back Button --}}
<div class="mb-6">
    <a href="{{ route('admin.orders.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900
              transition-colors duration-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        {{ __('admin.back') }}
    </a>
</div>

 {{-- Page Header --}}
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ $order->order_number }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('admin.order_date') }}: {{ $order->created_at->format('Y-m-d H:i') }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            @if($order->isProcessing())
                @can('manage orders')
                    <a href="{{ route('admin.orders.edit', $order) }}"
                       class="inline-flex items-center gap-2 px-4 py-2
                              bg-primary text-white
                              rounded-lg
                              hover:bg-primary/90
                              transition-colors duration-200
                              font-medium text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{ __('admin.edit_order') }}
                    </a>
                @endcan
            @endif
        </div>
    </div>
</div>

{{-- Order Details Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Column: Order Info & Items --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Customer Information --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                {{ __('admin.customer_name') }}
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <div class="text-sm text-gray-500 mb-1">{{ __('admin.customer_name') }}</div>
                    <div class="text-sm font-medium text-gray-900">{{ $order->full_name }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500 mb-1">{{ __('admin.order_phone_numbers') }}</div>
                    <div class="text-sm font-medium text-gray-900">
                        @foreach($order->mobiles as $mobile)
                            {{ $mobile->phone_number }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <div class="text-sm text-gray-500 mb-1">{{ __('admin.customer_address') }}</div>
                    <div class="text-sm font-medium text-gray-900">{{ $order->address }}</div>
                </div>
            </div>
        </div>

        {{-- Order Items --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                {{ __('admin.order_items') }}
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-100">
                        <tr>
                            <th class="pb-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                                <span class="text-xs font-semibold text-gray-600 uppercase">
                                    {{ __('admin.item_product') }}
                                </span>
                            </th>
                            <th class="pb-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                                <span class="text-xs font-semibold text-gray-600 uppercase">
                                    {{ __('admin.item_quantity') }}
                                </span>
                            </th>
                            <th class="pb-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                                <span class="text-xs font-semibold text-gray-600 uppercase">
                                    {{ __('admin.item_base_price') }}
                                </span>
                            </th>
                            <th class="pb-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                                <span class="text-xs font-semibold text-gray-600 uppercase">
                                    {{ __('admin.item_discount') }}
                                </span>
                            </th>
                            <th class="pb-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                                <span class="text-xs font-semibold text-gray-600 uppercase">
                                    {{ __('admin.item_final_price') }}
                                </span>
                            </th>
                            <th class="pb-3 {{ $direction === 'rtl' ? 'text-right' : 'text-left' }}">
                                <span class="text-xs font-semibold text-gray-600 uppercase">
                                    {{ __('admin.item_total') }}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        @if($item->product->primaryImage && $item->product->primaryImage->first())
                                            <img src="{{ $item->product->primaryImage->first()->image_url }}"
                                                 alt="{{ $item->product->name }}"
                                                 class="w-12 h-12 object-cover rounded-lg border border-gray-200">
                                        @else
                                            <div class="w-12 h-12 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $item->product->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                Cost: {{ number_format($item->product->cost_price, 2) }} JOD
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="text-sm text-gray-600">{{ $item->quantity }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="text-sm text-gray-600">
                                        {{ number_format($item->base_price, 2) }} JOD
                                    </div>
                                </td>
                                <td class="py-3">
                                    @if($item->coupon_discount_per_unit > 0)
                                        <div class="text-sm text-red-600">
                                            -{{ number_format($item->coupon_discount_per_unit, 2) }} JOD
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-400">-</div>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ number_format($item->unit_sale_price, 2) }} JOD
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ number_format($item->total_price, 2) }} JOD
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right Column: Status & Financials --}}
    <div class="space-y-6">
        {{-- Order Status --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                {{ __('admin.order_status') }}
            </h2>

            <div class="mb-4">
                @switch($order->status)
                    @case('processing')
                        <span class="inline-flex items-center
                                      px-3 py-1.5
                                      rounded-full
                                      text-sm font-medium
                                      bg-blue-100 text-blue-700">
                            {{ __('admin.status_processing') }}
                        </span>
                    @break

                    @case('with_delivery_company')
                        <span class="inline-flex items-center
                                      px-3 py-1.5
                                      rounded-full
                                      text-sm font-medium
                                      bg-yellow-100 text-yellow-700">
                            {{ __('admin.status_with_delivery_company') }}
                        </span>
                    @break

                    @case('received')
                        <span class="inline-flex items-center
                                      px-3 py-1.5
                                      rounded-full
                                      text-sm font-medium
                                      bg-green-100 text-green-700">
                            {{ __('admin.status_received') }}
                        </span>
                    @break

                    @case('cancelled')
                        <span class="inline-flex items-center
                                      px-3 py-1.5
                                      rounded-full
                                      text-sm font-medium
                                      bg-red-100 text-red-700">
                            {{ __('admin.status_cancelled') }}
                        </span>
                    @break

                    @case('returned')
                        <span class="inline-flex items-center
                                      px-3 py-1.5
                                      rounded-full
                                      text-sm font-medium
                                      bg-orange-100 text-orange-700">
                            {{ __('admin.status_returned') }}
                        </span>
                    @break
                @endswitch
            </div>

            @can('manage orders')
                {{-- Update Status Form --}}
                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-3">
                        <select name="status"
                                class="w-full px-3 py-2
                                       rounded-lg
                                       border border-gray-200
                                       focus:ring-2 focus:ring-primary/20 focus:border-primary
                                       transition-all duration-200
                                       text-sm
                                       bg-white">
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>
                                {{ __('admin.status_processing') }}
                            </option>
                            <option value="with_delivery_company" {{ $order->status === 'with_delivery_company' ? 'selected' : '' }}>
                                {{ __('admin.status_with_delivery_company') }}
                            </option>
                            <option value="received" {{ $order->status === 'received' ? 'selected' : '' }}>
                                {{ __('admin.status_received') }}
                            </option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>
                                {{ __('admin.status_cancelled') }}
                            </option>
                            <option value="returned" {{ $order->status === 'returned' ? 'selected' : '' }}>
                                {{ __('admin.status_returned') }}
                            </option>
                        </select>
                        <button type="submit"
                                class="w-full px-4 py-2
                                       bg-primary text-white
                                       rounded-lg
                                       hover:bg-primary/90
                                       transition-colors duration-200
                                       font-medium text-sm">
                            {{ __('admin.update_status') }}
                        </button>
                    </div>
                </form>
            @endcan
        </div>

        {{-- Assign Courier (for processing orders without courier) --}}
        @if($order->isProcessing() && !$order->hasCourier())
            @can('manage orders')
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        {{ __('admin.assign_courier') }}
                    </h2>
                    <form method="POST" action="{{ route('admin.orders.assign-courier', $order) }}">
                        @csrf
                        <div class="space-y-3">
                            <select name="delivery_courier_id"
                                    required
                                    class="w-full px-3 py-2
                                           rounded-lg
                                           border border-gray-200
                                           focus:ring-2 focus:ring-primary/20 focus:border-primary
                                           transition-all duration-200
                                           text-sm
                                           bg-white">
                                <option value="">{{ __('admin.select_courier') }}</option>
                                @foreach(\App\Models\DeliveryCourier::active()->get() as $courier)
                                    <option value="{{ $courier->id }}">
                                        {{ $courier->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit"
                                    class="w-full px-4 py-2
                                           bg-green-600 text-white
                                           rounded-lg
                                           hover:bg-green-700
                                           transition-colors duration-200
                                           font-medium text-sm">
                                {{ __('admin.assign_courier') }}
                            </button>
                        </div>
                    </form>
                </div>
            @endcan
        @endif

        {{-- Financial Summary --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                {{ __('admin.total_price') }}
            </h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">{{ __('admin.subtotal_products') }}</span>
                    <span class="text-sm font-medium text-gray-900">
                        {{ number_format($order->subtotal_products, 2) }} JOD
                    </span>
                </div>

                @if($order->coupon_discount_amount > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ __('admin.coupon_discount') }}</span>
                        <span class="text-sm font-medium text-red-600">
                            -{{ number_format($order->coupon_discount_amount, 2) }} JOD
                        </span>
                    </div>
                @endif

                @if($order->real_delivery_fee)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ __('admin.real_delivery_fee') }}</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ number_format($order->real_delivery_fee, 3) }} JOD
                        </span>
                    </div>
                @endif

                <div class="border-t border-gray-200 pt-3">
                    <div class="flex justify-between items-center">
                        <span class="text-base font-semibold text-gray-900">{{ __('admin.total_price') }}</span>
                        <span class="text-base font-bold text-primary">
                            {{ number_format($order->total_price_for_customer, 2) }} JOD
                        </span>
                    </div>
                </div>

                {{-- Profit --}}
                <div class="border-t border-gray-200 pt-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ __('admin.order_profit') }}</span>
                        <span class="text-sm font-semibold {{ $order->profit > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($order->profit, 2) }} JOD
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        @if($order->notes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">
                    {{ __('admin.order_notes') }}
                </h2>
                <p class="text-sm text-gray-600">{{ $order->notes }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
