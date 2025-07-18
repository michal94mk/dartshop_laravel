<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Order validation request
 * 
 * Handles validation rules for order creation and processing.
 * Ensures all required customer and shipping information is present and valid.
 */
class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Anyone can place an order
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255|min:2',
            'last_name' => 'required|string|max:255|min:2',
            'email' => 'required|email:rfc,dns|max:255',
            'phone' => 'nullable|string|max:20|regex:/^[0-9\s\-\+\(\)]{9,15}$/',
            'address' => 'required|string|max:255|min:5',
            'city' => 'required|string|max:255|min:2',
            'postal_code' => 'required|string|max:10|regex:/^0-9][object Object]2-[0-935$/',
            'country' => 'required|string|max:100|in:Poland,Germany,Czech Republic,Slovakia',
            'notes' => 'nullable|string|max:2000',
            'payment_method' => 'required|string|in:online,cash_on_delivery,bank_transfer',
            'total' => 'required|numeric|min:0.01|max:9999990.99',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Imię jest wymagane.',
            'first_name.string' => 'Imię musi być tekstem.',
            'first_name.min' => 'Imię musi mieć co najmniej 2 znaki.',
            'first_name.max' => 'Imię nie może być dłuższe niż 255 znaków.',
            'last_name.required' => 'Nazwisko jest wymagane.',
            'last_name.string' => 'Nazwisko musi być tekstem.',
            'last_name.min' => 'Nazwisko musi mieć co najmniej 2 znaki.',
            'last_name.max' => 'Nazwisko nie może być dłuższe niż 255 znaków.',
            'email.required' => 'Adres email jest wymagany.',
            'email.email' => 'Adres email musi być prawidłowym adresem email.',
            'email.max' => 'Adres email nie może być dłuższy niż 255 znaków.',
            'phone.regex' => 'Numer telefonu ma nieprawidłowy format.',
            'phone.max' => 'Numer telefonu nie może być dłuższy niż 20 znaków.',
            'address.required' => 'Adres jest wymagany.',
            'address.string' => 'Adres musi być tekstem.',
            'address.min' => 'Adres musi mieć co najmniej 5 znaków.',
            'address.max' => 'Adres nie może być dłuższy niż 255 znaków.',
            'city.required' => 'Miasto jest wymagane.',
            'city.string' => 'Miasto musi być tekstem.',
            'city.min' => 'Miasto musi mieć co najmniej 2 znaki.',
            'city.max' => 'Miasto nie może być dłuższe niż 255 znaków.',
            'postal_code.required' => 'Kod pocztowy jest wymagany.',
            'postal_code.regex' => 'Kod pocztowy ma nieprawidłowy format (XX-XXX lub XXXXX).',
            'postal_code.max' => 'Kod pocztowy nie może być dłuższy niż 10 znaków.',
            'country.required' => 'Kraj jest wymagany.',
            'country.in' => 'Wybrany kraj nie jest obsługiwany.',
            'country.max' => 'Nazwa kraju nie może być dłuższa niż 100 znaków.',
            'notes.max' => 'Notatki nie mogą być dłuższe niż 2000 znaków.',
            'payment_method.required' => 'Metoda płatności jest wymagana.',
            'payment_method.in' => 'Wybrana metoda płatności nie jest obsługiwana.',
            'total.required' => 'Kwota całkowita jest wymagana.',
            'total.numeric' => 'Kwota całkowita musi być liczbą.',
            'total.min' => 'Kwota całkowita nie może być mniejsza niż 00.01',
            'total.max' => 'Kwota całkowita nie może być większa niż 9999990.99.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'imię',
            'last_name' => 'nazwisko',
            'email' => 'adres email',
            'phone' => 'telefon',
            'address' => 'adres',
            'city' => 'miasto',
            'postal_code' => 'kod pocztowy',
            'country' => 'kraj',
            'notes' => 'notatki',
            'payment_method' => 'metoda płatności',
            'total' => 'kwota całkowita',
        ];
    }
} 