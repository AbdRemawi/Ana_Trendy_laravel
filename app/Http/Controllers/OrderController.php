<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\AssignCourierRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Services\OrderCreationService;
use App\Services\OrderStatusService;
use App\Services\OrderCalculationService;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderCreationService $orderCreationService,
        private OrderStatusService $orderStatusService,
        private OrderCalculationService $calculationService
    ) {}

    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderCreationService->create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order->load(['items.product', 'mobiles', 'city', 'coupon']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $order->load(['items.product', 'mobiles', 'city', 'deliveryCourier', 'coupon']),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['items.product', 'mobiles', 'city', 'deliveryCourier', 'coupon'])
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->byStatus($request->input('status'));
        }

        if ($request->has('city_id')) {
            $query->where('city_id', $request->input('city_id'));
        }

        if ($request->has('courier_id')) {
            $query->where('delivery_courier_id', $request->input('courier_id'));
        }

        if ($request->has('coupon_id')) {
            $query->where('coupon_id', $request->input('coupon_id'));
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $perPage = $request->input('per_page', 20);
        $orders = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function assignCourier(Order $order, AssignCourierRequest $request): JsonResponse
    {
        try {
            $this->orderStatusService->assignDeliveryCourier(
                $order,
                $request->delivery_courier_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Courier assigned successfully',
                'data' => $order->fresh()->load(['items', 'mobiles', 'city', 'deliveryCourier', 'coupon']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function updateStatus(Order $order, UpdateOrderStatusRequest $request): JsonResponse
    {
        try {
            $newStatus = $request->getStatusEnum();

            if (!$this->orderStatusService->canTransitionTo($order, $newStatus)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot transition to this status from current status',
                ], 422);
            }

            $this->orderStatusService->transitionStatus($order, $newStatus);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'data' => $order->fresh()->load(['items', 'mobiles', 'city', 'deliveryCourier', 'coupon']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function getStats(Request $request): JsonResponse
    {
        $query = Order::query();

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $orders = $query->get();

        $stats = [
            'total_orders' => $orders->count(),
            'processing' => $orders->where('status', OrderStatus::PROCESSING->value)->count(),
            'with_delivery' => $orders->where('status', OrderStatus::WITH_DELIVERY_COMPANY->value)->count(),
            'received' => $orders->where('status', OrderStatus::RECEIVED->value)->count(),
            'cancelled' => $orders->where('status', OrderStatus::CANCELLED->value)->count(),
            'returned' => $orders->where('status', OrderStatus::RETURNED->value)->count(),
            'total_revenue' => $orders->sum('total_price_for_customer'),
            'total_profit' => $orders->sum(fn($order) => $order->profit),
            'orders_with_coupon' => $orders->where('coupon_id', '!==', null)->count(),
            'orders_with_courier' => $orders->where('delivery_courier_id', '!==', null)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    public function getAvailableTransitions(Order $order): JsonResponse
    {
        $transitions = $this->orderStatusService->getAllowedTransitions($order);

        return response()->json([
            'success' => true,
            'data' => [
                'current_status' => $order->status,
                'current_status_label' => $order->status_label,
                'allowed_transitions' => $transitions,
            ],
        ]);
    }
}
