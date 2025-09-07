<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Brand validation request
 * 
 * Handles validation rules for brand management.
 * Used by Admin\BrandController for creating and updating brands.
 */
class BrandRequest extends FormRequest
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
        $brandId = $this->route('brand');
        // Handle both object and string cases
        if (is_object($brandId)) {
            $brandId = $brandId->id;
        }
        
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ0-9\s\-\.\&]+$/',
                Rule::unique('brands')->ignore($brandId),
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nazwa marki jest wymagana.',
            'name.string' => 'Nazwa marki musi być tekstem.',
            'name.min' => 'Nazwa marki musi mieć co najmniej 2 znaki.',
            'name.max' => 'Nazwa marki nie może być dłuższa niż 255 znaków.',
            'name.regex' => 'Nazwa marki może zawierać tylko litery, cyfry, spacje, myślniki, kropki i znak &.',
            'name.unique' => 'Ta nazwa marki już istnieje.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nazwa marki',
        ];
    }
} 