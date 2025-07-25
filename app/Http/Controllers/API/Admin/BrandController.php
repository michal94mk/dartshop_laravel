<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Brand;
use App\Http\Requests\Admin\BrandRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseApiController;
use App\Services\Admin\BrandAdminService;

/**
 * API controller for admin brand management.
 * Handles requests for listing, creating, updating, deleting brands.
 */
class BrandController extends BaseApiController
{
    /**
     * @var BrandAdminService
     */
    protected $brandAdminService;

    /**
     * Inject the brand admin service.
     *
     * @param BrandAdminService $brandAdminService
     */
    public function __construct(BrandAdminService $brandAdminService)
    {
        $this->brandAdminService = $brandAdminService;
    }

    /**
     * Display a listing of the brands.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $brands = $this->brandAdminService->getBrandsWithFilters($request);
        return $this->paginatedResponse($brands);
    }

    /**
     * Store a newly created brand.
     *
     * @param BrandRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BrandRequest $request)
    {
        $brand = $this->brandAdminService->createBrand($request->validated());
        return $this->successResponse($brand, 'Marka została utworzona', 201);
    }

    /**
     * Display the specified brand.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $brand = $this->brandAdminService->getBrandWithDetails($id);
        if (!$brand) {
            return $this->errorResponse('Marka nie została znaleziona', 404);
        }
        return $this->successResponse($brand, 'Marka pobrana pomyślnie');
    }

    /**
     * Update the specified brand.
     *
     * @param BrandRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(BrandRequest $request, $id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return $this->errorResponse('Marka nie została znaleziona', 404);
        }
        $brand = $this->brandAdminService->updateBrand($brand, $request->validated());
        return $this->successResponse($brand, 'Marka została zaktualizowana');
    }

    /**
     * Remove the specified brand.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return $this->errorResponse('Marka nie została znaleziona', 404);
        }
        $result = $this->brandAdminService->deleteBrand($brand);
        return $this->successResponse($result['message']);
    }
} 