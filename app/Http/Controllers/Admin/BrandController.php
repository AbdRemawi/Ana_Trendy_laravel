<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Str;

/**
 * Brand Management Controller (Admin)
 *
 * Handles brand CRUD operations in the admin dashboard.
 * All methods are protected by permission middleware via routes.
 * Uses Form Request validation classes for clean separation of concerns.
 */
class BrandController extends Controller
{
    public function index(Request $request): View
    {
        $query = Brand::query();

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $brands = $query->latest()->paginate(25);

        return view('admin.brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('admin.brands.create');
    }

    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $slug = Str::slug($validated['name']);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('brands', 'public');
        }

        Brand::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'logo' => $logoPath,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', __('admin.brand_created_successfully'));
    }

    public function show(Brand $brand): View
    {
        return view('admin.brands.show', compact('brand'));
    }

    public function edit(Brand $brand): View
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse
    {
        $validated = $request->validated();
        $slug = Str::slug($validated['name']);

        $updateData = [
            'name' => $validated['name'],
            'slug' => $slug,
            'status' => $validated['status'],
        ];

        if ($request->hasFile('logo')) {
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }

            $updateData['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $brand->update($updateData);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', __('admin.brand_updated_successfully'));
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brandName = $brand->name;
        $brand->delete();

        return redirect()
            ->route('admin.brands.index')
            ->with('success', __('admin.brand_deleted_successfully', ['name' => $brandName]));
    }

    public function restore($id): RedirectResponse
    {
        $brand = Brand::withTrashed()->findOrFail($id);
        $brand->restore();

        return redirect()
            ->route('admin.brands.index')
            ->with('success', __('admin.brand_restored_successfully'));
    }
}
