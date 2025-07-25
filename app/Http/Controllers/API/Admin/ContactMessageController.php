<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Admin\ContactMessageAdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Admin\ContactMessageRespondRequest;

class ContactMessageController extends BaseApiController
{
    private ContactMessageAdminService $contactMessageAdminService;

    public function __construct(ContactMessageAdminService $contactMessageAdminService)
    {
        $this->contactMessageAdminService = $contactMessageAdminService;
    }

    /**
     * Display a listing of the messages.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->all();
            $perPage = $this->getPerPage($request);
            $messages = $this->contactMessageAdminService->getMessagesWithFilters($filters, $perPage);
            return $this->successResponse($messages, 'Wiadomości kontaktowe pobrane pomyślnie');
        } catch (\Exception $e) {
            return $this->errorResponse('Błąd podczas pobierania wiadomości kontaktowych: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified message.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $message = $this->contactMessageAdminService->getById($id);
        return $this->successResponse($message, 'Wiadomość kontaktowa pobrana pomyślnie');
    }

    /**
     * Update the message status.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:unread,read,replied',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $message = $this->contactMessageAdminService->updateStatus($id, $request->status);
        return $this->successResponse($message, 'Wiadomość została zaktualizowana');
    }

    /**
     * Mark a message as read.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function markAsRead($id): JsonResponse
    {
        $message = $this->contactMessageAdminService->markAsRead($id);
        return $this->successResponse($message, 'Wiadomość oznaczona jako przeczytana');
    }

    /**
     * Update the message notes.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function updateNotes(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $message = $this->contactMessageAdminService->updateNotes($id, $request->notes);
        return $this->successResponse($message, 'Wiadomość została zaktualizowana');
    }

    /**
     * Remove the specified message from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $this->contactMessageAdminService->deleteById($id);
        return $this->successResponse(null, 'Wiadomość została usunięta', 204);
    }

    /**
     * Send a response to the contact message.
     *
     * @param  ContactMessageRespondRequest  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function respond(ContactMessageRespondRequest $request, $id): JsonResponse
    {
        try {
            $message = $this->contactMessageAdminService->respond($id, $request->message);
            return $this->successResponse($message, 'Odpowiedź została wysłana');
        } catch (\Exception $e) {
            return $this->errorResponse('Błąd podczas wysyłania odpowiedzi: ' . $e->getMessage(), 500);
        }
    }
} 