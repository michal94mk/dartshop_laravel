<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\AboutPageRequest;
use App\Services\Admin\AboutPageAdminService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class AboutPageController extends BaseApiController
{
    private AboutPageAdminService $aboutPageAdminService;

    public function __construct(AboutPageAdminService $aboutPageAdminService)
    {
        $this->aboutPageAdminService = $aboutPageAdminService;
    }

    /**
     * Display the first about page data.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $aboutPage = $this->aboutPageAdminService->getFirstOrCreate();
            return $this->successResponse($aboutPage, 'Dane strony O nas pobrane pomyślnie');
        } catch (\Exception $e) {
            Log::error('Failed to get about page data', [
                'error' => $e->getMessage(),
                'method' => __METHOD__
            ]);
            return $this->errorResponse('Błąd podczas pobierania danych strony O nas: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display all about pages.
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        try {
            $aboutPages = $this->aboutPageAdminService->getAll();
            return $this->successResponse($aboutPages, 'Wszystkie strony O nas pobrane pomyślnie');
        } catch (\Exception $e) {
            Log::error('Failed to get all about pages', [
                'error' => $e->getMessage(),
                'method' => __METHOD__
            ]);
            return $this->errorResponse('Błąd podczas pobierania wszystkich stron O nas: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified about page.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $aboutPage = $this->aboutPageAdminService->getById($id);
            return $this->successResponse($aboutPage, 'Strona O nas pobrana pomyślnie');
        } catch (\Exception $e) {
            Log::error('Failed to get about page', [
                'error' => $e->getMessage(),
                'id' => $id,
                'method' => __METHOD__
            ]);
            return $this->errorResponse('Nie znaleziono strony O nas', 404);
        }
    }

    /**
     * Create a new about page.
     *
     * @param  AboutPageRequest  $request
     * @return JsonResponse
     */
    public function create(AboutPageRequest $request): JsonResponse
    {
        try {
            $aboutPage = $this->aboutPageAdminService->create($request->validated());
            return $this->successResponse($aboutPage, 'Strona O nas została utworzona', 201);
        } catch (\Exception $e) {
            Log::error('Failed to create about page', [
                'error' => $e->getMessage(),
                'request_data' => $request->validated(),
                'method' => __METHOD__
            ]);
            return $this->errorResponse('Błąd podczas tworzenia strony O nas: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the first about page.
     *
     * @param  AboutPageRequest  $request
     * @return JsonResponse
     */
    public function update(AboutPageRequest $request): JsonResponse
    {
        try {
            $aboutPage = $this->aboutPageAdminService->updateFirst($request->validated());
            return $this->successResponse($aboutPage, 'Strona O nas została zaktualizowana');
        } catch (\Exception $e) {
            Log::error('Failed to update about page', [
                'error' => $e->getMessage(),
                'request_data' => $request->validated(),
                'method' => __METHOD__
            ]);
            return $this->errorResponse('Błąd podczas aktualizacji strony O nas: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified about page.
     *
     * @param  AboutPageRequest  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function updateById(AboutPageRequest $request, $id): JsonResponse
    {
        try {
            $aboutPage = $this->aboutPageAdminService->updateById($id, $request->validated());
            return $this->successResponse($aboutPage, 'Strona O nas została zaktualizowana');
        } catch (\Exception $e) {
            Log::error('Failed to update about page by ID', [
                'error' => $e->getMessage(),
                'id' => $id,
                'request_data' => $request->validated(),
                'method' => __METHOD__
            ]);
            return $this->errorResponse('Błąd podczas aktualizacji strony O nas: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified about page from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->aboutPageAdminService->deleteById($id);
            return $this->successResponse(null, 'Strona O nas została usunięta', 204);
        } catch (\Exception $e) {
            Log::error('Failed to delete about page', [
                'error' => $e->getMessage(),
                'id' => $id,
                'method' => __METHOD__
            ]);
            return $this->errorResponse('Błąd podczas usuwania strony O nas: ' . $e->getMessage(), 500);
        }
    }
} 