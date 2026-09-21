<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ConfirmPaymentRequest extends FormRequest
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
     * Cart lines are not accepted from the client anymore — they are taken from
     * the snapshot frozen when the PaymentIntent was created.
     */
    public function rules(): array
    {
        return [
            'payment_intent_id' => 'required|string',
            'shipping.name' => 'required|string|max:255',
            'shipping.email' => 'required|email|max:255',
            'shipping.address' => 'required|string|max:255',
            'shipping.city' => 'required|string|max:255',
            'shipping.postalCode' => 'required|string|max:10|regex:/^\d{2}-\d{3}$/',
            'shipping_method' => 'required|string|in:courier,express,pickup,inpost',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'payment_intent_id.required' => 'ID płatności jest wymagane.',
            'payment_intent_id.string' => 'ID płatności musi być tekstem.',
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
     * Guest vs authenticated is determined by the session, not by request shape.
     */
    public function isGuestPayment(): bool
    {
        return !Auth::check();
    }

    /**
     * Get the validated payment intent ID.
     */
    public function getPaymentIntentId(): string
    {
        return $this->validated()['payment_intent_id'];
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
