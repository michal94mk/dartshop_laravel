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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image_url' => 'nullable|string',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
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
            'name.max' => 'Nazwa produktu nie może być dłuższa niż :max znaków.',
            'description.string' => 'Opis musi być tekstem.',
            'price.required' => 'Cena jest wymagana.',
            'price.numeric' => 'Cena musi być liczbą.',
            'price.min' => 'Cena nie może być ujemna.',
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
            'image_url' => 'zdjęcie',
            'is_featured' => 'wyróżniony',
            'is_active' => 'aktywny',
            'brand_id' => 'marka',
            'category_id' => 'kategoria',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert string booleans to actual booleans
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'is_active' => $this->boolean('is_active', true), // Default to true
        ]);
    }
} 