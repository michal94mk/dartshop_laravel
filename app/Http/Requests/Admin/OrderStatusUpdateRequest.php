<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OrderStatusUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,processing,completed,shipped,delivered,cancelled,refunded',
            'note' => 'nullable|string',
            'notify_customer' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status jest wymagany.',
            'status.in' => 'Status musi być jedną z wartości: pending, processing, completed, shipped, delivered, cancelled, refunded.',
            'note.string' => 'Notatka musi być tekstem.',
            'notify_customer.boolean' => 'Pole powiadomienia klienta musi być prawdą lub fałszem.',
        ];
    }

    public function attributes(): array
    {
        return [
            'status' => 'status',
            'note' => 'notatka',
            'notify_customer' => 'powiadom klienta',
        ];
    }
} 