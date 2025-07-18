<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255', 'min:2'],
            'email' => ['email:rfc,dns', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9\s\-\+\(\)]{9,15}$/'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:1024'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.string' => 'Nazwa użytkownika musi być tekstem.',
            'name.min' => 'Nazwa użytkownika musi mieć co najmniej 2 znaki.',
            'name.max' => 'Nazwa użytkownika nie może być dłuższa niż 255 znaków.',
            'email.email' => 'Adres email musi być prawidłowym adresem email.',
            'email.max' => 'Adres email nie może być dłuższy niż 255 znaków.',
            'email.unique' => 'Ten adres email jest już zajęty.',
            'phone.regex' => 'Numer telefonu ma nieprawidłowy format.',
            'phone.max' => 'Numer telefonu nie może być dłuższy niż 20 znaków.',
            'avatar.image' => 'Plik musi być obrazem.',
            'avatar.mimes' => 'Avatar musi być w formacie: jpeg, png lub jpg.',
            'avatar.max' => 'Rozmiar avataru nie może przekraczać 1.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nazwa użytkownika',
            'email' => 'adres email',
            'phone' => 'telefon',
            'avatar' => 'avatar',
        ];
    }
}
