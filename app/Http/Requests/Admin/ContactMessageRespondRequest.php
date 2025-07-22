<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ContactMessageRespondRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    public function rules(): array
    {
        return [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'subject.required' => 'Temat jest wymagany.',
            'subject.string' => 'Temat musi być tekstem.',
            'subject.max' => 'Temat nie może być dłuższy niż 255 znaków.',
            'message.required' => 'Treść wiadomości jest wymagana.',
            'message.string' => 'Treść wiadomości musi być tekstem.',
        ];
    }

    public function attributes(): array
    {
        return [
            'subject' => 'temat',
            'message' => 'treść wiadomości',
        ];
    }
} 