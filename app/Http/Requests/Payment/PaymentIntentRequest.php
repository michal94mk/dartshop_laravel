<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

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
        // For guest users, cart_items are required
        // For authenticated users, cart comes from database
        if ($this->isGuestPayment()) {
            return [
                'cart_items' => 'required|array|min:1',
                'cart_items.*.product_id' => 'required|exists:products,id',
                'cart_items.*.quantity' => 'required|integer|min:1|max:99',
            ];
        }

        return [];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
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

    /**
     * Check if this is a guest payment request.
     */
    public function isGuestPayment(): bool
    {
        return $this->has('cart_items');
    }

    /**
     * Get the validated cart items (for guest payments).
     */
    public function getCartItems(): array
    {
        return $this->validated()['cart_items'] ?? [];
    }
} 