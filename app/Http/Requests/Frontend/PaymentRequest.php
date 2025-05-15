<?php

namespace App\Http\Requests\Frontend;

use App\Enums\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Payment validation request
 * 
 * Handles validation rules for payment processing and updates.
 * Ensures all payment data is consistent and valid.
 */
class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Check if user is authorized to interact with this payment
        $order = $this->route('order');
        
        if (!$order) {
            return false;
        }
        
        // Allow access if the order belongs to the current user or session
        if (Auth::check()) {
            return $order->user_id === Auth::id() || $order->session_id === session()->getId();
        }
        
        return $order->session_id === session()->getId();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules()
    {
        $rules = [];
        
        // Different rules for different payment endpoints
        if ($this->is('payment/*/complete')) {
            $rules = [
                'transaction_id' => 'sometimes|string|max:255',
                'card_number' => 'sometimes|string|regex:/^[0-9\s]{13,19}$/',
                'expiration' => 'sometimes|string|regex:/^(0[1-9]|1[0-2])\/[0-9]{2}$/',
                'cvv' => 'sometimes|string|regex:/^[0-9]{3,4}$/',
                'cardholder' => 'sometimes|string|max:255',
            ];
        } elseif ($this->is('admin/payments/*/update')) {
            $rules = [
                'status' => 'required|string|in:' . implode(',', array_column(PaymentStatus::cases(), 'value')),
                'notes' => 'nullable|string|max:1000',
            ];
        }
        
        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'transaction_id.string' => 'Transaction ID must be a valid string',
            'card_number.regex' => 'Card number format is invalid',
            'expiration.regex' => 'Expiration date must be in format MM/YY',
            'cvv.regex' => 'CVV code must be 3 or 4 digits',
            'status.required' => 'Payment status is required',
            'status.in' => 'Selected payment status is invalid',
        ];
    }
} 