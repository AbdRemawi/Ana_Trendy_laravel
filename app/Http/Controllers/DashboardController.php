<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\InventoryTransaction;
use App\Models\Brand;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Dashboard Controller
 *
 * Handles the main dashboard page with real business statistics.
 * All calculations are based on actual database values.
 *
 * Security: Uses permission-based authorization (not roles) for RBAC compliance.
 */
class DashboardController extends Controller
{
    /**
     * Cache TTL in seconds (5 minutes for dashboard data)
     */
    private const CACHE_TTL = 300;

    /**
     * Display the main dashboard page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        Gate::authorize('view dashboard');

        $filter = $request->get('filter', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Get cache key based on filter
        $cacheKey = "dashboard.{$filter}.{$startDate}.{$endDate}";

        $data = Cache::remember($cacheKey, self::CACHE_TTL, function() use ($filter, $startDate, $endDate) {
            return [
                // Top Summary Cards
                'totalRevenue' => $this->getTotalRevenue($filter, $startDate, $endDate),
                'netProfit' => $this->getNetProfit($filter, $startDate, $endDate),
                'totalOrders' => $this->getTotalOrders($filter, $startDate, $endDate),
                'profitMargin' => $this->getProfitMargin($filter, $startDate, $endDate),

                // Order Status Overview
                'orderStatusBreakdown' => $this->getOrderStatusBreakdown($filter, $startDate, $endDate),

                // Revenue & Profit Overview
                'revenueProfitBreakdown' => $this->getRevenueProfitBreakdown($filter, $startDate, $endDate),

                // Inventory Health
                'lowStockProducts' => $this->getLowStockProducts(),
                'totalStockValue' => $this->getTotalStockValue(),
                'outOfStockCount' => $this->getOutOfStockCount(),
                'recentInventoryMovements' => $this->getRecentInventoryMovements(),

                // Recent Orders (no filter - always show latest)
                'recentOrders' => $this->getRecentOrders(),

                // Top Selling Products
                'topSellingProducts' => $this->getTopSellingProducts($filter, $startDate, $endDate),

                // Coupon Usage
                'couponUsage' => $this->getCouponUsage($filter, $startDate, $endDate),

                // Delivery Performance
                'deliveryByCourier' => $this->getDeliveryByCourier($filter, $startDate, $endDate),
                'deliveryByCity' => $this->getDeliveryByCity($filter, $startDate, $endDate),
                'undeliveredOrders' => $this->getUndeliveredOrders(),
            ];
        });

        return view('dashboard.index', array_merge($data, [
            'currentFilter' => $filter,
            'filterLabel' => $this->getFilterLabel($filter, $startDate, $endDate),
        ]));
    }

    /**
     * Get Total Revenue
     *
     * SUM(actual_charge) for active/received orders
     */
    protected function getTotalRevenue(string $filter, ?string $startDate, ?string $endDate): float
    {
        return (float) Order::dateFilter($filter, $startDate, $endDate)
            ->whereIn('status', [
                \App\Enums\OrderStatus::RECEIVED->value,
                \App\Enums\OrderStatus::WITH_DELIVERY_COMPANY->value,
                \App\Enums\OrderStatus::PROCESSING->value,
            ])
            ->sum('actual_charge');
    }

    /**
     * Get Net Profit
     *
     * Net Profit = (Product Revenue - Product Cost) + (Delivery Revenue - Delivery Cost)
     */
    protected function getNetProfit(string $filter, ?string $startDate, ?string $endDate): float
    {
        $result = Order::dateFilter($filter, $startDate, $endDate)
            ->whereIn('status', [
                \App\Enums\OrderStatus::RECEIVED->value,
                \App\Enums\OrderStatus::WITH_DELIVERY_COMPANY->value,
                \App\Enums\OrderStatus::PROCESSING->value,
            ])
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->selectRaw('
                SUM(order_items.total_price) as product_revenue,
                SUM(order_items.unit_cost_price * order_items.quantity) as product_cost,
                SUM(orders.real_delivery_fee) as delivery_cost
            ')
            ->first();

        if (!$result) {
            return 0.0;
        }

        $productProfit = $result->product_revenue - $result->product_cost;
        $deliveryProfit = 0 - $result->delivery_cost;

        return (float) ($productProfit + $deliveryProfit);
    }

    /**
     * Get Total Orders Count
     */
    protected function getTotalOrders(string $filter, ?string $startDate, ?string $endDate): int
    {
        return Order::dateFilter($filter, $startDate, $endDate)->count();
    }

    /**
     * Get Profit Margin Percentage
     *
     * (Net Profit / Total Revenue) * 100
     */
    protected function getProfitMargin(string $filter, ?string $startDate, ?string $endDate): float
    {
        $revenue = $this->getTotalRevenue($filter, $startDate, $endDate);
        $profit = $this->getNetProfit($filter, $startDate, $endDate);

        if ($revenue <= 0) {
            return 0.0;
        }

        return round(($profit / $revenue) * 100, 1);
    }

    /**
     * Get Order Status Breakdown
     *
     * Returns count and value for each status
     */
    protected function getOrderStatusBreakdown(string $filter, ?string $startDate, ?string $endDate): array
    {
        $breakdown = Order::dateFilter($filter, $startDate, $endDate)
            ->selectRaw('
                status,
                COUNT(*) as order_count,
                SUM(actual_charge) as total_value
            ')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return [
            'processing' => $breakdown->get('processing', (object)['order_count' => 0, 'total_value' => 0]),
            'with_delivery_company' => $breakdown->get('with_delivery_company', (object)['order_count' => 0, 'total_value' => 0]),
            'received' => $breakdown->get('received', (object)['order_count' => 0, 'total_value' => 0]),
            'cancelled' => $breakdown->get('cancelled', (object)['order_count' => 0, 'total_value' => 0]),
            'returned' => $breakdown->get('returned', (object)['order_count' => 0, 'total_value' => 0]),
        ];
    }

    /**
     * Get Revenue & Profit Breakdown
     *
     * Detailed breakdown of product vs delivery metrics
     */
    protected function getRevenueProfitBreakdown(string $filter, ?string $startDate, ?string $endDate): array
    {
        $result = Order::dateFilter($filter, $startDate, $endDate)
            ->whereIn('status', [
                \App\Enums\OrderStatus::RECEIVED->value,
                \App\Enums\OrderStatus::WITH_DELIVERY_COMPANY->value,
                \App\Enums\OrderStatus::PROCESSING->value,
            ])
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->selectRaw('
                SUM(order_items.total_price) as product_revenue,
                SUM(order_items.unit_cost_price * order_items.quantity) as product_cost,
                0 as delivery_revenue,
                SUM(orders.real_delivery_fee) as delivery_cost,
                SUM(orders.coupon_discount_amount) as product_discounts,
                COALESCE(SUM(orders.free_delivery_discount), 0) as delivery_discounts
            ')
            ->first();

        if (!$result) {
            return [
                'product_revenue' => 0,
                'product_cost' => 0,
                'product_profit' => 0,
                'product_margin' => 0,
                'delivery_revenue' => 0,
                'delivery_cost' => 0,
                'delivery_profit' => 0,
                'delivery_margin' => 0,
                'product_discounts' => 0,
                'delivery_discounts' => 0,
                'total_discounts' => 0,
                'net_profit' => 0,
            ];
        }

        $productProfit = $result->product_revenue - $result->product_cost;
        $deliveryProfit = $result->delivery_revenue - $result->delivery_cost;
        $productMargin = $result->product_revenue > 0
            ? round(($productProfit / $result->product_revenue) * 100, 1)
            : 0;
        $deliveryMargin = $result->delivery_revenue > 0
            ? round(($deliveryProfit / $result->delivery_revenue) * 100, 1)
            : 0;

        return [
            'product_revenue' => (float) $result->product_revenue,
            'product_cost' => (float) $result->product_cost,
            'product_profit' => (float) $productProfit,
            'product_margin' => (float) $productMargin,
            'delivery_revenue' => (float) $result->delivery_revenue,
            'delivery_cost' => (float) $result->delivery_cost,
            'delivery_profit' => (float) $deliveryProfit,
            'delivery_margin' => (float) $deliveryMargin,
            'product_discounts' => (float) $result->product_discounts,
            'delivery_discounts' => (float) $result->delivery_discounts,
            'total_discounts' => (float) ($result->product_discounts + $result->delivery_discounts),
            'net_profit' => (float) ($productProfit + $deliveryProfit),
        ];
    }

    /**
     * Get Low Stock Products
     *
     * Products with stock quantity <= 5
     */
    protected function getLowStockProducts(): array
    {
        $products = Product::with('brand')
            ->where('status', 'active')
            ->get()
            ->filter(function ($product) {
                return $product->stock_quantity <= 5;
            })
            ->sortBy('stock_quantity')
            ->take(10)
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'size' => $product->size,
                    'brand' => $product->brand?->name,
                    'stock_quantity' => $product->stock_quantity,
                ];
            })
            ->values()
            ->toArray();

        return $products;
    }

    /**
     * Get Total Stock Value
     *
     * SUM(stock_quantity * cost_price) for all active products
     */
    protected function getTotalStockValue(): float
    {
        $products = Product::where('status', 'active')->get();

        $totalValue = 0;
        foreach ($products as $product) {
            $totalValue += $product->stock_quantity * $product->cost_price;
        }

        return (float) $totalValue;
    }

    /**
     * Get Out of Stock Count
     */
    protected function getOutOfStockCount(): int
    {
        return Product::where('status', 'active')
            ->get()
            ->filter(function ($product) {
                return $product->stock_quantity === 0;
            })
            ->count();
    }

    /**
     * Get Recent Inventory Movements
     */
    protected function getRecentInventoryMovements(): array
    {
        return InventoryTransaction::with('product')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($transaction) {
                return [
                    'type' => $transaction->type,
                    'type_label' => $transaction->type_label,
                    'quantity' => $transaction->quantity,
                    'product_name' => $transaction->product?->name,
                    'notes' => $transaction->notes,
                    'created_at' => $transaction->created_at,
                ];
            })
            ->toArray();
    }

    /**
     * Get Recent Orders (latest 20)
     */
    protected function getRecentOrders(): array
    {
        return Order::with(['city', 'deliveryCourier', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'full_name' => $order->full_name,
                    'city' => $order->city?->name,
                    'courier' => $order->deliveryCourier?->name,
                    'status' => $order->status,
                    'status_label' => $order->status_label,
                    'items_count' => $order->items->count(),
                    'total_items' => $order->items->sum('quantity'),
                    'actual_charge' => $order->actual_charge,
                    'created_at' => $order->created_at,
                ];
            })
            ->toArray();
    }

    /**
     * Get Top Selling Products
     */
    protected function getTopSellingProducts(string $filter, ?string $startDate, ?string $endDate): array
    {
        return DB::table('products as p')
            ->join('brands as b', 'p.brand_id', '=', 'b.id')
            ->join('order_items as oi', 'p.id', '=', 'oi.product_id')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->whereBetween('o.created_at', $this->getDateRange($filter, $startDate, $endDate))
            ->whereIn('o.status', [
                \App\Enums\OrderStatus::RECEIVED->value,
                \App\Enums\OrderStatus::WITH_DELIVERY_COMPANY->value,
                \App\Enums\OrderStatus::PROCESSING->value,
            ])
            ->selectRaw('
                p.id,
                p.name,
                p.size,
                p.offer_price,
                p.sale_price,
                b.name as brand_name,
                SUM(oi.quantity) as total_sold,
                SUM(oi.total_price) as total_revenue,
                SUM(oi.total_price - (oi.unit_cost_price * oi.quantity)) as total_profit,
                COUNT(DISTINCT oi.order_id) as order_count
            ')
            ->groupBy('p.id', 'p.name', 'p.size', 'p.offer_price', 'p.sale_price', 'b.name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get()
            ->map(function ($product) {
                $profitMargin = $product->total_revenue > 0
                    ? round(($product->total_profit / $product->total_revenue) * 100, 1)
                    : 0;

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'size' => $product->size,
                    'offer_price' => (float) $product->offer_price,
                    'sale_price' => (float) $product->sale_price,
                    'brand_name' => $product->brand_name,
                    'total_sold' => (int) $product->total_sold,
                    'total_revenue' => (float) $product->total_revenue,
                    'total_profit' => (float) $product->total_profit,
                    'profit_margin' => (float) $profitMargin,
                    'order_count' => (int) $product->order_count,
                ];
            })
            ->toArray();
    }

    /**
     * Get Coupon Usage Summary
     */
    protected function getCouponUsage(string $filter, ?string $startDate, ?string $endDate): array
    {
        return DB::table('coupons as c')
            ->join('orders as o', 'c.id', '=', 'o.coupon_id')
            ->whereBetween('o.created_at', $this->getDateRange($filter, $startDate, $endDate))
            ->selectRaw('
                c.id,
                c.code,
                c.type,
                c.value,
                COUNT(o.id) as usage_count,
                SUM(o.coupon_discount_amount) as total_discount_given,
                SUM(o.actual_charge) as revenue_with_coupon
            ')
            ->groupBy('c.id', 'c.code', 'c.type', 'c.value')
            ->orderByDesc('usage_count')
            ->get()
            ->map(function ($coupon) {
                return [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => (float) $coupon->value,
                    'usage_count' => (int) $coupon->usage_count,
                    'total_discount_given' => (float) $coupon->total_discount_given,
                    'revenue_with_coupon' => (float) $coupon->revenue_with_coupon,
                    'avg_order_value' => (float) ($coupon->usage_count > 0
                        ? $coupon->revenue_with_coupon / $coupon->usage_count
                        : 0),
                ];
            })
            ->toArray();
    }

    /**
     * Get Delivery Performance by Courier
     */
    protected function getDeliveryByCourier(string $filter, ?string $startDate, ?string $endDate): array
    {
        return DB::table('delivery_couriers as dc')
            ->join('orders as o', 'dc.id', '=', 'o.delivery_courier_id')
            ->whereBetween('o.created_at', $this->getDateRange($filter, $startDate, $endDate))
            ->where('o.status', \App\Enums\OrderStatus::RECEIVED->value)
            ->selectRaw('
                dc.id,
                dc.name as courier_name,
                COUNT(o.id) as order_count,
                0 as revenue,
                SUM(o.real_delivery_fee) as cost,
                0 - SUM(o.real_delivery_fee) as profit
            ')
            ->groupBy('dc.id', 'dc.name')
            ->orderByDesc('profit')
            ->get()
            ->map(function ($courier) {
                $margin = $courier->revenue > 0
                    ? round(($courier->profit / $courier->revenue) * 100, 1)
                    : 0;

                return [
                    'id' => $courier->id,
                    'courier_name' => $courier->courier_name,
                    'order_count' => (int) $courier->order_count,
                    'revenue' => (float) $courier->revenue,
                    'cost' => (float) $courier->cost,
                    'profit' => (float) $courier->profit,
                    'margin' => (float) $margin,
                ];
            })
            ->toArray();
    }

    /**
     * Get Delivery Performance by City
     */
    protected function getDeliveryByCity(string $filter, ?string $startDate, ?string $endDate): array
    {
        return DB::table('cities as c')
            ->join('orders as o', 'c.id', '=', 'o.city_id')
            ->whereBetween('o.created_at', $this->getDateRange($filter, $startDate, $endDate))
            ->where('o.status', \App\Enums\OrderStatus::RECEIVED->value)
            ->selectRaw('
                c.id,
                c.name as city_name,
                COUNT(o.id) as order_count,
                AVG(o.real_delivery_fee) as avg_delivery_cost,
                SUM(o.real_delivery_fee) as total_delivery_cost
            ')
            ->groupBy('c.id', 'c.name')
            ->orderByDesc('order_count')
            ->limit(10)
            ->get()
            ->map(function ($city) {
                return [
                    'id' => $city->id,
                    'city_name' => $city->city_name,
                    'order_count' => (int) $city->order_count,
                    'avg_delivery_cost' => (float) $city->avg_delivery_cost,
                    'total_delivery_cost' => (float) $city->total_delivery_cost,
                ];
            })
            ->toArray();
    }

    /**
     * Get Undelivered Orders
     *
     * Orders that are processing or with delivery company
     */
    protected function getUndeliveredOrders(): array
    {
        return Order::with(['city', 'deliveryCourier'])
            ->whereIn('status', [
                \App\Enums\OrderStatus::PROCESSING->value,
                \App\Enums\OrderStatus::WITH_DELIVERY_COMPANY->value,
            ])
            ->orderBy('created_at', 'asc')
            ->limit(50)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'full_name' => $order->full_name,
                    'status' => $order->status,
                    'status_label' => $order->status_label,
                    'courier_name' => $order->deliveryCourier?->name,
                    'city_name' => $order->city?->name,
                    'created_at' => $order->created_at,
                    'days_in_transit' => $order->created_at->diffInDays(now()),
                ];
            })
            ->toArray();
    }

    /**
     * Get date range array based on filter
     */
    protected function getDateRange(string $filter, ?string $startDate, ?string $endDate): array
    {
        return match ($filter) {
            'today' => [Carbon::today(), Carbon::tomorrow()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'custom' => [
                $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->subMonth(),
                $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now(),
            ],
            default => [Carbon::now()->subYear(), Carbon::now()],
        };
    }

    /**
     * Get human-readable filter label
     */
    protected function getFilterLabel(string $filter, ?string $startDate, ?string $endDate): string
    {
        return match ($filter) {
            'today' => 'Today',
            'week' => 'This Week',
            'month' => 'This Month',
            'custom' => $startDate && $endDate
                ? Carbon::parse($startDate)->format('M d') . ' - ' . Carbon::parse($endDate)->format('M d, Y')
                : 'Custom Range',
            default => 'All Time',
        };
    }
}
