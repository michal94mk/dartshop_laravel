<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Admin Order validation request
 * 
 * Handles validation rules for order administration in the admin panel.
 * Ensures administrators can only perform valid order status transitions.
 */
class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
    public function rules()
    {
        return [
            'user_id' => 'nullable|exists:users,id',
            'first_name' => 'required|string|min:2|max:255',
            'last_name' => 'required|string|min:2|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|min:9|max:20|regex:/^[\d\s\-\+\(\)]+$/',
            'address' => 'required|string|min:5|max:255',
            'city' => 'required|string|min:2|max:100',
            'postal_code' => 'required|string|min:5|max:20|regex:/^\d{2}-\d{3}$/',
            'country' => 'required|string|min:2|max:100',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:pending,processing,completed,shipped,delivered,cancelled,refunded',
            'payment_status' => 'required|in:pending,completed,failed',
            'payment_method' => 'required|string|min:2|max:100',
            'shipping_method' => 'required|string|min:2|max:100',
            'shipping_cost' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.product_name' => 'nullable|string|max:255',
            'items.*.total' => 'nullable|numeric|min:0',
            'order_number' => 'nullable|string|min:3|max:50'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'user_id.exists' => 'Wybrany użytkownik nie istnieje.',
            'first_name.required' => 'Imię jest wymagane.',
            'first_name.string' => 'Imię musi być tekstem.',
            'first_name.min' => 'Imię musi mieć co najmniej 2 znaki.',
            'first_name.max' => 'Imię nie może być dłuższe niż 255 znaków.',
            'last_name.required' => 'Nazwisko jest wymagane.',
            'last_name.string' => 'Nazwisko musi być tekstem.',
            'last_name.min' => 'Nazwisko musi mieć co najmniej 2 znaki.',
            'last_name.max' => 'Nazwisko nie może być dłuższe niż 255 znaków.',
            'email.required' => 'Email jest wymagany.',
            'email.email' => 'Email musi być prawidłowym adresem email.',
            'email.max' => 'Email nie może być dłuższy niż 255 znaków.',
            'phone.string' => 'Telefon musi być tekstem.',
            'phone.min' => 'Telefon musi mieć co najmniej 9 znaków.',
            'phone.max' => 'Telefon nie może być dłuższy niż 20 znaków.',
            'phone.regex' => 'Telefon może zawierać tylko cyfry, spacje, myślniki, plusy i nawiasy.',
            'address.required' => 'Adres jest wymagany.',
            'address.string' => 'Adres musi być tekstem.',
            'address.min' => 'Adres musi mieć co najmniej 5 znaków.',
            'address.max' => 'Adres nie może być dłuższy niż 255 znaków.',
            'city.required' => 'Miasto jest wymagane.',
            'city.string' => 'Miasto musi być tekstem.',
            'city.min' => 'Miasto musi mieć co najmniej 2 znaki.',
            'city.max' => 'Miasto nie może być dłuższe niż 100 znaków.',
            'postal_code.required' => 'Kod pocztowy jest wymagany.',
            'postal_code.string' => 'Kod pocztowy musi być tekstem.',
            'postal_code.min' => 'Kod pocztowy musi mieć co najmniej 5 znaków.',
            'postal_code.max' => 'Kod pocztowy nie może być dłuższy niż 20 znaków.',
            'postal_code.regex' => 'Kod pocztowy musi być w formacie XX-XXX (np. 00-000).',
            'country.required' => 'Kraj jest wymagany.',
            'country.string' => 'Kraj musi być tekstem.',
            'country.min' => 'Kraj musi mieć co najmniej 2 znaki.',
            'country.max' => 'Kraj nie może być dłuższy niż 100 znaków.',
            'total.required' => 'Suma jest wymagana.',
            'total.numeric' => 'Suma musi być liczbą.',
            'total.min' => 'Suma nie może być ujemna.',
            'status.required' => 'Status zamówienia jest wymagany.',
            'status.in' => 'Wybrany status zamówienia jest nieprawidłowy.',
            'payment_status.required' => 'Status płatności jest wymagany.',
            'payment_status.in' => 'Wybrany status płatności jest nieprawidłowy.',
            'payment_method.required' => 'Metoda płatności jest wymagana.',
            'payment_method.string' => 'Metoda płatności musi być tekstem.',
            'payment_method.min' => 'Metoda płatności musi mieć co najmniej 2 znaki.',
            'payment_method.max' => 'Metoda płatności nie może być dłuższa niż 100 znaków.',
            'shipping_method.required' => 'Metoda dostawy jest wymagana.',
            'shipping_method.string' => 'Metoda dostawy musi być tekstem.',
            'shipping_method.min' => 'Metoda dostawy musi mieć co najmniej 2 znaki.',
            'shipping_method.max' => 'Metoda dostawy nie może być dłuższa niż 100 znaków.',
            'shipping_cost.required' => 'Koszt dostawy jest wymagany.',
            'shipping_cost.numeric' => 'Koszt dostawy musi być liczbą.',
            'shipping_cost.min' => 'Koszt dostawy nie może być ujemny.',
            'discount.numeric' => 'Rabat musi być liczbą.',
            'discount.min' => 'Rabat nie może być ujemny.',
            'notes.string' => 'Notatki muszą być tekstem.',
            'notes.max' => 'Notatki nie mogą być dłuższe niż 1000 znaków.',
            'items.required' => 'Elementy zamówienia są wymagane.',
            'items.array' => 'Elementy zamówienia muszą być tablicą.',
            'items.min' => 'Zamówienie musi zawierać co najmniej jeden element.',
            'items.*.product_id.required' => 'ID produktu jest wymagane.',
            'items.*.product_id.exists' => 'Wybrany produkt nie istnieje.',
            'items.*.quantity.required' => 'Ilość jest wymagana.',
            'items.*.quantity.integer' => 'Ilość musi być liczbą całkowitą.',
            'items.*.quantity.min' => 'Ilość musi być większa niż 0.',
            'items.*.price.required' => 'Cena jest wymagana.',
            'items.*.price.numeric' => 'Cena musi być liczbą.',
            'items.*.price.min' => 'Cena nie może być ujemna.',
            'items.*.product_name.string' => 'Nazwa produktu musi być tekstem.',
            'items.*.product_name.max' => 'Nazwa produktu nie może być dłuższa niż 255 znaków.',
            'items.*.total.numeric' => 'Suma elementu musi być liczbą.',
            'items.*.total.min' => 'Suma elementu nie może być ujemna.',
            'order_number.string' => 'Numer zamówienia musi być tekstem.',
            'order_number.min' => 'Numer zamówienia musi mieć co najmniej 3 znaki.',
            'order_number.max' => 'Numer zamówienia nie może być dłuższy niż 50 znaków.',
        ];
    }
} 