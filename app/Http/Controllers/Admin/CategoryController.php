<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\ImageCompressorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Category Management Controller (Admin)
 *
 * Handles category CRUD operations in the admin dashboard.
 * All methods are protected by permission middleware via routes.
 * Uses Form Request validation classes for clean separation of concerns.
 *
 * Features:
 * - Hierarchical categories with parent-child relationships
 * - Auto-generate slugs from category names
 * - Handle image uploads with compression
 * - Soft delete support for categories
 * - Prevent deletion of categories with children
 */
class CategoryController extends Controller
{
    private ImageCompressorService $compressor;

    public function __construct(ImageCompressorService $compressor)
    {
        $this->compressor = $compressor;
    }
    /**
     * Display a hierarchical listing of categories.
     * Authorization is handled via route middleware.
     */
    public function index(Request $request): View
    {
        $categories = Category::with('parent')
            ->ordered()
            ->get();

        $nestedCategories = $this->buildTree($categories);

        return view('admin.categories.index', compact('nestedCategories'));
    }

    /**
     * Show the form for creating a new category.
     * Authorization is handled via route middleware.
     */
    public function create(): View
    {
        $parentCategories = Category::active()
            ->whereNull('parent_id')
            ->ordered()
            ->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created category.
     * Authorization is handled via route middleware.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Auto-generate slug from name
        $slug = Str::slug($validated['name']);

        // Handle image upload with compression
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->compressor->compressAndStore($request->file('image'), 'categories', 'public');
        }

        Category::create([
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'slug' => $slug,
            'image' => $imagePath,
            'status' => $validated['status'],
            'sort_order' => $validated['sort_order'],
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', __('admin.category_created_successfully'));
    }

    /**
     * Show the form for editing the specified category.
     * Authorization is handled via route middleware.
     */
    public function edit(Category $category): View
    {
        $parentCategories = Category::active()
            ->whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->ordered()
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified category.
     * Authorization is handled via route middleware.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();

        // Auto-generate slug from name
        $slug = Str::slug($validated['name']);

        $updateData = [
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'slug' => $slug,
            'status' => $validated['status'],
            'sort_order' => $validated['sort_order'],
        ];

        // Handle image upload with compression
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $updateData['image'] = $this->compressor->compressAndStore($request->file('image'), 'categories', 'public');
        }

        $category->update($updateData);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', __('admin.category_updated_successfully'));
    }

    /**
     * Remove the specified category.
     * Authorization is handled via route middleware.
     *
     * Uses soft delete - records are not permanently removed.
     * Prevents deletion of categories with children.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->children()->exists()) {
            return back()
                ->with('error', __('admin.category_has_children'));
        }

        // Delete image from storage
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', __('admin.category_deleted_successfully'));
    }

    /**
     * Restore a soft deleted category.
     * Authorization is handled via route middleware.
     */
    public function restore($id): RedirectResponse
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->restore();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', __('admin.category_restored_successfully'));
    }

    /**
     * Build a hierarchical tree structure from flat categories.
     */
    protected function buildTree($categories)
    {
        $grouped = $categories->groupBy('parent_id');

        foreach ($categories as $category) {
            if ($grouped->has($category->id)) {
                $category->children = $grouped[$category->id];
            }
        }

        return $grouped->get(null, collect());
    }
}
