<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Category validation request
 * 
 * Handles validation rules for category management.
 * Used by Admin\CategoryController for creating and updating categories.
 */
class CategoryRequest extends FormRequest
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
        $categoryId = $this->route('category');
        // Handle both object and string cases
        if (is_object($categoryId)) {
            $categoryId = $categoryId->id;
        }
        
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ0-9\s\-\.\&]+$/',
                Rule::unique('categories')->ignore($categoryId),
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nazwa kategorii jest wymagana.',
            'name.string' => 'Nazwa kategorii musi być tekstem.',
            'name.min' => 'Nazwa kategorii musi mieć co najmniej 2 znaki.',
            'name.max' => 'Nazwa kategorii nie może być dłuższa niż 255 znaków.',
            'name.regex' => 'Nazwa kategorii może zawierać tylko litery, cyfry, spacje, myślniki, kropki i znak &.',
            'name.unique' => 'Ta nazwa kategorii już istnieje.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nazwa kategorii',
        ];
    }
} 