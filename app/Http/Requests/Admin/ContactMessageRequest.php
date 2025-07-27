<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Contact message validation request
 * 
 * Handles validation rules for contact message management in admin panel.
 * Used by Admin\ContactMessageController for updating and responding to contact messages.
 */
class ContactMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        if ($this->route()->getName() === 'admin.contact-messages.respond') {
            // Respond to message
            return [
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ];
        } else {
            // Update message
            return [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|max:255',
                'subject' => 'sometimes|string|max:255',
                'message' => 'sometimes|string',
                'is_read' => 'sometimes|boolean',
            ];
        }
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.string' => 'Imię musi być tekstem.',
            'name.max' => 'Imię nie może być dłuższe niż 255 znaków.',
            'email.email' => 'Email musi być prawidłowym adresem email.',
            'email.max' => 'Email nie może być dłuższy niż 255 znaków.',
            'subject.required' => 'Temat jest wymagany.',
            'subject.string' => 'Temat musi być tekstem.',
            'subject.max' => 'Temat nie może być dłuższy niż 255 znaków.',
            'message.required' => 'Treść wiadomości jest wymagana.',
            'message.string' => 'Treść wiadomości musi być tekstem.',
            'is_read.boolean' => 'Pole przeczytane musi być prawdą lub fałszem.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'imię',
            'email' => 'email',
            'subject' => 'temat',
            'message' => 'treść wiadomości',
            'is_read' => 'przeczytane',
        ];
    }
} 