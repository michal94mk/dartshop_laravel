<?php

namespace App\Http\Requests\Admin;

use App\Enums\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Admin Payment validation request
 * 
 * Handles validation rules for payment administration in the admin panel.
 * Ensures administrators can only perform valid status transitions.
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
        // Authorization is handled by middleware
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules()
    {
        return [
            'status' => [
                'required',
                'string',
                'in:' . implode(',', array_column(PaymentStatus::cases(), 'value')),
                function ($attribute, $value, $fail) {
                    $payment = $this->route('payment');
                    
                    // Prevent invalid status transitions
                    if ($payment->status === PaymentStatus::COMPLETED && 
                        in_array($value, [PaymentStatus::PENDING->value, PaymentStatus::PROCESSING->value])) {
                        $fail('Cannot change a completed payment back to ' . $value);
                    }
                    
                    // Prevent changing a refunded payment
                    if ($payment->status === PaymentStatus::REFUNDED && $value !== PaymentStatus::REFUNDED->value) {
                        $fail('Cannot change status of a refunded payment');
                    }
                },
            ],
            'notes' => 'nullable|string|max:1000',
            'transaction_id' => 'nullable|string|max:255',
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
            'status.required' => 'Payment status is required',
            'status.in' => 'Selected payment status is invalid',
        ];
    }
} 