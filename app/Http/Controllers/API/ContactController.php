<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\ContactMessage;
use App\Mail\ContactMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Frontend\ContactRequest;
use Illuminate\Http\JsonResponse;
use Exception;
use App\Services\ContactService;

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
        try {
            $this->logApiRequest($request, 'Send contact message');
            $contactMessage = $this->contactService->createAndNotify($request->validated());
            return $this->createdResponse($contactMessage, 'Wiadomość została wysłana. Skontaktujemy się z Tobą jak najszybciej.');
        } catch (Exception $e) {
            return $this->handleException($e, 'Sending contact message');
        }
    }

    /**
     * Get all contact messages.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $messages = $this->contactService->getAll();
            return $this->successResponse($messages);
        } catch (Exception $e) {
            return $this->handleException($e, 'Fetching contact messages');
        }
    }

    /**
     * Get a single contact message by ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $message = $this->contactService->getById($id);
            return $this->successResponse($message);
        } catch (Exception $e) {
            return $this->handleException($e, 'Fetching contact message');
        }
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
        try {
            $message = $this->contactService->update($id, $request->all());
            return $this->successResponse($message, 'Wiadomość została zaktualizowana.');
        } catch (Exception $e) {
            return $this->handleException($e, 'Updating contact message');
        }
    }

    /**
     * Delete a contact message by ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->contactService->delete($id);
            return $this->noContentResponse();
        } catch (Exception $e) {
            return $this->handleException($e, 'Deleting contact message');
        }
    }
} 