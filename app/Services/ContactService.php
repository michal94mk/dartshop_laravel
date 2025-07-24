<?php

namespace App\Services;

use App\Models\ContactMessage;
use App\Mail\ContactMessageNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class ContactService
{
    /**
     * Zapisz wiadomość kontaktową i wyślij powiadomienie do admina.
     *
     * @param array $data
     * @return ContactMessage
     * @throws Exception
     */
    public function createAndNotify(array $data): ContactMessage
    {
        try {
            $contactMessage = ContactMessage::create($data);
            $adminEmail = config('mail.admin_email', config('mail.from.address'));
            Mail::to($adminEmail)->queue(new ContactMessageNotification($contactMessage));
            return $contactMessage;
        } catch (Exception $e) {
            Log::error('ContactService error', [
                'message' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Pobierz wszystkie wiadomości kontaktowe.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return ContactMessage::all();
    }

    /**
     * Pobierz wiadomość kontaktową po ID.
     *
     * @param int $id
     * @return ContactMessage
     * @throws Exception
     */
    public function getById(int $id): ContactMessage
    {
        return ContactMessage::findOrFail($id);
    }

    /**
     * Usuń wiadomość kontaktową po ID.
     *
     * @param int $id
     * @return void
     * @throws Exception
     */
    public function delete(int $id): void
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();
    }

    /**
     * Zaktualizuj wiadomość kontaktową po ID.
     *
     * @param int $id
     * @param array $data
     * @return ContactMessage
     * @throws Exception
     */
    public function update(int $id, array $data): ContactMessage
    {
        $message = ContactMessage::findOrFail($id);
        $message->update($data);
        return $message;
    }
} 