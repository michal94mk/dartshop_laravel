<?php

namespace App\Services\Admin;

use App\Models\ContactMessage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling admin contact message business logic (listing, filtering, updating, deleting, responding)
 */
class ContactMessageAdminService
{
    /**
     * Get paginated, filtered, and sorted contact messages.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getMessagesWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = ContactMessage::query();

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Sorting
        $sortField = $filters['sort_field'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $allowedSortFields = ['created_at', 'name', 'email', 'subject', 'status'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Get a single contact message by ID.
     *
     * @param int $id
     * @return ContactMessage
     */
    public function getById(int $id): ContactMessage
    {
        return ContactMessage::findOrFail($id);
    }

    /**
     * Update the status of a contact message.
     *
     * @param int $id
     * @param string $status
     * @return ContactMessage
     */
    public function updateStatus(int $id, string $status): ContactMessage
    {
        $message = ContactMessage::findOrFail($id);
        $message->status = $status;
        $message->save();
        return $message;
    }

    /**
     * Mark a message as read.
     *
     * @param int $id
     * @return ContactMessage
     */
    public function markAsRead(int $id): ContactMessage
    {
        $message = ContactMessage::findOrFail($id);
        if ($message->status === 'unread') {
            $message->status = 'read';
            $message->save();
        }
        return $message;
    }

    /**
     * Update notes for a contact message.
     *
     * @param int $id
     * @param string|null $notes
     * @return ContactMessage
     */
    public function updateNotes(int $id, ?string $notes): ContactMessage
    {
        $message = ContactMessage::findOrFail($id);
        $message->notes = $notes;
        $message->save();
        return $message;
    }

    /**
     * Delete a contact message by ID.
     *
     * @param int $id
     * @return void
     */
    public function deleteById(int $id): void
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();
    }

    /**
     * Store a reply to a contact message (simulate sending email).
     *
     * @param int $id
     * @param string $reply
     * @return ContactMessage
     */
    public function respond(int $id, string $reply): ContactMessage
    {
        $message = ContactMessage::findOrFail($id);
        $message->reply = $reply;
        $message->save();
        // Here you could send an email, etc.
        return $message;
    }
} 