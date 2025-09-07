<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Admin review validation request
 * 
 * Handles validation rules for admin review management.
 * Used by Admin\ReviewController for creating and updating reviews.
 */
class ReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
            'user_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255|min:3',
            'content' => 'required|string|max:1000|min:10',
            'is_approved' => 'boolean',
            'is_featured' => 'boolean',
        ];

        if ($this->isMethod('patch') || $this->isMethod('put')) {
            foreach ($rules as $field => $rule) {
                if (is_string($rule) && !str_contains($rule, 'sometimes')) {
                    $rules[$field] = 'sometimes|' . $rule;
                }
            }
        }

        return $rules;
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Produkt jest wymagany.',
            'product_id.exists' => 'Wybrany produkt nie istnieje.',
            'user_id.required' => 'Użytkownik jest wymagany.',
            'user_id.exists' => 'Wybrany użytkownik nie istnieje.',
            'rating.required' => 'Ocena jest wymagana.',
            'rating.integer' => 'Ocena musi być liczbą.',
            'rating.min' => 'Ocena musi być między 1 a 5.',
            'rating.max' => 'Ocena musi być między 1 a 5.',
            'title.required' => 'Tytuł recenzji jest wymagany.',
            'title.string' => 'Tytuł musi być tekstem.',
            'title.max' => 'Tytuł może mieć maksymalnie 255 znaków.',
            'title.min' => 'Tytuł musi mieć co najmniej 3 znaki.',
            'content.required' => 'Treść recenzji jest wymagana.',
            'content.string' => 'Treść musi być tekstem.',
            'content.max' => 'Treść może mieć maksymalnie 1000 znaków.',
            'content.min' => 'Treść musi mieć co najmniej 10 znaków.',
            'is_approved.boolean' => 'Status zatwierdzenia musi być wartością logiczną.',
            'is_featured.boolean' => 'Status wyróżnienia musi być wartością logiczną.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'product_id' => 'produkt',
            'user_id' => 'użytkownik',
            'rating' => 'ocena',
            'title' => 'tytuł',
            'content' => 'treść',
            'is_approved' => 'status zatwierdzenia',
            'is_featured' => 'status wyróżnienia',
        ];
    }
} 