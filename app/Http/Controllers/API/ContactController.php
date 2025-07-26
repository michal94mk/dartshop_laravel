<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Services\ContactService;
use App\Http\Requests\Frontend\ContactRequest;
use Illuminate\Http\JsonResponse;
use Exception;

class ContactController extends BaseApiController
{
    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }
    /**
     * Store a new contact message.
     *
     * @param ContactRequest $request
     * @return JsonResponse
     */
    public function store(ContactRequest $request): JsonResponse
    {
        $this->logApiRequest($request, 'Send contact message');
        $contactMessage = $this->contactService->createAndNotify($request->validated());
        return $this->createdResponse($contactMessage, 'Wiadomość została wysłana. Skontaktujemy się z Tobą jak najszybciej.');
    }

    /**
     * Get all contact messages.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $messages = $this->contactService->getAll();
        return $this->successResponse($messages);
    }

    /**
     * Get a single contact message by ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $message = $this->contactService->getById($id);
        return $this->successResponse($message);
    }

    /**
     * Update a contact message by ID.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $message = $this->contactService->update($id, $request->all());
        return $this->successResponse($message, 'Wiadomość została zaktualizowana.');
    }

    /**
     * Delete a contact message by ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $this->contactService->delete($id);
        return $this->noContentResponse();
    }
} 