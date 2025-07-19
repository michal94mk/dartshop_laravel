<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Tutorial validation request
 * 
 * Handles validation rules for tutorial management.
 * Used by Admin\TutorialController for creating and updating tutorials.
 */
class TutorialRequest extends FormRequest
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
        $tutorialId = $this->route('tutorial');
        // Handle both object and string cases
        if (is_object($tutorialId)) {
            $tutorialId = $tutorialId->id;
        }
        
        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'string',
                'max:255',
                Rule::unique('tutorials')->ignore($tutorialId),
            ],
            'content' => 'required|string',
            'image_url' => 'nullable|string',
            'order' => 'nullable|integer',
            'status' => 'required|string|in:draft,published',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Tytuł jest wymagany.',
            'title.string' => 'Tytuł musi być tekstem.',
            'title.max' => 'Tytuł nie może być dłuższy niż 255 znaków.',
            'slug.string' => 'Slug musi być tekstem.',
            'slug.max' => 'Slug nie może być dłuższy niż 255 znaków.',
            'slug.unique' => 'Ten slug już istnieje.',
            'content.required' => 'Treść jest wymagana.',
            'content.string' => 'Treść musi być tekstem.',
            'image_url.string' => 'URL obrazu musi być tekstem.',
            'order.integer' => 'Kolejność musi być liczbą całkowitą.',
            'status.required' => 'Status jest wymagany.',
            'status.in' => 'Status musi być draft lub published.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'tytuł',
            'slug' => 'slug',
            'content' => 'treść',
            'image_url' => 'URL obrazu',
            'order' => 'jność',
            'status' => 'status',
        ];
    }
} 