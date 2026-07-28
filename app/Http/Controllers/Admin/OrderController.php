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
use App\Services\InventoryService;
use App\Enums\OrderStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(
        private OrderStatusService $orderStatusService,
        private InventoryService $inventoryService
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

        // Processing ("pending") orders shown in the bulk status-change modal.
        $processingOrders = Order::byStatus(OrderStatus::PROCESSING->value)
            ->with(['city'])
            ->latest()
            ->get();

        return view('admin.orders.index', compact('orders', 'cities', 'couriers', 'coupons', 'processingOrders'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'mobiles', 'city', 'deliveryCourier', 'coupon']);
        $order->profit = $order->profit;

        return view('admin.orders.show', compact('order'));
    }

    public function print(Order $order): View
    {
        $order->load(['items.product.primaryImage', 'mobiles', 'city', 'deliveryCourier', 'coupon']);

        return view('admin.orders.print', compact('order'));
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

            DB::transaction(function () use ($order) {
                // Remove this order's inventory transactions (both the "sale"
                // entries from placement and any "return" entries from
                // cancellation), so the deducted stock is returned and the
                // ledger keeps no leftover entries for the deleted order.
                $this->inventoryService->deleteTransactionsForOrder($order);

                $order->delete();
            });

            return redirect()
                ->route('admin.orders.index')
                ->with('success', __('admin.order_deleted_successfully', ['number' => $orderNumber]));
        } catch (\Exception $e) {
            return back()
                ->with('error', __('admin.order_cannot_be_deleted') . ': ' . $e->getMessage());
        }
    }

    /**
     * Change the status of many selected orders at once (from the modal).
     */
    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_ids'   => ['required', 'array', 'min:1'],
            'order_ids.*' => ['integer', 'exists:orders,id'],
            'status'      => ['required', 'in:' . implode(',', OrderStatus::values())],
        ]);

        $newStatus = OrderStatus::from($validated['status']);
        $orders = Order::with('items')->whereIn('id', $validated['order_ids'])->get();

        $updated = 0;
        $failed = [];

        foreach ($orders as $order) {
            try {
                if (!$this->orderStatusService->canTransitionTo($order, $newStatus)) {
                    $failed[] = $order->order_number;
                    continue;
                }

                $this->orderStatusService->transitionStatus($order, $newStatus);
                $updated++;
            } catch (\Exception $e) {
                $failed[] = $order->order_number;
            }
        }

        $message = __('admin.bulk_status_updated', [
            'count'  => $updated,
            'status' => __('admin.status_' . $newStatus->value),
        ]);

        $redirect = redirect()->route('admin.orders.index');

        if (!empty($failed)) {
            return $redirect
                ->with('success', $message)
                ->with('error', __('admin.bulk_status_failed', ['orders' => implode(', ', $failed)]));
        }

        return $redirect->with('success', $message);
    }

    /**
     * Export active orders (everything except cancelled / returned) to a
     * UTF-8 CSV that Excel opens directly, including per-order wholesale cost,
     * profit and status, plus a totals row.
     */
    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $excludedStatuses = [
            OrderStatus::CANCELLED->value,
            OrderStatus::RETURNED->value,
        ];

        $orders = Order::with(['items.product', 'city'])
            ->whereNotIn('status', $excludedStatuses)
            ->latest()
            ->get();

        $filename = 'orders-export.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->streamDownload(function () use ($orders) {
            $out = fopen('php://output', 'w');

            // UTF-8 BOM so Excel renders Arabic correctly.
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                __('admin.order_number'),
                __('admin.customer_name'),
                __('admin.order_city'),
                __('admin.order_date'),
                __('admin.order_status'),
                __('admin.export_total_wholesale'),
                __('admin.order_profit'),
                __('admin.total_price'),
            ]);

            $sumWholesale = 0;
            $sumProfit = 0;
            $sumTotal = 0;

            foreach ($orders as $order) {
                $wholesale = $order->items->sum(function ($item) {
                    $costPrice = $item->product->cost_price ?? $item->unit_cost_price;
                    return $costPrice * $item->quantity;
                });
                // Profit = items revenue - items cost (matches Order::getProfitAttribute),
                // computed from the eager-loaded items to avoid a query per order.
                $profit = $order->items->sum('total_price') - $wholesale;
                $total = $order->total_price_for_customer;

                $sumWholesale += $wholesale;
                $sumProfit += $profit;
                $sumTotal += $total;

                fputcsv($out, [
                    $order->order_number,
                    $order->full_name,
                    $order->city->name ?? '-',
                    $order->created_at->format('Y-m-d H:i'),
                    __('admin.status_' . $order->status),
                    number_format($wholesale, 2, '.', ''),
                    number_format($profit, 2, '.', ''),
                    number_format($total, 2, '.', ''),
                ]);
            }

            // Totals row.
            fputcsv($out, [
                __('admin.export_totals'),
                '', '', '', '',
                number_format($sumWholesale, 2, '.', ''),
                number_format($sumProfit, 2, '.', ''),
                number_format($sumTotal, 2, '.', ''),
            ]);

            fclose($out);
        }, $filename, $headers);
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
