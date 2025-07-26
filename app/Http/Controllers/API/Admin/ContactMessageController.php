<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Admin\ContactMessageAdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
        $filters = $request->all();
        $perPage = $this->getPerPage($request);
        $messages = $this->contactMessageAdminService->getMessagesWithFilters($filters, $perPage);
        return $this->successResponse($messages, 'Wiadomości kontaktowe pobrane pomyślnie');
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
     * Update the specified message.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'subject' => 'sometimes|string|max:255',
            'message' => 'sometimes|string',
            'is_read' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $message = $this->contactMessageAdminService->update($id, $request->all());
        return $this->successResponse($message, 'Wiadomość została zaktualizowana');
    }

    /**
     * Mark message as read.
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
     * Respond to a contact message.
     *
     * @param  ContactMessageRespondRequest  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function respond(ContactMessageRespondRequest $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'response_message' => 'required|string|min:10',
            'send_email' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $message = $this->contactMessageAdminService->respond($id, $request->all());
        return $this->successResponse($message, 'Odpowiedź została wysłana');
    }

    /**
     * Remove the specified message.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $this->contactMessageAdminService->delete($id);
        return $this->successResponse(null, 'Wiadomość została usunięta', 204);
    }
} 