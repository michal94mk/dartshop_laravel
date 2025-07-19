<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

/**
 * User review validation request
 * 
 * Handles validation rules for user product reviews.
 * Used by UserReviewController for creating new reviews.
 */
class UserReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255|min:3',
            'content' => 'required|string|max:1000|min:10',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
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
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'rating' => 'ocena',
            'title' => 'tytuł',
            'content' => 'treść',
        ];
    }
} 