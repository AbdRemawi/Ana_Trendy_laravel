<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderRequest;
use App\Http\Requests\Admin\AssignCourierRequest;
use App\Models\Order;
use App\Models\City;
use App\Models\DeliveryCourier;
use App\Models\Coupon;
use App\Services\OrderStatusService;
use App\Enums\OrderStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderStatusService $orderStatusService
    ) {}

    public function index(Request $request): View
    {
        $query = Order::with(['items.product', 'city', 'deliveryCourier', 'coupon']);

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->filled('courier_id')) {
            $query->where('delivery_courier_id', $request->courier_id);
        }

        if ($request->filled('coupon_id')) {
            $query->where('coupon_id', $request->coupon_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(20);

        $cities = City::active()->orderBy('name')->get(['id', 'name']);
        $couriers = DeliveryCourier::active()->orderBy('name')->get(['id', 'name']);
        $coupons = Coupon::active()->orderBy('code')->get(['id', 'code']);

        return view('admin.orders.index', compact('orders', 'cities', 'couriers', 'coupons'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'mobiles', 'city', 'deliveryCourier', 'coupon']);
        $order->profit = $order->profit;

        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order): View
    {
        if (!$order->isProcessing()) {
            return back()->with('error', __('admin.order_not_editable'));
        }

        $order->load(['items.product', 'mobiles', 'city', 'deliveryCourier', 'coupon']);

        $cities = City::active()->orderBy('name')->get(['id', 'name']);

        return view('admin.orders.edit', compact('order', 'cities'));
    }

    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        if (!$order->isProcessing()) {
            return back()->with('error', __('admin.order_not_editable'));
        }

        $order->update([
            'full_name' => $request->full_name,
            'city_id' => $request->city_id,
            'address' => $request->address,
            'notes' => $request->notes,
        ]);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', __('admin.order_updated_successfully'));
    }

    public function assignCourier(Order $order, AssignCourierRequest $request): RedirectResponse
    {
        try {
            $this->orderStatusService->assignDeliveryCourier(
                $order,
                $request->delivery_courier_id
            );

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', __('admin.courier_assigned_successfully'));
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:' . implode(',', OrderStatus::values())],
        ]);

        try {
            $newStatus = OrderStatus::from($request->status);

            if (!$this->orderStatusService->canTransitionTo($order, $newStatus)) {
                return back()->with('error', __('admin.status_transition_not_allowed'));
            }

            $this->orderStatusService->transitionStatus($order, $newStatus);

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', __('admin.order_status_updated', ['status' => $newStatus->label()]));
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Order $order): RedirectResponse
    {
        try {
            $orderNumber = $order->order_number;
            $order->delete();

            return redirect()
                ->route('admin.orders.index')
                ->with('success', __('admin.order_deleted_successfully', ['number' => $orderNumber]));
        } catch (\Exception $e) {
            return back()
                ->with('error', __('admin.order_cannot_be_deleted') . ': ' . $e->getMessage());
        }
    }

    public function getAvailableTransitions(Order $order): View
    {
        $transitions = $this->orderStatusService->getAllowedTransitions($order);

        return view('admin.orders.partials.status-transitions', [
            'order' => $order,
            'transitions' => $transitions,
        ]);
    }
}
