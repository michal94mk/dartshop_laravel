<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * User validation request
 * 
 * Handles validation rules for user management in admin panel.
 * Used by Admin\UserController for creating and updating users.
 */
class UserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'role' => 'required|in:admin,user',
            'verified' => 'boolean',
        ];

        // Email validation - different for store vs update
        if ($this->isMethod('POST')) {
            // Store - email must be unique
            $rules['email'] = [
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
            ];
            $rules['password'] = 'required|string|min:8';
        } else {
            // Update - email must be unique except for current user
            $userId = $this->route('user');
            if (is_object($userId)) {
                $userId = $userId->id;
            }
            
            $rules['email'] = [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId)
            ];
            $rules['password'] = 'nullable|string|min:8';
        }

        return $rules;
    }

    /**
     * Get custom error messages for validator errors.
     */
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

    /**
     * Get custom attributes for validator errors.
     */
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