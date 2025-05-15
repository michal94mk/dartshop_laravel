<?php

namespace App\Http\Requests\Admin;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Admin Order validation request
 * 
 * Handles validation rules for order administration in the admin panel.
 * Ensures administrators can only perform valid order status transitions.
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
                'in:' . implode(',', array_column(OrderStatus::cases(), 'value')),
                function ($attribute, $value, $fail) {
                    $order = $this->route('order');
                    
                    // Prevent invalid status transitions
                    if ($order->status === OrderStatus::CANCELLED && 
                        $value !== OrderStatus::CANCELLED->value) {
                        $fail('Cannot change status of a cancelled order');
                    }
                    
                    if ($order->status === OrderStatus::REFUNDED && 
                        $value !== OrderStatus::REFUNDED->value) {
                        $fail('Cannot change status of a refunded order');
                    }
                    
                    // Prevent backward transitions in the fulfillment process
                    if ($order->status === OrderStatus::DELIVERED && 
                        in_array($value, [
                            OrderStatus::PENDING->value,
                            OrderStatus::PROCESSING->value,
                            OrderStatus::COMPLETED->value,
                            OrderStatus::SHIPPED->value
                        ])) {
                        $fail('Cannot change a delivered order to ' . $value);
                    }
                    
                    if ($order->status === OrderStatus::SHIPPED && 
                        in_array($value, [
                            OrderStatus::PENDING->value,
                            OrderStatus::PROCESSING->value,
                            OrderStatus::COMPLETED->value
                        ])) {
                        $fail('Cannot change a shipped order to ' . $value);
                    }
                    
                    if ($order->status === OrderStatus::COMPLETED && 
                        in_array($value, [
                            OrderStatus::PENDING->value,
                            OrderStatus::PROCESSING->value
                        ])) {
                        $fail('Cannot change a completed order to ' . $value);
                    }
                },
            ],
            'notes' => 'nullable|string|max:1000',
            'payment_status' => 'nullable|string|exists:App\Enums\PaymentStatus,value',
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
            'status.required' => 'Order status is required',
            'status.in' => 'Selected order status is invalid',
            'payment_status.exists' => 'Selected payment status is invalid',
        ];
    }
} 