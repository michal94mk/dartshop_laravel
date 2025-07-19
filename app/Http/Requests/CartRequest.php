<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Cart validation request
 * 
 * Handles validation rules for cart operations.
 * Used by CartController for adding and updating cart items.
 */
class CartRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'ID produktu jest wymagane.',
            'product_id.exists' => 'Wybrany produkt nie istnieje.',
            'quantity.required' => 'Ilość jest wymagana.',
            'quantity.integer' => 'Ilość musi być liczbą całkowitą.',
            'quantity.min' => 'Ilość musi być większa niż 0.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'product_id' => 'ID produktu',
            'quantity' => 'ilość',
        ];
    }
} 