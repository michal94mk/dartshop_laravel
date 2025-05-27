<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only authenticated users can create products
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'required|string|min:10',
            'price' => 'required|numeric|min:0.01|max:999999.99',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'featured' => 'boolean',
            'active' => 'boolean',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nazwa produktu jest wymagana.',
            'name.unique' => 'Produkt o tej nazwie już istnieje.',
            'name.max' => 'Nazwa produktu nie może być dłuższa niż 255 znaków.',
            
            'description.required' => 'Opis produktu jest wymagany.',
            'description.min' => 'Opis produktu musi mieć co najmniej 10 znaków.',
            
            'price.required' => 'Cena produktu jest wymagana.',
            'price.numeric' => 'Cena musi być liczbą.',
            'price.min' => 'Cena musi być większa niż 0.',
            'price.max' => 'Cena nie może być większa niż 999,999.99.',
            
            'category_id.required' => 'Kategoria jest wymagana.',
            'category_id.exists' => 'Wybrana kategoria nie istnieje.',
            
            'brand_id.required' => 'Marka jest wymagana.',
            'brand_id.exists' => 'Wybrana marka nie istnieje.',
            
            'stock_quantity.required' => 'Ilość w magazynie jest wymagana.',
            'stock_quantity.integer' => 'Ilość w magazynie musi być liczbą całkowitą.',
            'stock_quantity.min' => 'Ilość w magazynie nie może być ujemna.',
            
            'sku.unique' => 'SKU musi być unikalne.',
            'sku.max' => 'SKU nie może być dłuższe niż 100 znaków.',
            
            'image.image' => 'Plik musi być obrazem.',
            'image.mimes' => 'Obraz musi być w formacie: jpeg, png, jpg, gif.',
            'image.max' => 'Rozmiar obrazu nie może przekraczać 2MB.',
            
            'weight.numeric' => 'Waga musi być liczbą.',
            'weight.min' => 'Waga nie może być ujemna.',
            
            'dimensions.max' => 'Wymiary nie mogą być dłuższe niż 100 znaków.',
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
            'category_id' => 'kategoria',
            'brand_id' => 'marka',
            'stock_quantity' => 'ilość w magazynie',
            'sku' => 'SKU',
            'image' => 'obraz',
            'featured' => 'polecany',
            'active' => 'aktywny',
            'weight' => 'waga',
            'dimensions' => 'wymiary',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert string booleans to actual booleans
        $this->merge([
            'featured' => $this->boolean('featured'),
            'active' => $this->boolean('active', true), // Default to true
        ]);
    }
} 