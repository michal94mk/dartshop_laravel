<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * About page validation request
 * 
 * Handles validation rules for about page content management.
 * Used by Admin\AboutPageController for updating about page content.
 */
class AboutPageRequest extends FormRequest
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
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'header_style' => 'nullable|string',
            'header_margin' => 'nullable|string',
            'content_layout' => 'nullable|string',
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
            'meta_title.string' => 'Meta tytuł musi być tekstem.',
            'meta_title.max' => 'Meta tytuł nie może być dłuższy niż 255 znaków.',
            'meta_description.string' => 'Meta opis musi być tekstem.',
            'header_style.string' => 'Styl nagłówka musi być tekstem.',
            'header_margin.string' => 'Margines nagłówka musi być tekstem.',
            'content_layout.string' => 'Układ treści musi być tekstem.',
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
            'meta_title' => 'meta tytuł',
            'meta_description' => 'opis',
            'header_style' => 'styl nagłówka',
            'header_margin' => 'margines nagłówka',
            'content_layout' => 'układ treści',
        ];
    }
} 