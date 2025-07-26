<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class SyncCartRequest extends FormRequest
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
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Lista elementów jest wymagana.',
            'items.array' => 'Lista elementów musi być tablicą.',
            'items.*.product_id.required' => 'ID produktu jest wymagane.',
            'items.*.product_id.exists' => 'Wybrany produkt nie istnieje.',
            'items.*.quantity.required' => 'Ilość jest wymagana.',
            'items.*.quantity.integer' => 'Ilość musi być liczbą całkowitą.',
            'items.*.quantity.min' => 'Ilość musi być większa niż 0.',
        ];
    }
} 