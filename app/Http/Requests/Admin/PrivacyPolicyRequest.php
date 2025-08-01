<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Privacy policy validation request
 * 
 * Handles validation rules for privacy policy management.
 * Used by Admin\PrivacyPolicyController for creating and updating privacy policies.
 */
class PrivacyPolicyRequest extends FormRequest
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
        return [
            'title' => 'required|string|min:3|max:255',
            'content' => 'required|string|min:50',
            'version' => 'required|string|max:20',
            'effective_date' => 'required|date',
            'is_active' => 'boolean',
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
            'title.min' => 'Tytuł musi mieć co najmniej 3 znaki.',
            'title.max' => 'Tytuł nie może być dłuższy niż 255 znaków.',
            'content.required' => 'Treść jest wymagana.',
            'content.string' => 'Treść musi być tekstem.',
            'content.min' => 'Treść musi mieć co najmniej 50 znaków.',
            'version.required' => 'Wersja jest wymagana.',
            'version.string' => 'Wersja musi być tekstem.',
            'version.max' => 'Wersja nie może być dłuższa niż 20 znaków.',
            'effective_date.required' => 'Data obowiązywania jest wymagana.',
            'effective_date.date' => 'Data obowiązywania musi być prawidłową datą.',
            'is_active.boolean' => 'Pole aktywne musi być prawdą lub fałszem.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'tytuł',
            'content' => 'treść',
            'version' => 'wersja',
            'effective_date' => 'data obowiązywania',
            'is_active' => 'aktywny',
        ];
    }
} 