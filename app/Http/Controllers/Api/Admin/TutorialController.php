<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseApiController;
use App\Services\Admin\TutorialAdminService;
use App\Http\Requests\Admin\TutorialRequest;

class TutorialController extends BaseApiController
{
    protected $tutorialAdminService;

    public function __construct(TutorialAdminService $tutorialAdminService)
    {
        $this->tutorialAdminService = $tutorialAdminService;
    }

    /**
     * Display a listing of the tutorials (paginated, filtered, sorted).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $tutorials = $this->tutorialAdminService->getPaginatedTutorials($request);
            return $this->paginatedResponse($tutorials);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Fetching admin tutorials');
        }
    }

    /**
     * Display the specified tutorial by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $tutorial = $this->tutorialAdminService->getTutorialById($id);
            if (!$tutorial) {
                return $this->notFoundResponse('Tutorial not found');
            }
            return $this->successResponse($tutorial, 'Tutorial fetched successfully');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Fetching tutorial by ID');
        }
    }

    /**
     * Store a newly created tutorial in storage.
     *
     * @param  \App\Http\Requests\Admin\TutorialRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TutorialRequest $request)
    {
        try {
            $tutorial = $this->tutorialAdminService->createTutorial($request->validated());
            return $this->successResponse($tutorial, 'Poradnik został utworzony', 201);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Creating tutorial');
        }
    }

    /**
     * Update the specified tutorial in storage.
     *
     * @param  \App\Http\Requests\Admin\TutorialRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TutorialRequest $request, $id)
    {
        try {
            $tutorial = $this->tutorialAdminService->getTutorialById($id);
            if (!$tutorial) {
                return $this->notFoundResponse('Tutorial not found');
            }
            $tutorial = $this->tutorialAdminService->updateTutorial($tutorial, $request->validated());
            return $this->successResponse($tutorial, 'Poradnik został zaktualizowany');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Updating tutorial');
        }
    }

    /**
     * Remove the specified tutorial from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $tutorial = $this->tutorialAdminService->getTutorialById($id);
            if (!$tutorial) {
                return $this->notFoundResponse('Tutorial not found');
            }
            $result = $this->tutorialAdminService->deleteTutorial($tutorial);
            return $this->successResponse('Poradnik został usunięty');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Deleting tutorial');
        }
    }
} 