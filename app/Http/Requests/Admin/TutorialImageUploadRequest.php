<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TutorialImageUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    public function rules(): array
    {
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Obraz jest wymagany.',
            'image.image' => 'Plik musi być obrazem.',
            'image.mimes' => 'Obraz musi być w formacie jpeg, png, jpg, gif lub webp.',
            'image.max' => 'Obraz nie może być większy niż 5MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'image' => 'obraz',
        ];
    }
} 