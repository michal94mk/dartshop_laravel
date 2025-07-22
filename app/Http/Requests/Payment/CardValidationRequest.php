<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class CardValidationRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'card_number' => 'required|string|regex:/^[\d\s\-]+$/',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'card_number.required' => 'Numer karty jest wymagany.',
            'card_number.string' => 'Numer karty musi być tekstem.',
            'card_number.regex' => 'Numer karty może zawierać tylko cyfry, spacje i myślniki.',
        ];
    }

    /**
     * Get the validated card number.
     */
    public function getCardNumber(): string
    {
        return $this->validated()['card_number'];
    }
} 