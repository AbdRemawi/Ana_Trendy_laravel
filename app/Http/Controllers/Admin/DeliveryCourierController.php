<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeliveryCourierRequest;
use App\Http\Requests\UpdateDeliveryCourierRequest;
use App\Models\DeliveryCourier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Delivery Courier Management Controller (Admin)
 *
 * Handles delivery courier CRUD operations in the admin dashboard.
 * All methods are protected by permission middleware via routes.
 * Uses Form Request validation classes for clean separation of concerns.
 *
 * Features:
 * - Search and filter by status
 * - Status toggle
 * - Pagination
 * - Clean validation for unique courier names (only active couriers)
 */
class DeliveryCourierController extends Controller
{
    /**
     * Display a listing of delivery couriers.
     *
     * Authorization is handled via route middleware.
     */
    public function index(Request $request): View
    {
        $query = DeliveryCourier::query();

        // Filter by status if requested
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search by name
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'like', "%{$searchTerm}%");
        }

        $couriers = $query->latest()->paginate(25);

        return view('admin.delivery-couriers.index', compact('couriers'));
    }

    /**
     * Show the form for creating a new delivery courier.
     * Authorization is handled via route middleware.
     */
    public function create(): View
    {
        return view('admin.delivery-couriers.create');
    }

    /**
     * Store a newly created delivery courier.
     * Authorization is handled via route middleware.
     */
    public function store(StoreDeliveryCourierRequest $request): RedirectResponse
    {
        DeliveryCourier::create($request->validated());

        return redirect()
            ->route('admin.delivery-couriers.index')
            ->with('success', __('admin.courier_created_successfully'));
    }

    /**
     * Display the specified delivery courier.
     * Authorization is handled via route middleware.
     */
    public function show(DeliveryCourier $courier): View
    {
        // Eager load delivery fees relationship
        $courier->load(['deliveryFees' => function ($query) {
            $query->with('city')->latest();
        }]);

        return view('admin.delivery-couriers.show', compact('courier'));
    }

    /**
     * Show the form for editing the specified delivery courier.
     * Authorization is handled via route middleware.
     */
    public function edit(DeliveryCourier $courier): View
    {
        return view('admin.delivery-couriers.edit', compact('courier'));
    }

    /**
     * Update the specified delivery courier.
     * Authorization is handled via route middleware.
     */
    public function update(UpdateDeliveryCourierRequest $request, DeliveryCourier $courier): RedirectResponse
    {
        $courier->update($request->validated());

        return redirect()
            ->route('admin.delivery-couriers.index')
            ->with('success', __('admin.courier_updated_successfully'));
    }

    /**
     * Remove the specified delivery courier.
     * Authorization is handled via route middleware.
     *
     * Uses soft delete - records are not permanently removed.
     *
     * SECURITY: Prevents deletion of couriers with active delivery fees
     */
    public function destroy(DeliveryCourier $courier): RedirectResponse
    {
        try {
            // SECURITY: Check if courier has delivery fees before deletion
            if ($courier->fees && $courier->fees->count() > 0) {
                return back()
                    ->with('error', __('admin.cannot_delete_courier_with_fees', ['name' => $courier->name, 'count' => $courier->fees->count()]));
            }

            $courierName = $courier->name;
            $courier->delete();

            return redirect()
                ->route('admin.delivery-couriers.index')
                ->with('success', __('admin.courier_deleted_successfully', ['name' => $courierName]));
        } catch (\Exception $e) {
            return back()
                ->with('error', __('admin.delete_failed', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Restore a soft deleted delivery courier.
     * Authorization is handled via route middleware.
     */
    public function restore($id): RedirectResponse
    {
        $courier = DeliveryCourier::withTrashed()->findOrFail($id);
        $courier->restore();

        return redirect()
            ->route('admin.delivery-couriers.index')
            ->with('success', __('admin.courier_restored_successfully'));
    }

    /**
     * Toggle the active status of a delivery courier.
     * Authorization is handled via route middleware.
     */
    public function toggleStatus(DeliveryCourier $courier): RedirectResponse
    {
        $courier->update(['is_active' => !$courier->is_active]);

        $status = $courier->is_active ? __('admin.status_active') : __('admin.status_inactive');

        return redirect()
            ->route('admin.delivery-couriers.index')
            ->with('success', __('admin.courier_status_updated', ['status' => $status]));
    }
}
