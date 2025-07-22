<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

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
     */
    public function rules(): array
    {
        $rules = [
            'payment_intent_id' => 'required|string',
            'shipping.name' => 'required|string|max:255',
            'shipping.email' => 'required|email|max:255',
            'shipping.address' => 'required|string|max:255',
            'shipping.city' => 'required|string|max:255',
            'shipping.postalCode' => 'required|string|max:10|regex:/^\d{2}-\d{3}$/',
            'shipping_method' => 'required|string|in:courier,express,pickup',
        ];

        // For guest payments, cart_items are also required
        if ($this->isGuestPayment()) {
            $rules['cart_items'] = 'required|array|min:1';
            $rules['cart_items.*.product_id'] = 'required|exists:products,id';
            $rules['cart_items.*.quantity'] = 'required|integer|min:1|max:99';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'payment_intent_id.required' => 'ID płatności jest wymagane.',
            'payment_intent_id.string' => 'ID płatności musi być tekstem.',
            'cart_items.required' => 'Koszyk nie może być pusty.',
            'cart_items.array' => 'Nieprawidłowe dane koszyka.',
            'cart_items.min' => 'Koszyk musi zawierać przynajmniej jeden produkt.',
            'cart_items.*.product_id.required' => 'ID produktu jest wymagane.',
            'cart_items.*.product_id.exists' => 'Wybrany produkt nie istnieje.',
            'cart_items.*.quantity.required' => 'Ilość produktu jest wymagana.',
            'cart_items.*.quantity.integer' => 'Ilość musi być liczbą całkowitą.',
            'cart_items.*.quantity.min' => 'Minimalna ilość to 1.',
            'cart_items.*.quantity.max' => 'Maksymalna ilość to 99.',
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
     * Check if this is a guest payment request.
     */
    public function isGuestPayment(): bool
    {
        return $this->has('cart_items');
    }

    /**
     * Get the validated payment intent ID.
     */
    public function getPaymentIntentId(): string
    {
        return $this->validated()['payment_intent_id'];
    }

    /**
     * Get the validated cart items (for guest payments).
     */
    public function getCartItems(): array
    {
        return $this->validated()['cart_items'] ?? [];
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