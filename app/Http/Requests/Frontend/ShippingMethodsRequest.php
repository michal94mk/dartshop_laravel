<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Shipping methods validation request
 * 
 * Handles validation rules for shipping methods requests.
 * Used by ShippingController for getting shipping methods.
 */
class ShippingMethodsRequest extends FormRequest
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
            'cart_total' => 'required|numeric|min:0'
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'cart_total.required' => 'Wartość koszyka jest wymagana.',
            'cart_total.numeric' => 'Wartość koszyka musi być liczbą.',
            'cart_total.min' => 'Wartość koszyka nie może być ujemna.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'cart_total' => 'wartość koszyka',
        ];
    }
} 