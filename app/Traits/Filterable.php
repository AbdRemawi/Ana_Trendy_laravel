<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Filterable Trait
 *
 * Provides reusable query filtering scopes for models.
 * Reduces controller duplication by standardizing filter patterns.
 */
trait Filterable
{
    /**
     * Filter by status field.
     */
    public function scopeByStatus(Builder $query, ?string $status): Builder
    {
        if (empty($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * Apply common filters from request.
     * Handles status, search, and custom filters.
     *
     * @param Builder $query
     * @param Request $request
     * @param array $filters Available filters: ['status', 'search', 'brand', 'category', etc.]
     * @param array $searchColumns Columns to search in (defaults to ['name'])
     * @return Builder
     */
    public function scopeApplyFilters(Builder $query, Request $request, array $filters = [], array $searchColumns = ['name']): Builder
    {
        // Status filter
        if (in_array('status', $filters) && $request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Search filter
        if (in_array('search', $filters) && $request->filled('search')) {
            $query->search($request->search, $searchColumns);
        }

        // Dynamic relationship filters (e.g., brand_id, category_id)
        $relationshipFilters = array_diff($filters, ['status', 'search']);
        foreach ($relationshipFilters as $filter) {
            if ($request->filled($filter)) {
                $column = str_ends_with($filter, '_id') ? $filter : $filter . '_id';
                $query->where($column, $request->$filter);
            }
        }

        return $query;
    }

    /**
     * Paginate filtered results with ordering.
     */
    public function scopePaginateFiltered(Builder $query, Request $request, int $perPage = 25): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $query->latest()->paginate($perPage);
    }
}
