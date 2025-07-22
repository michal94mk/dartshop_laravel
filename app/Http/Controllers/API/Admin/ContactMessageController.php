<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Admin/Contact Messages",
 *     description="API Endpoints for admin contact message management"
 * )
 */

class ContactMessageController extends BaseAdminController
{
    /**
     * Display a listing of the messages.
     *
     * @OA\Get(
     *     path="/api/admin/contact-messages",
     *     summary="Get contact messages list (admin)",
     *     description="Retrieve all contact messages with admin filters and pagination",
     *     tags={"Admin/Contact Messages"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search in name, email, subject, message",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status (unread, read, replied)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"unread", "read", "replied"})
     *     ),
     *     @OA\Parameter(
     *         name="sort_field",
     *         in="query",
     *         description="Sort field (created_at, name, email, subject, status)",
     *         required=false,
     *         @OA\Schema(type="string", default="created_at")
     *     ),
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         description="Sort direction (asc, desc)",
     *         required=false,
     *         @OA\Schema(type="string", default="desc")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ContactMessage")),
     *             @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta"),
     *             @OA\Property(property="links", ref="#/components/schemas/PaginationLinks")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Admin access required"
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $query = ContactMessage::query();
            
            // Apply search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('subject', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%");
                });
            }
            
            // Apply status filter
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }
            
            // Apply sorting
            $sortField = $request->sort_field ?? 'created_at';
            $sortDirection = $request->sort_direction ?? 'desc';
            
            // Validate sort field to prevent SQL injection
            $allowedSortFields = ['created_at', 'name', 'email', 'subject', 'status'];
            if (!in_array($sortField, $allowedSortFields)) {
                $sortField = 'created_at';
            }
            
            $query->orderBy($sortField, $sortDirection);
            
            // Paginate results
            $perPage = $this->getPerPage($request);
            $messages = $query->paginate($perPage);
            
            return $this->successResponse('Wiadomości kontaktowe pobrane pomyślnie', $messages);
        } catch (\Exception $e) {
            return $this->errorResponse('Błąd podczas pobierania wiadomości kontaktowych: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified message.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);
        return $this->successResponse('Wiadomość kontaktowa pobrana pomyślnie', $message);
    }

    /**
     * Update the message status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:unread,read,replied',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $message = ContactMessage::findOrFail($id);
        $message->status = $request->status;
        $message->save();

        return $this->successResponse('Wiadomość została zaktualizowana', $message);
    }

    /**
     * Mark a message as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $message = ContactMessage::findOrFail($id);
        
        if ($message->status === 'unread') {
            $message->status = 'read';
            $message->save();
        }

        return response()->json($message);
    }

    /**
     * Update the message notes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateNotes(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $message = ContactMessage::findOrFail($id);
        $message->notes = $request->notes;
        $message->save();

        return $this->successResponse('Wiadomość została zaktualizowana', $message);
    }

    /**
     * Remove the specified message from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return $this->successResponse('Wiadomość została usunięta', null, 204);
    }

    /**
     * Send a response to the contact message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function respond(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $contactMessage = ContactMessage::findOrFail($id);
        
        try {
            // Here you would typically send an email to the contact
            // For now, we'll just store the reply
            $contactMessage->reply = $request->message;
            $contactMessage->save();
            
            return $this->successResponse('Odpowiedź została wysłana');
        } catch (\Exception $e) {
            return $this->errorResponse('Błąd podczas wysyłania odpowiedzi: ' . $e->getMessage(), 500);
        }
    }
} 