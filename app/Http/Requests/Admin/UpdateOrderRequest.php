<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'promotions' => 'required|array',
            'promotions.*.id' => 'required|exists:promotions,id',
            'promotions.*.display_order' => 'required|integer|min:0'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'promotions.required' => 'Lista promocji jest wymagana.',
            'promotions.array' => 'Lista promocji musi być tablicą.',
            'promotions.*.id.required' => 'ID promocji jest wymagane.',
            'promotions.*.id.exists' => 'Wybrana promocja nie istnieje.',
            'promotions.*.display_order.required' => 'Kolejność wyświetlania jest wymagana.',
            'promotions.*.display_order.integer' => 'Kolejność wyświetlania musi być liczbą całkowitą.',
            'promotions.*.display_order.min' => 'Kolejność wyświetlania nie może być mniejsza niż 0.'
        ];
    }
} 