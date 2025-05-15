<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Order validation request
 * 
 * Handles validation rules for order creation and processing.
 * Ensures all required customer and shipping information is present and valid.
 */
class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Anyone can place an order
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20|regex:/^[0-9\s\-\+\(\)]+$/',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10|regex:/^[0-9]{2}-[0-9]{3}$/',
            'notes' => 'nullable|string|max:1000',
            'payment_method' => 'required|string|in:online,cash_on_delivery,bank_transfer',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'phone.regex' => 'Phone number format is invalid',
            'address.required' => 'Shipping address is required',
            'city.required' => 'City is required',
            'postal_code.required' => 'Postal code is required',
            'postal_code.regex' => 'Postal code must be in format XX-XXX',
            'payment_method.required' => 'Payment method is required',
            'payment_method.in' => 'Selected payment method is invalid',
        ];
    }
} 