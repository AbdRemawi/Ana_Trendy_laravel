<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeliveryCourierFeeRequest;
use App\Http\Requests\UpdateDeliveryCourierFeeRequest;
use App\Models\City;
use App\Models\DeliveryCourier;
use App\Models\DeliveryCourierFee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Delivery Courier Fee Management Controller (Admin)
 *
 * Handles delivery courier fee CRUD operations in the admin dashboard.
 * All methods are protected by permission middleware via routes.
 * Uses Form Request validation classes for clean separation of concerns.
 *
 * Features:
 * - Only ONE fee allowed per courier-city combination (enforced at DB and validation level)
 * - Filters by courier and city
 * - Status toggle
 * - Pagination with eager loading to avoid N+1 queries
 * - Clean validation for money amounts (decimal precision, >= 0)
 */
class DeliveryCourierFeeController extends Controller
{
    /**
     * Display a listing of delivery courier fees.
     *
     * Authorization is handled via route middleware.
     * Uses eager loading to prevent N+1 queries.
     */
    public function index(Request $request): View
    {
        $query = DeliveryCourierFee::with(['courier', 'city']);

        // Filter by courier if requested
        if ($request->filled('delivery_courier_id')) {
            $query->where('delivery_courier_id', $request->integer('delivery_courier_id'));
        }

        // Filter by city if requested
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->integer('city_id'));
        }

        // Filter by status if requested
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search by courier name or city name
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('courier', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', "%{$searchTerm}%");
                })->orWhereHas('city', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', "%{$searchTerm}%");
                });
            });
        }

        $fees = $query->latest()->paginate(25);

        // Get filter options
        $couriers = DeliveryCourier::active()->get(['id', 'name']);
        $cities = City::active()->get(['id', 'name']);

        return view('admin.delivery-courier-fees.index', compact('fees', 'couriers', 'cities'));
    }

    /**
     * Show the form for creating a new delivery courier fee.
     * Authorization is handled via route middleware.
     */
    public function create(): View
    {
        $couriers = DeliveryCourier::active()->get(['id', 'name']);
        $cities = City::active()->get(['id', 'name']);

        return view('admin.delivery-courier-fees.create', compact('couriers', 'cities'));
    }

    /**
     * Store a newly created delivery courier fee.
     * Authorization is handled via route middleware.
     */
    public function store(StoreDeliveryCourierFeeRequest $request): RedirectResponse
    {
        DeliveryCourierFee::create($request->validated());

        return redirect()
            ->route('admin.delivery-courier-fees.index')
            ->with('success', __('admin.fee_created_successfully'));
    }

    /**
     * Display the specified delivery courier fee.
     * Authorization is handled via route middleware.
     */
    public function show(DeliveryCourierFee $fee): View
    {
        // Eager load relationships
        $fee->load(['courier', 'city']);

        return view('admin.delivery-courier-fees.show', compact('fee'));
    }

    /**
     * Show the form for editing the specified delivery courier fee.
     * Authorization is handled via route middleware.
     */
    public function edit(DeliveryCourierFee $fee): View
    {
        // Eager load relationships
        $fee->load(['courier', 'city']);

        $couriers = DeliveryCourier::active()->get(['id', 'name']);
        $cities = City::active()->get(['id', 'name']);

        return view('admin.delivery-courier-fees.edit', compact('fee', 'couriers', 'cities'));
    }

    /**
     * Update the specified delivery courier fee.
     * Authorization is handled via route middleware.
     */
    public function update(UpdateDeliveryCourierFeeRequest $request, DeliveryCourierFee $fee): RedirectResponse
    {
        $fee->update($request->validated());

        return redirect()
            ->route('admin.delivery-courier-fees.index')
            ->with('success', __('admin.fee_updated_successfully'));
    }

    /**
     * Remove the specified delivery courier fee.
     * Authorization is handled via route middleware.
     *
     * SECURITY: Validates relationships exist before deletion
     */
    public function destroy(DeliveryCourierFee $fee): RedirectResponse
    {
        try {
            // SECURITY: Validate relationships before accessing them
            if (!$fee->courier || !$fee->city) {
                return back()
                    ->with('error', __('admin.fee_missing_relationships'));
            }

            $feeInfo = "{$fee->courier->name} - {$fee->city->name}";
            $fee->delete();

            return redirect()
                ->route('admin.delivery-courier-fees.index')
                ->with('success', __('admin.fee_deleted_successfully', ['info' => $feeInfo]));
        } catch (\Exception $e) {
            return back()
                ->with('error', __('admin.delete_failed', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Toggle the active status of a delivery courier fee.
     * Authorization is handled via route middleware.
     */
    public function toggleStatus(DeliveryCourierFee $fee): RedirectResponse
    {
        $fee->update(['is_active' => !$fee->is_active]);

        $status = $fee->is_active ? __('admin.status_active') : __('admin.status_inactive');

        return redirect()
            ->route('admin.delivery-courier-fees.index')
            ->with('success', __('admin.fee_status_updated', ['status' => $status]));
    }
}
