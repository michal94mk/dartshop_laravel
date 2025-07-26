<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ApiRegisterRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'privacy_policy_accepted' => 'required|boolean|accepted',
            'newsletter_consent' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nazwa użytkownika jest wymagana.',
            'name.string' => 'Nazwa użytkownika musi być tekstem.',
            'name.max' => 'Nazwa użytkownika nie może być dłuższa niż 255 znaków.',
            'first_name.required' => 'Imię jest wymagane.',
            'first_name.string' => 'Imię musi być tekstem.',
            'first_name.max' => 'Imię nie może być dłuższe niż 255 znaków.',
            'last_name.required' => 'Nazwisko jest wymagane.',
            'last_name.string' => 'Nazwisko musi być tekstem.',
            'last_name.max' => 'Nazwisko nie może być dłuższe niż 255 znaków.',
            'email.required' => 'Email jest wymagany.',
            'email.string' => 'Email musi być tekstem.',
            'email.email' => 'Email musi być poprawnym adresem email.',
            'email.max' => 'Email nie może być dłuższy niż 255 znaków.',
            'email.unique' => 'Ten adres email jest już zajęty.',
            'password.required' => 'Hasło jest wymagane.',
            'password.string' => 'Hasło musi być tekstem.',
            'password.min' => 'Hasło musi mieć co najmniej 8 znaków.',
            'password.confirmed' => 'Potwierdzenie hasła nie pasuje.',
            'privacy_policy_accepted.required' => 'Akceptacja polityki prywatności jest wymagana.',
            'privacy_policy_accepted.boolean' => 'Akceptacja polityki prywatności musi być wartością logiczną.',
            'privacy_policy_accepted.accepted' => 'Musisz zaakceptować politykę prywatności.',
            'newsletter_consent.boolean' => 'Zgoda na newsletter musi być wartością logiczną.',
        ];
    }
} 