<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PaymentIntentRequest extends FormRequest
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
            'shipping_method' => 'required|string|in:courier,express,pickup,inpost',
        ];

        // Guests must send product ids + quantities; prices always come from the DB.
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
            'shipping_method.required' => 'Metoda wysyłki jest wymagana.',
            'shipping_method.in' => 'Wybrano nieprawidłową metodę wysyłki.',
            'cart_items.required' => 'Koszyk nie może być pusty.',
            'cart_items.array' => 'Nieprawidłowe dane koszyka.',
            'cart_items.min' => 'Koszyk musi zawierać przynajmniej jeden produkt.',
            'cart_items.*.product_id.required' => 'ID produktu jest wymagane.',
            'cart_items.*.product_id.exists' => 'Wybrany produkt nie istnieje.',
            'cart_items.*.quantity.required' => 'Ilość produktu jest wymagana.',
            'cart_items.*.quantity.integer' => 'Ilość musi być liczbą całkowitą.',
            'cart_items.*.quantity.min' => 'Minimalna ilość to 1.',
            'cart_items.*.quantity.max' => 'Maksymalna ilość to 99.',
        ];
    }

    public function isGuestPayment(): bool
    {
        return !Auth::check();
    }

    public function getCartItems(): array
    {
        return $this->validated()['cart_items'] ?? [];
    }

    public function getShippingMethod(): string
    {
        return $this->validated()['shipping_method'];
    }
}
