<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
                function ($attribute, $value, $fail) {
                    $user = User::where('email', $value)->first();
                    if ($user && !$user->hasVerifiedEmail()) {
                        $fail('Konto z tym adresem e-mail już istnieje, ale nie zostało zweryfikowane. Możesz ponownie wysłać link weryfikacyjny z poziomu panelu użytkownika.');
                    }
                },
            ],
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,user',
            'verified' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nazwa jest wymagana.',
            'name.string' => 'Nazwa musi być tekstem.',
            'name.max' => 'Nazwa nie może być dłuższa niż 255 znaków.',
            'first_name.required' => 'Imię jest wymagane.',
            'first_name.string' => 'Imię musi być tekstem.',
            'first_name.max' => 'Imię nie może być dłuższe niż 255 znaków.',
            'last_name.required' => 'Nazwisko jest wymagane.',
            'last_name.string' => 'Nazwisko musi być tekstem.',
            'last_name.max' => 'Nazwisko nie może być dłuższe niż 255 znaków.',
            'email.required' => 'Email jest wymagany.',
            'email.string' => 'Email musi być tekstem.',
            'email.email' => 'Email musi być prawidłowym adresem email.',
            'email.max' => 'Email nie może być dłuższy niż 255 znaków.',
            'email.unique' => 'Ten email już istnieje.',
            'password.required' => 'Hasło jest wymagane.',
            'password.string' => 'Hasło musi być tekstem.',
            'password.min' => 'Hasło musi mieć co najmniej 8 znaków.',
            'role.required' => 'Rola jest wymagana.',
            'role.in' => 'Rola musi być admin lub user.',
            'verified.boolean' => 'Pole weryfikacji musi być prawdą lub fałszem.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nazwa',
            'first_name' => 'imię',
            'last_name' => 'nazwisko',
            'email' => 'email',
            'password' => 'hasło',
            'role' => 'rola',
            'verified' => 'zweryfikowany',
        ];
    }
} 