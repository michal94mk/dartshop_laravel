<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Promotion validation request
 * 
 * Handles validation rules for promotion management.
 * Used by Admin\PromotionController for creating and updating promotions.
 */
class PromotionRequest extends FormRequest
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
        $promotionId = $this->route('promotion');
        // Handle both object and string cases
        if (is_object($promotionId)) {
            $promotionId = $promotionId->id;
        }
        
        return [
            'title' => 'required|string|min:3|max:255',
            'name' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|min:10',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
            'code' => 'nullable|string|min:3|max:50|unique:promotions,code' . ($promotionId ? ',' . $promotionId : ''),
            'badge_text' => 'nullable|string|min:2|max:50',
            'badge_color' => 'nullable|string|min:4|max:7',
            'is_featured' => 'boolean',
            'display_order' => 'integer|min:0',
            'product_ids' => 'array',
            'product_ids.*' => 'exists:products,id',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Tytuł promocji jest wymagany.',
            'title.string' => 'Tytuł promocji musi być tekstem.',
            'title.min' => 'Tytuł promocji musi mieć co najmniej 3 znaki.',
            'title.max' => 'Tytuł promocji nie może być dłuższy niż 255 znaków.',
            'name.required' => 'Nazwa promocji jest wymagana.',
            'name.string' => 'Nazwa promocji musi być tekstem.',
            'name.min' => 'Nazwa promocji musi mieć co najmniej 3 znaki.',
            'name.max' => 'Nazwa promocji nie może być dłuższa niż 255 znaków.',
            'description.string' => 'Opis musi być tekstem.',
            'description.min' => 'Opis musi mieć co najmniej 10 znaków.',
            'discount_type.required' => 'Typ rabatu jest wymagany.',
            'discount_type.in' => 'Typ rabatu musi być percentage lub fixed.',
            'discount_value.required' => 'Wartość rabatu jest wymagana.',
            'discount_value.numeric' => 'Wartość rabatu musi być liczbą.',
            'discount_value.min' => 'Wartość rabatu nie może być ujemna.',
            'starts_at.required' => 'Data rozpoczęcia jest wymagana.',
            'starts_at.date' => 'Data rozpoczęcia musi być prawidłową datą.',
            'ends_at.date' => 'Data zakończenia musi być prawidłową datą.',
            'ends_at.after' => 'Data zakończenia musi być po dacie rozpoczęcia.',
            'is_active.boolean' => 'Pole aktywne musi być prawdą lub fałszem.',
            'code.string' => 'Kod promocji musi być tekstem.',
            'code.min' => 'Kod promocji musi mieć co najmniej 3 znaki.',
            'code.max' => 'Kod promocji nie może być dłuższy niż 50 znaków.',
            'code.unique' => 'Ten kod promocji już istnieje.',
            'badge_text.string' => 'Tekst odznaki musi być tekstem.',
            'badge_text.min' => 'Tekst odznaki musi mieć co najmniej 2 znaki.',
            'badge_text.max' => 'Tekst odznaki nie może być dłuższy niż 50 znaków.',
            'badge_color.string' => 'Kolor odznaki musi być tekstem.',
            'badge_color.min' => 'Kolor odznaki musi mieć co najmniej 4 znaki.',
            'badge_color.max' => 'Kolor odznaki nie może być dłuższy niż 7 znaków.',
            'is_featured.boolean' => 'Pole wyróżniony musi być prawdą lub fałszem.',
            'display_order.integer' => 'Kolejność wyświetlania musi być liczbą całkowitą.',
            'display_order.min' => 'Kolejność wyświetlania nie może być ujemna.',
            'product_ids.array' => 'Produkty muszą być tablicą.',
            'product_ids.*.exists' => 'Wybrany produkt nie istnieje.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'tytuł promocji',
            'name' => 'nazwa promocji',
            'description' => 'opis',
            'discount_type' => 'typ rabatu',
            'discount_value' => 'wartość rabatu',
            'starts_at' => 'data rozpoczęcia',
            'ends_at' => 'data zakończenia',
            'is_active' => 'aktywny',
            'code' => 'kod promocji',
            'badge_text' => 'tekst odznaki',
            'badge_color' => 'kolor odznaki',
            'is_featured' => 'wyróżniony',
            'display_order' => 'kolejność wyświetlania',
            'product_ids' => 'produkty',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $validator->getData();
            
            // Walidacja dodatkowa dla discount_value
            if (isset($data['discount_type']) && $data['discount_type'] === 'percentage' && 
                isset($data['discount_value']) && $data['discount_value'] > 100) {
                $validator->errors()->add('discount_value', 'Rabat procentowy nie może być większy niż 100');
            }
        });
    }
} 