<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Models\City;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * City Management Controller (Admin)
 *
 * Handles city CRUD operations in the admin dashboard.
 * All methods are protected by permission middleware via routes.
 * Uses Form Request validation classes for clean separation of concerns.
 *
 * Features:
 * - Search and filter by status
 * - Status toggle
 * - Pagination
 * - Clean validation for unique city names (only active cities)
 */
class CityController extends Controller
{
    /**
     * Display a listing of cities.
     *
     * Authorization is handled via route middleware.
     */
    public function index(Request $request): View
    {
        $query = City::query();

        // Filter by status if requested
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search by name
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'like', "%{$searchTerm}%");
        }

        $cities = $query->latest()->paginate(25);

        return view('admin.cities.index', compact('cities'));
    }

    /**
     * Show the form for creating a new city.
     * Authorization is handled via route middleware.
     */
    public function create(): View
    {
        return view('admin.cities.create');
    }

    /**
     * Store a newly created city.
     * Authorization is handled via route middleware.
     */
    public function store(StoreCityRequest $request): RedirectResponse
    {
        City::create($request->validated());

        return redirect()
            ->route('admin.cities.index')
            ->with('success', __('admin.city_created_successfully'));
    }

    /**
     * Display the specified city.
     * Authorization is handled via route middleware.
     */
    public function show(City $city): View
    {
        // Eager load delivery fees relationship
        $city->load(['deliveryFees' => function ($query) {
            $query->with('courier')->latest();
        }]);

        return view('admin.cities.show', compact('city'));
    }

    /**
     * Show the form for editing the specified city.
     * Authorization is handled via route middleware.
     */
    public function edit(City $city): View
    {
        return view('admin.cities.edit', compact('city'));
    }

    /**
     * Update the specified city.
     * Authorization is handled via route middleware.
     */
    public function update(UpdateCityRequest $request, City $city): RedirectResponse
    {
        $city->update($request->validated());

        return redirect()
            ->route('admin.cities.index')
            ->with('success', __('admin.city_updated_successfully'));
    }

    /**
     * Remove the specified city.
     * Authorization is handled via route middleware.
     *
     * Uses soft delete - records are not permanently removed.
     *
     * SECURITY: Prevents deletion of cities with active delivery fees
     */
    public function destroy(City $city): RedirectResponse
    {
        try {
            // SECURITY: Check if city has delivery fees before deletion
            if ($city->deliveryFees && $city->deliveryFees->count() > 0) {
                return back()
                    ->with('error', __('admin.cannot_delete_city_with_fees', ['name' => $city->name, 'count' => $city->deliveryFees->count()]));
            }

            $cityName = $city->name;
            $city->delete();

            return redirect()
                ->route('admin.cities.index')
                ->with('success', __('admin.city_deleted_successfully', ['name' => $cityName]));
        } catch (\Exception $e) {
            return back()
                ->with('error', __('admin.delete_failed', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Restore a soft deleted city.
     * Authorization is handled via route middleware.
     */
    public function restore($id): RedirectResponse
    {
        $city = City::withTrashed()->findOrFail($id);
        $city->restore();

        return redirect()
            ->route('admin.cities.index')
            ->with('success', __('admin.city_restored_successfully'));
    }

    /**
     * Toggle the active status of a city.
     * Authorization is handled via route middleware.
     */
    public function toggleStatus(City $city): RedirectResponse
    {
        $city->update(['is_active' => !$city->is_active]);

        $status = $city->is_active ? __('admin.status_active') : __('admin.status_inactive');

        return redirect()
            ->route('admin.cities.index')
            ->with('success', __('admin.city_status_updated', ['status' => $status]));
    }
}
