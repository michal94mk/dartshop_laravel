<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutSessionRequest extends FormRequest
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
            'shipping.name' => 'required|string|max:255',
            'shipping.email' => 'required|email|max:255',
            'shipping.address' => 'required|string|max:255',
            'shipping.city' => 'required|string|max:255',
            'shipping.postalCode' => 'required|string|max:10|regex:/^\d{2}-\d{3}$/',
            'shipping_method' => 'required|string|in:courier,express,pickup',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'shipping.name.required' => 'Imię i nazwisko są wymagane.',
            'shipping.name.max' => 'Imię i nazwisko nie mogą przekraczać 255 znaków.',
            'shipping.email.required' => 'Adres email jest wymagany.',
            'shipping.email.email' => 'Podaj prawidłowy adres email.',
            'shipping.address.required' => 'Adres jest wymagany.',
            'shipping.address.max' => 'Adres nie może przekraczać 255 znaków.',
            'shipping.city.required' => 'Miasto jest wymagane.',
            'shipping.city.max' => 'Nazwa miasta nie może przekraczać 255 znaków.',
            'shipping.postalCode.required' => 'Kod pocztowy jest wymagany.',
            'shipping.postalCode.regex' => 'Kod pocztowy musi być w formacie XX-XXX.',
            'shipping_method.required' => 'Metoda wysyłki jest wymagana.',
            'shipping_method.in' => 'Wybrano nieprawidłową metodę wysyłki.',
        ];
    }

    /**
     * Get the validated shipping data.
     */
    public function getShippingData(): array
    {
        return $this->validated()['shipping'];
    }

    /**
     * Get the validated shipping method.
     */
    public function getShippingMethod(): string
    {
        return $this->validated()['shipping_method'];
    }
} 