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
        $aboutPage = $this->aboutPageAdminService->getFirstOrCreate();
        return $this->successResponse($aboutPage, 'Dane strony O nas pobrane pomyślnie');
    }

    /**
     * Display all about pages.
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        $aboutPages = $this->aboutPageAdminService->getAll();
        return $this->successResponse($aboutPages, 'Wszystkie strony O nas pobrane pomyślnie');
    }

    /**
     * Display the specified about page.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $aboutPage = $this->aboutPageAdminService->getById($id);
        return $this->successResponse($aboutPage, 'Strona O nas pobrana pomyślnie');
    }

    /**
     * Create a new about page.
     *
     * @param  AboutPageRequest  $request
     * @return JsonResponse
     */
    public function create(AboutPageRequest $request): JsonResponse
    {
        $aboutPage = $this->aboutPageAdminService->create($request->validated());
        return $this->successResponse($aboutPage, 'Strona O nas została utworzona', 201);
    }

    /**
     * Update the specified about page.
     *
     * @param  AboutPageRequest  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(AboutPageRequest $request, $id): JsonResponse
    {
        $aboutPage = $this->aboutPageAdminService->update($id, $request->validated());
        return $this->successResponse($aboutPage, 'Strona O nas została zaktualizowana');
    }

    /**
     * Update the specified about page.
     *
     * @param  AboutPageRequest  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function updateContent(AboutPageRequest $request, $id): JsonResponse
    {
        $aboutPage = $this->aboutPageAdminService->updateContent($id, $request->validated());
        return $this->successResponse($aboutPage, 'Strona O nas została zaktualizowana');
    }

    /**
     * Remove the specified about page from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $this->aboutPageAdminService->delete($id);
        return $this->successResponse(null, 'Strona O nas została usunięta', 204);
    }
} 