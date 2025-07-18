<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingAddressRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'address' => ['required', 'string', 'max:255', 'min:5'],
            'city' => ['required', 'string', 'max:255', 'min:2'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20', 'regex:/^[0-9]{2}-[0-9]{3}$/'],
            'country' => ['required', 'string', 'max:255', 'in:Poland,Germany,Czech Republic,Slovakia'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9\s\-\+\(\)]{9,15}$/'],
            'is_default' => ['boolean'],
        ];
    }
    
    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Imię i nazwisko jest wymagane.',
            'name.string' => 'Imię i nazwisko musi być tekstem.',
            'name.min' => 'Imię i nazwisko musi mieć co najmniej 2 znaki.',
            'name.max' => 'Imię i nazwisko nie może być dłuższe niż 255 znaków.',
            'address.required' => 'Adres jest wymagany.',
            'address.string' => 'Adres musi być tekstem.',
            'address.min' => 'Adres musi mieć co najmniej 5 znaków.',
            'address.max' => 'Adres nie może być dłuższy niż 255 znaków.',
            'city.required' => 'Miasto jest wymagane.',
            'city.string' => 'Miasto musi być tekstem.',
            'city.min' => 'Miasto musi mieć co najmniej 2 znaki.',
            'city.max' => 'Miasto nie może być dłuższe niż 255 znaków.',
            'state.string' => 'Województwo/stan musi być tekstem.',
            'state.max' => 'Województwo/stan nie może być dłuższe niż 255 znaków.',
            'postal_code.required' => 'Kod pocztowy jest wymagany.',
            'postal_code.regex' => 'Kod pocztowy ma nieprawidłowy format (XX-XXX lub XXXXX).',
            'postal_code.max' => 'Kod pocztowy nie może być dłuższy niż 20 znaków.',
            'country.required' => 'Kraj jest wymagany.',
            'country.in' => 'Wybrany kraj nie jest obsługiwany.',
            'country.max' => 'Nazwa kraju nie może być dłuższa niż 255 znaków.',
            'phone.regex' => 'Numer telefonu ma nieprawidłowy format.',
            'phone.max' => 'Numer telefonu nie może być dłuższy niż 20 znaków.',
            'is_default.boolean' => 'Pole domyślny adres musi być prawdą lub fałszem.',
        ];
    }
    
    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'imię i nazwisko',
            'address' => 'adres',
            'city' => 'miasto',
            'state' => 'województwo/stan',
            'postal_code' => 'kod pocztowy',
            'country' => 'kraj',
            'phone' => 'telefon',
            'is_default' => 'domyślny adres',
        ];
    }
} 