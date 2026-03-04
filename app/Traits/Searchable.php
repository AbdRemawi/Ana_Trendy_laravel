<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Searchable Trait
 *
 * Provides reusable search functionality for models.
 * Reduces controller duplication by standardizing search patterns.
 */
trait Searchable
{
    /**
     * Apply search query to specified columns.
     * Uses LIKE for partial matching across multiple columns.
     *
     * @param Builder $query
     * @param string|null $search
     * @param array $columns Columns to search in
     * @return Builder
     */
    public function scopeSearch(Builder $query, ?string $search, array $columns = ['name']): Builder
    {
        if (empty($search)) {
            return $query;
        }

        $searchTerm = '%' . $search . '%';

        return $query->where(function ($q) use ($searchTerm, $columns) {
            foreach ($columns as $column) {
                $q->orWhere($column, 'like', $searchTerm);
            }
        });
    }

    /**
     * Apply search to multiple columns with AND logic instead of OR.
     * Useful when searching for exact matches across fields.
     */
    public function scopeSearchAll(Builder $query, ?string $search, array $columns = ['name']): Builder
    {
        if (empty($search)) {
            return $query;
        }

        $searchTerm = '%' . $search . '%';

        return $query->where(function ($q) use ($searchTerm, $columns) {
            foreach ($columns as $column) {
                $q->where($column, 'like', $searchTerm);
            }
        });
    }
}
