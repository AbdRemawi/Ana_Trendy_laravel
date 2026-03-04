<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of active categories.
     *
     * Returns all active categories ordered by sort_order,
     * including parent relationship for hierarchical data.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->active()
            ->with('parent')
            ->ordered()
            ->get();

        return CategoryResource::collection($categories)->response();
    }

    /**
     * Display the specified category.
     *
     * @param string $slug The category slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        $category = Category::query()
            ->where('slug', $slug)
            ->active()
            ->with(['parent', 'children'])
            ->firstOrFail();

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(200);
    }
}
