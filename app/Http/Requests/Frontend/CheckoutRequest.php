<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Checkout validation request
 * 
 * Handles validation rules for order checkout process.
 * Used by both authenticated and guest checkout controllers.
 */
class CheckoutRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'shipping_address.first_name' => 'required|string|max:255|min:2',
            'shipping_address.last_name' => 'required|string|max:255|min:2',
            'shipping_address.email' => 'required|email|max:255',
            'shipping_address.street' => 'required|string|max:255|min:5',
            'shipping_address.city' => 'required|string|max:255|min:2',
            'shipping_address.postal_code' => 'required|string|max:10|regex:/^[0-9]{2}-[0-9]{3}$/',
            'shipping_address.phone' => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]{9,20}$/',
            'payment_method' => 'required|string|in:stripe,cod',
            'shipping_method' => 'required|string|in:courier,inpost,pickup,express',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'shipping_address.first_name.required' => 'Imię jest wymagane.',
            'shipping_address.first_name.min' => 'Imię musi mieć co najmniej 2 znaki.',
            'shipping_address.first_name.max' => 'Imię nie może być dłuższe niż 255 znaków.',
            'shipping_address.last_name.required' => 'Nazwisko jest wymagane.',
            'shipping_address.last_name.min' => 'Nazwisko musi mieć co najmniej 2 znaki.',
            'shipping_address.last_name.max' => 'Nazwisko nie może być dłuższe niż 255 znaków.',
            'shipping_address.email.required' => 'Adres email jest wymagany.',
            'shipping_address.email.email' => 'Podaj prawidłowy adres email.',
            'shipping_address.email.max' => 'Adres email nie może być dłuższy niż 255 znaków.',
            'shipping_address.street.required' => 'Adres jest wymagany.',
            'shipping_address.street.min' => 'Adres musi mieć co najmniej 5 znaków.',
            'shipping_address.street.max' => 'Adres nie może być dłuższy niż 255 znaków.',
            'shipping_address.city.required' => 'Miasto jest wymagane.',
            'shipping_address.city.min' => 'Miasto musi mieć co najmniej 2 znaki.',
            'shipping_address.city.max' => 'Miasto nie może być dłuższe niż 255 znaków.',
            'shipping_address.postal_code.required' => 'Kod pocztowy jest wymagany.',
            'shipping_address.postal_code.regex' => 'Kod pocztowy musi być w formacie XX-XXX.',
            'shipping_address.postal_code.max' => 'Kod pocztowy nie może być dłuższy niż 10 znaków.',
            'shipping_address.phone.regex' => 'Numer telefonu ma nieprawidłowy format.',
            'shipping_address.phone.max' => 'Numer telefonu nie może być dłuższy niż 20 znaków.',
            'payment_method.required' => 'Metoda płatności jest wymagana.',
            'payment_method.in' => 'Wybierz prawidłową metodę płatności.',
            'shipping_method.required' => 'Metoda dostawy jest wymagana.',
            'shipping_method.in' => 'Wybierz prawidłową metodę dostawy.',
            'notes.max' => 'Uwagi nie mogą być dłuższe niż 2000 znaków.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'shipping_address.first_name' => 'imię',
            'shipping_address.last_name' => 'nazwisko',
            'shipping_address.email' => 'adres email',
            'shipping_address.street' => 'adres',
            'shipping_address.city' => 'miasto',
            'shipping_address.postal_code' => 'kod pocztowy',
            'shipping_address.phone' => 'numer telefonu',
            'payment_method' => 'metoda płatności',
            'shipping_method' => 'metoda dostawy',
            'notes' => 'uwagi',
        ];
    }
} 