<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        return [
            'name' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|min:10|max:2000',
            'price' => 'required|numeric|min:0.01',
            'image' => 'nullable|image|max:2048',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'weight' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nazwa produktu jest wymagana.',
            'name.string' => 'Nazwa produktu musi być tekstem.',
            'name.min' => 'Nazwa produktu musi mieć co najmniej 3 znaki.',
            'name.max' => 'Nazwa produktu nie może być dłuższa niż 255 znaków.',
            'description.min' => 'Opis musi mieć co najmniej 10 znaków.',
            'description.max' => 'Opis nie może być dłuższy niż 2000 znaków.',
            'price.required' => 'Cena jest wymagana.',
            'price.numeric' => 'Cena musi być liczbą.',
            'price.min' => 'Cena nie może być mniejsza niż 0.01',
            'image.image' => 'Plik musi być obrazem.',
            'image.max' => 'Rozmiar obrazu nie może przekraczać 2MB.',
            'weight.numeric' => 'Waga musi być liczbą.',
            'weight.min' => 'Waga nie może być ujemna.',
            'is_active.boolean' => 'Pole aktywne musi być prawdą lub fałszem.',
            'brand_id.required' => 'Marka jest wymagana.',
            'brand_id.exists' => 'Wybrana marka nie istnieje.',
            'category_id.required' => 'Kategoria jest wymagana.',
            'category_id.exists' => 'Wybrana kategoria nie istnieje.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nazwa produktu',
            'description' => 'opis',
            'price' => 'cena',
            'image' => 'obraz',
            'is_featured' => 'wyróżniony',
            'is_active' => 'aktywny',
            'brand_id' => 'marka',
            'category_id' => 'kategoria',
            'weight' => 'waga',
        ];
    }
} 