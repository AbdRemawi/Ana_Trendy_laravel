@extends('layouts.dashboard')

@section('content')
    {{-- Header with Filter --}}
    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-semibold text-primary/90">{{ __('dashboard.title') }}</h2>
            <p class="text-sm text-primary/60 mt-1">{{ $filterLabel }}</p>
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                <select name="filter" onchange="this.form.submit()" class="rounded-lg border border-accent-light bg-white px-3 py-2 text-sm text-primary focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">
                    <option value="today" {{ $currentFilter === 'today' ? 'selected' : '' }}>{{ __('dashboard.filter_today') }}</option>
                    <option value="week" {{ $currentFilter === 'week' ? 'selected' : '' }}>{{ __('dashboard.filter_week') }}</option>
                    <option value="month" {{ $currentFilter === 'month' ? 'selected' : '' }}>{{ __('dashboard.filter_month') }}</option>
                    <option value="all" {{ $currentFilter === 'all' ? 'selected' : '' }}>{{ __('dashboard.filter_all') }}</option>
                </select>
            </form>
        </div>
    </div>

    {{-- Top Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
        {{-- Total Revenue --}}
        <x-dashboard.stat-card
            :title="__('dashboard.total_revenue')"
            :value="'JOD ' . number_format($totalRevenue, 2)"
            icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            color="success"
        />

        {{-- Net Profit --}}
        <x-dashboard.stat-card
            :title="__('dashboard.net_profit')"
            :value="'JOD ' . number_format($netProfit, 2)"
            icon="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
            :color="$netProfit >= 0 ? 'success' : 'danger'"
        />

        {{-- Total Orders --}}
        <x-dashboard.stat-card
            :title="__('dashboard.total_orders')"
            :value="$totalOrders"
            icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
            color="primary"
        />

        {{-- Profit Margin --}}
        @php
            $marginColor = $profitMargin >= 25 ? 'success' : ($profitMargin >= 10 ? 'warning' : 'danger');
        @endphp
        <x-dashboard.stat-card
            :title="__('dashboard.profit_margin')"
            :value="number_format($profitMargin, 1) . '%'"
            icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
            :color="$marginColor"
        />
    </div>

    {{-- Order Status & Revenue/Profit Breakdown --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Order Status Overview --}}
        <div class="bg-white rounded-xl border border-accent-light shadow-sm p-6">
            <h3 class="text-lg font-semibold text-primary/80 mb-4">{{ __('dashboard.order_status_overview') }}</h3>
            <div class="space-y-3">
                @foreach([
                    'received' => __('dashboard.status_delivered'),
                    'with_delivery_company' => __('dashboard.status_in_transit'),
                    'processing' => __('dashboard.status_pending'),
                    'cancelled' => __('dashboard.status_cancelled'),
                    'returned' => __('dashboard.status_returned')
                ] as $key => $label)
                    @php
                        $status = $orderStatusBreakdown[$key];
                        $count = $status->order_count ?? 0;
                        $value = $status->total_value ?? 0;
                    @endphp
                    <div class="flex items-center justify-between py-2 border-b border-accent-light/50 last:border-0">
                        <div class="flex items-center gap-3">
                            @switch($key)
                                @case('received')
                                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                                    @break
                                @case('with_delivery_company')
                                    <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                                    @break
                                @case('processing')
                                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                                    @break
                                @case('cancelled')
                                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                                    @break
                                @case('returned')
                                    <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                                    @break
                            @endswitch
                            <span class="text-sm text-primary/70">{{ $label }}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-medium text-primary">{{ $count }}</span>
                            <span class="text-sm text-primary/60">{{ $value > 0 ? 'JOD ' . number_format($value, 2) : '-' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Revenue & Profit Breakdown --}}
        <div class="bg-white rounded-xl border border-accent-light shadow-sm p-6">
            <h3 class="text-lg font-semibold text-primary/80 mb-4">{{ __('dashboard.revenue_profit_breakdown') }}</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-2 border-b border-accent-light/50">
                    <span class="text-primary/60">{{ __('dashboard.product_revenue') }}</span>
                    <span class="font-medium text-primary">JOD {{ number_format($revenueProfitBreakdown['product_revenue'], 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-accent-light/50">
                    <span class="text-primary/60">{{ __('dashboard.product_cost') }}</span>
                    <span class="font-medium text-red-600">-JOD {{ number_format($revenueProfitBreakdown['product_cost'], 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-accent-light/50 bg-green-50/50 px-2 rounded">
                    <span class="text-primary/70">{{ __('dashboard.product_profit') }}</span>
                    <span class="font-semibold text-green-600">JOD {{ number_format($revenueProfitBreakdown['product_profit'], 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-accent-light/50">
                    <span class="text-primary/60">{{ __('dashboard.delivery_revenue') }}</span>
                    <span class="font-medium text-primary">JOD {{ number_format($revenueProfitBreakdown['delivery_revenue'], 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-accent-light/50">
                    <span class="text-primary/60">{{ __('dashboard.delivery_cost') }}</span>
                    <span class="font-medium text-red-600">-JOD {{ number_format($revenueProfitBreakdown['delivery_cost'], 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-accent-light/50 bg-green-50/50 px-2 rounded">
                    <span class="text-primary/70">{{ __('dashboard.delivery_profit') }}</span>
                    <span class="font-semibold text-green-600">JOD {{ number_format($revenueProfitBreakdown['delivery_profit'], 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-accent-light/50">
                    <span class="text-primary/60">{{ __('dashboard.product_discounts') }}</span>
                    <span class="font-medium text-red-600">-JOD {{ number_format($revenueProfitBreakdown['product_discounts'], 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-accent-light/50">
                    <span class="text-primary/60">{{ __('dashboard.delivery_discounts') }}</span>
                    <span class="font-medium text-red-600">-JOD {{ number_format($revenueProfitBreakdown['delivery_discounts'], 2) }}</span>
                </div>
                <div class="flex justify-between py-3 bg-primary/5 px-3 rounded-lg mt-2">
                    <span class="font-semibold text-primary/80">{{ __('dashboard.net_profit_caps') }}</span>
                    <span class="font-bold text-lg {{ $revenueProfitBreakdown['net_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        JOD {{ number_format($revenueProfitBreakdown['net_profit'], 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Orders Table --}}
    <div class="bg-white rounded-xl border border-accent-light shadow-sm p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-primary/80">{{ __('dashboard.recent_orders') }}</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-accent hover:text-accent/80">{{ __('dashboard.view_all') }}</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-accent-light">
                        <th class="text-start py-3 px-2 font-medium text-primary/60">{{ __('dashboard.col_order') }}</th>
                        <th class="text-start py-3 px-2 font-medium text-primary/60">{{ __('dashboard.col_customer') }}</th>
                        <th class="text-start py-3 px-2 font-medium text-primary/60">{{ __('dashboard.col_city') }}</th>
                        <th class="text-start py-3 px-2 font-medium text-primary/60">{{ __('dashboard.col_status') }}</th>
                        <th class="text-start py-3 px-2 font-medium text-primary/60">{{ __('dashboard.col_items') }}</th>
                        <th class="text-end py-3 px-2 font-medium text-primary/60">{{ __('dashboard.col_total') }}</th>
                        <th class="text-start py-3 px-2 font-medium text-primary/60">{{ __('dashboard.col_date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr class="border-b border-accent-light/50 hover:bg-accent-light/20">
                            <td class="py-3 px-2 font-medium text-primary">{{ $order['order_number'] }}</td>
                            <td class="py-3 px-2 text-primary/70">{{ $order['full_name'] }}</td>
                            <td class="py-3 px-2 text-primary/60">{{ $order['city'] ?? '-' }}</td>
                            <td class="py-3 px-2">
                                @switch($order['status'])
                                    @case('processing')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ __('dashboard.status_pending') }}</span>
                                        @break
                                    @case('with_delivery_company')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">{{ __('dashboard.status_in_transit') }}</span>
                                        @break
                                    @case('received')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">{{ __('dashboard.status_delivered') }}</span>
                                        @break
                                    @case('cancelled')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">{{ __('dashboard.status_cancelled') }}</span>
                                        @break
                                    @case('returned')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">{{ __('dashboard.status_returned') }}</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="py-3 px-2 text-primary/60">{{ $order['items_count'] }} ({{ $order['total_items'] }})</td>
                            <td class="py-3 px-2 text-end font-medium text-primary">JOD {{ number_format($order['actual_charge'], 2) }}</td>
                            <td class="py-3 px-2 text-primary/60">{{ \Carbon\Carbon::parse($order['created_at'])->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-primary/50">{{ __('dashboard.no_orders') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top Selling Products & Coupon Usage --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Top Selling Products --}}
        <div class="bg-white rounded-xl border border-accent-light shadow-sm p-6">
            <h3 class="text-lg font-semibold text-primary/80 mb-4">{{ __('dashboard.top_selling_products') }}</h3>
            @forelse($topSellingProducts as $product)
                <div class="flex items-center justify-between py-3 border-b border-accent-light/50 last:border-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-primary truncate">{{ $product['name'] }}</p>
                        <p class="text-xs text-primary/50">{{ $product['brand_name'] }} · Size {{ $product['size'] }}</p>
                    </div>
                    <div class="flex items-center gap-4 text-sm">
                        <div class="text-end">
                            <p class="font-medium text-primary">{{ $product['total_sold'] }} {{ __('dashboard.sold') }}</p>
                            <p class="text-xs text-green-600">JOD {{ number_format($product['total_profit'], 2) }} {{ __('dashboard.profit') }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-primary/50 py-8">{{ __('dashboard.no_sales_data') }}</p>
            @endforelse
        </div>

        {{-- Coupon Usage --}}
        <div class="bg-white rounded-xl border border-accent-light shadow-sm p-6">
            <h3 class="text-lg font-semibold text-primary/80 mb-4">{{ __('dashboard.coupon_usage') }}</h3>
            @forelse($couponUsage as $coupon)
                <div class="flex items-center justify-between py-3 border-b border-accent-light/50 last:border-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-primary">{{ $coupon['code'] }}</p>
                        <p class="text-xs text-primary/50">
                            {{ ucfirst($coupon['type']) }}
                            @if($coupon['type'] === 'percentage')
                                ({{ $coupon['value'] }}%)
                            @elseif($coupon['type'] === 'fixed')
                                (JOD {{ number_format($coupon['value'], 2) }})
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-4 text-sm">
                        <div class="text-end">
                            <p class="font-medium text-primary">{{ $coupon['usage_count'] }} {{ __('dashboard.used') }}</p>
                            <p class="text-xs text-red-600">-JOD {{ number_format($coupon['total_discount_given'], 2) }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-primary/50 py-8">{{ __('dashboard.no_coupons_used') }}</p>
            @endforelse
        </div>
    </div>

    {{-- Inventory Health & Delivery Performance --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Inventory Health --}}
        <div class="bg-white rounded-xl border border-accent-light shadow-sm p-6">
            <h3 class="text-lg font-semibold text-primary/80 mb-4">{{ __('dashboard.inventory_health') }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div class="bg-accent-light/30 rounded-lg p-3 text-center">
                    <p class="text-2xl font-bold text-primary">{{ $totalStockValue > 0 ? 'JOD ' . number_format($totalStockValue, 0) : '-' }}</p>
                    <p class="text-xs text-primary/60 mt-1">{{ __('dashboard.stock_value') }}</p>
                </div>
                <div class="bg-red-50 rounded-lg p-3 text-center">
                    <p class="text-2xl font-bold text-red-600">{{ $outOfStockCount }}</p>
                    <p class="text-xs text-primary/60 mt-1">{{ __('dashboard.out_of_stock') }}</p>
                </div>
            </div>
            @if($lowStockProducts)
                <p class="text-sm font-medium text-primary/70 mb-2">{{ __('dashboard.low_stock_alert') }}</p>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($lowStockProducts as $product)
                        <div class="flex items-center justify-between py-1 border-b border-accent-light/30 last:border-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-primary truncate">{{ $product['name'] }}</p>
                                <p class="text-xs text-primary/50">{{ $product['brand'] ?? '-' }}</p>
                            </div>
                            <span class="text-xs font-bold {{ $product['stock_quantity'] <= 2 ? 'text-red-600' : 'text-amber-600' }}">
                                {{ $product['stock_quantity'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-primary/50 text-sm py-4">{{ __('dashboard.all_well_stocked') }}</p>
            @endif
        </div>

        {{-- Delivery by Courier --}}
        <div class="bg-white rounded-xl border border-accent-light shadow-sm p-6">
            <h3 class="text-lg font-semibold text-primary/80 mb-4">{{ __('dashboard.delivery_by_courier') }}</h3>
            @forelse($deliveryByCourier as $courier)
                <div class="flex items-center justify-between py-3 border-b border-accent-light/50 last:border-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-primary truncate">{{ $courier['courier_name'] }}</p>
                        <p class="text-xs text-primary/50">{{ $courier['order_count'] }} {{ __('dashboard.orders') }}</p>
                    </div>
                    <div class="text-end">
                        <p class="text-sm font-medium {{ $courier['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            JOD {{ number_format($courier['profit'], 2) }}
                        </p>
                        <p class="text-xs text-primary/50">{{ number_format($courier['margin'], 1) }}% {{ __('dashboard.margin') }}</p>
                    </div>
                </div>
            @empty
                <p class="text-center text-primary/50 py-8">{{ __('dashboard.no_delivery_data') }}</p>
            @endforelse
        </div>

        {{-- Recent Inventory Movements --}}
        <div class="bg-white rounded-xl border border-accent-light shadow-sm p-6">
            <h3 class="text-lg font-semibold text-primary/80 mb-4">{{ __('dashboard.recent_inventory') }}</h3>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @forelse($recentInventoryMovements as $movement)
                    <div class="flex items-center justify-between py-2 border-b border-accent-light/50 last:border-0">
                        <div class="flex-1 min-w-0">
                            @switch($movement['type'])
                                @case('supply')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">+</span>
                                    @break
                                @case('sale')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">-</span>
                                    @break
                                @case('return')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-700">+</span>
                                    @break
                                @case('damage')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">-</span>
                                    @break
                                @default
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">±</span>
                            @endswitch
                            <p class="text-xs font-medium text-primary truncate ms-2">{{ $movement['product_name'] ?? __('dashboard.unknown') }}</p>
                        </div>
                        <div class="text-end">
                            <p class="text-sm font-medium text-primary">{{ $movement['quantity'] }}</p>
                            <p class="text-xs text-primary/50">{{ \Carbon\Carbon::parse($movement['created_at'])->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-primary/50 py-4">{{ __('dashboard.no_recent_movements') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Undelivered Orders --}}
    @if($undeliveredOrders)
        <div class="bg-white rounded-xl border border-accent-light shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-primary/80">{{ __('dashboard.undelivered_orders') }} ({{ count($undeliveredOrders) }})</h3>
                <a href="{{ route('admin.orders.index') }}?status=processing" class="text-sm text-accent hover:text-accent/80">{{ __('dashboard.view_all') }}</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($undeliveredOrders as $order)
                    <div class="border border-accent-light rounded-lg p-4 hover:shadow-sm transition-shadow">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <p class="font-medium text-primary">{{ $order['order_number'] }}</p>
                                <p class="text-xs text-primary/60">{{ $order['full_name'] }}</p>
                            </div>
                            @switch($order['status'])
                                @case('processing')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ __('dashboard.status_pending') }}</span>
                                    @break
                                @case('with_delivery_company')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">{{ __('dashboard.status_in_transit') }}</span>
                                    @break
                            @endswitch
                        </div>
                        <div class="flex items-center justify-between text-xs text-primary/60">
                            <span>{{ $order['city_name'] ?? '-' }}</span>
                            <span>{{ $order['courier_name'] ?? __('dashboard.no_courier') }}</span>
                            <span class="{{ $order['days_in_transit'] > 3 ? 'text-red-600 font-medium' : '' }}">
                                {{ $order['days_in_transit'] }}d
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection
