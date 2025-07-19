<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Contact form validation request
 * 
 * Handles validation rules for contact form submissions.
 * Used by ContactController for processing contact messages.
 */
class ContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|email:rfc,dns|max:255',
            'subject' => 'required|string|max:255|min:3',
            'message' => 'required|string|min:10|max:5000',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Imię i nazwisko jest wymagane.',
            'name.string' => 'Imię i nazwisko musi być tekstem.',
            'name.min' => 'Imię i nazwisko musi mieć co najmniej 2 znaki.',
            'name.max' => 'Imię i nazwisko nie może być dłuższe niż 255 znaków.',
            'email.required' => 'Adres email jest wymagany.',
            'email.email' => 'Adres email musi być prawidłowym adresem email.',
            'email.max' => 'Adres email nie może być dłuższy niż 255 znaków.',
            'subject.required' => 'Temat jest wymagany.',
            'subject.string' => 'Temat musi być tekstem.',
            'subject.min' => 'Temat musi mieć co najmniej 3 znaki.',
            'subject.max' => 'Temat nie może być dłuższy niż 255 znaków.',
            'message.required' => 'Wiadomość jest wymagana.',
            'message.string' => 'Wiadomość musi być tekstem.',
            'message.min' => 'Wiadomość musi mieć co najmniej 10 znaków.',
            'message.max' => 'Wiadomość nie może być dłuższa niż 5000 znaków.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'imię i nazwisko',
            'email' => 'adres email',
            'subject' => 'temat',
            'message' => 'wiadomość',
        ];
    }
} 