<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Admin\OrderAdminService;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\OrderRequest;
use App\Http\Requests\Admin\OrderStatusUpdateRequest;
use Illuminate\Http\JsonResponse;

class OrderController extends BaseApiController
{
    private OrderAdminService $orderAdminService;

    public function __construct(OrderAdminService $orderAdminService)
    {
        $this->orderAdminService = $orderAdminService;
    }

    /**
     * Display a listing of the orders.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->all();
        $perPage = $this->getPerPage($request);
        $orders = $this->orderAdminService->getOrdersWithFilters($filters, $perPage);
        return $this->successResponse($orders, 'Zamówienia pobrane pomyślnie');
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(OrderRequest $request): JsonResponse
    {
        $order = $this->orderAdminService->create($request->validated(), $request->items ?? []);
        return $this->successResponse($order, 'Zamówienie zostało utworzone', 201);
    }

    /**
     * Display the specified order.
     */
    public function show($id): JsonResponse
    {
        $order = $this->orderAdminService->getById((int)$id);
        return $this->successResponse($order, 'Zamówienie pobrane');
    }

    /**
     * Update the specified order in storage.
     */
    public function update(OrderRequest $request, $id): JsonResponse
    {
        $order = $this->orderAdminService->update((int)$id, $request->validated(), $request->items ?? null);
        return $this->successResponse($order, 'Zamówienie zostało zaktualizowane');
    }

    /**
     * Update order status.
     */
    public function updateStatus(OrderStatusUpdateRequest $request, $id): JsonResponse
    {
        $order = $this->orderAdminService->updateStatus((int)$id, $request->status, $request->notify_customer ?? false, $request->note ?? null);
        return $this->successResponse($order, 'Status zamówienia został zaktualizowany');
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy($id): JsonResponse
    {
        $this->orderAdminService->deleteById((int)$id);
        return $this->successResponse(null, 'Zamówienie zostało usunięte');
    }
} 