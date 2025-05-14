<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;

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
} 