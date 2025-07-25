<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\CategoryRequest;
use App\Http\Controllers\Api\BaseApiController;
use App\Services\Admin\CategoryAdminService;

/**
 * API controller for admin category management.
 * Handles requests for listing, creating, updating, deleting categories.
 */
class CategoryController extends BaseApiController
{
    /**
     * @var CategoryAdminService
     */
    protected $categoryAdminService;

    /**
     * Inject the category admin service.
     *
     * @param CategoryAdminService $categoryAdminService
     */
    public function __construct(CategoryAdminService $categoryAdminService)
    {
        $this->categoryAdminService = $categoryAdminService;
    }

    /**
     * Display a listing of the categories.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $categories = $this->categoryAdminService->getCategoriesWithFilters($request);
        return $this->paginatedResponse($categories);
    }

    /**
     * Store a newly created category in storage.
     *
     * @param CategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CategoryRequest $request)
    {
        $category = $this->categoryAdminService->createCategory($request->validated());
        return $this->successResponse($category, 'Kategoria została utworzona', 201);
    }

    /**
     * Display the specified category.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $category = $this->categoryAdminService->getCategoryWithDetails($id);
        if (!$category) {
            return $this->errorResponse('Category not found', 404);
        }
        return $this->successResponse($category, 'Category retrieved');
    }

    /**
     * Update the specified category in storage.
     *
     * @param CategoryRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CategoryRequest $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return $this->errorResponse('Category not found', 404);
        }
        $category = $this->categoryAdminService->updateCategory($category, $request->validated());
        return $this->successResponse($category, 'Kategoria została zaktualizowana');
    }

    /**
     * Remove the specified category from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return $this->errorResponse('Category not found', 404);
        }
        $result = $this->categoryAdminService->deleteCategory($category);
        return $this->successResponse('Kategoria została usunięta');
    }
} 