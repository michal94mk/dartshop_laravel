<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user');
        // Handle both object and string cases
        if (is_object($userId)) {
            $userId = $userId->id;
        }
        
        return [
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,user',
            'verified' => 'boolean',
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('users')->ignore($userId)
            ],
        ];
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
            'password.string' => 'Hasło musi być tekstem.',
            'password.min' => 'Hasło musi mieć co najmniej 8 znaków.',
            'role.required' => 'Rola jest wymagana.',
            'role.in' => 'Rola musi być admin lub user.',
            'verified.boolean' => 'Pole weryfikacji musi być prawdą lub fałszem.',
            'email.required' => 'Email jest wymagany.',
            'email.string' => 'Email musi być tekstem.',
            'email.email' => 'Email musi być prawidłowym adresem email.',
            'email.max' => 'Email nie może być dłuższy niż 255 znaków.',
            'email.unique' => 'Ten email już istnieje.',
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
            'password' => 'hasło',
            'role' => 'rola',
            'verified' => 'zweryfikowany',
            'email' => 'email',
        ];
    }
} 