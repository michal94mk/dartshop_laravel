<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BaseAdminController extends Controller
{
    /**
     * Default number of items per page for admin panel
     */
    protected int $perPage = 15;

    /**
     * Custom paginate method for collections
     *
     * @param mixed $items
     * @param int|null $perPage
     * @param int|null $page
     * @param array $options
     * @return LengthAwarePaginator
     */
    protected function paginateCollection($items, $perPage = null, $page = null, $options = [])
    {
        $perPage = $perPage ?: $this->perPage;
        $page = $page ?: Paginator::resolveCurrentPage() ?: 1;
        
        $items = $items instanceof Collection ? $items : Collection::make($items);
        
        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            $options
        );
    }
    
    /**
     * Handle request for getting the number of items per page
     *
     * @return int
     */
    protected function getPerPage()
    {
        return request()->input('per_page', $this->perPage);
    }
    
    /**
     * Apply search filter to a query builder
     * 
     * @param Builder $query The Eloquent query builder
     * @param Request $request The HTTP request
     * @param array $searchFields Fields to search in (e.g. ['name', 'email'])
     * @return Builder Query with search applied
     */
    protected function applySearch(Builder $query, Request $request, array $searchFields): Builder
    {
        if ($request->has('search') && !empty(trim($request->search))) {
            $search = trim($request->search);
            
            // Prepare search patterns for exact word matching
            $exactTerms = [
                strtolower($search), // Exact match for the entire field
                strtolower($search) . ' %', // Match at the beginning
                '% ' . strtolower($search) . ' %', // Match in the middle
                '% ' . strtolower($search) // Match at the end
            ];
            
            $query->where(function($q) use ($search, $searchFields, $exactTerms) {
                foreach ($searchFields as $field) {
                    // For each field, check against all exact matching patterns
                    $q->orWhere(function($innerQ) use ($field, $exactTerms) {
                        // Exact match for the entire field
                        $innerQ->whereRaw("LOWER($field) = ?", [$exactTerms[0]]);
                        
                        // Match at the beginning
                        $innerQ->orWhereRaw("LOWER($field) LIKE ?", [$exactTerms[1]]);
                        
                        // Match in the middle
                        $innerQ->orWhereRaw("LOWER($field) LIKE ?", [$exactTerms[2]]);
                        
                        // Match at the end
                        $innerQ->orWhereRaw("LOWER($field) LIKE ?", [$exactTerms[3]]);
                    });
                }
            });
        }
        
        return $query;
    }
} 