<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Newsletter validation request
 * 
 * Handles validation rules for all newsletter operations.
 * Used by NewsletterController for subscribing, unsubscribing and checking status.
 */
class NewsletterRequest extends FormRequest
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
            'email' => 'required|email|max:255'
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Adres email jest wymagany.',
            'email.email' => 'Adres email musi być prawidłowym adresem email.',
            'email.max' => 'Adres email nie może być dłuższy niż 255 znaków.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'email' => 'adres email',
        ];
    }
} 