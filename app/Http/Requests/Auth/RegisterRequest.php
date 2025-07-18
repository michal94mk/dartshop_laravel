<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
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
        $rules = [
            'name' => 'required|string|max:255|min:2',
            'first_name' => 'required|string|max:255|min:2',
            'last_name' => 'required|string|max:255|min:2',
            'email' => 'required|string|email:rfc,dns|max:255|unique:'.User::class,
            'password' => 'required|confirmed|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            'terms' => 'required|accepted',
        ];
        
        // Commented out temporarily for testing
        // if (app()->environment('production')) {
        //     $rules['g-recaptcha-response'] = ['required', 'captcha'];
        // }
        
        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nazwa użytkownika jest wymagana.',
            'name.string' => 'Nazwa użytkownika musi być tekstem.',
            'name.min' => 'Nazwa użytkownika musi mieć co najmniej 2 znaki.',
            'name.max' => 'Nazwa użytkownika nie może być dłuższa niż 255 znaków.',
            'first_name.required' => 'Imię jest wymagane.',
            'first_name.string' => 'Imię musi być tekstem.',
            'first_name.min' => 'Imię musi mieć co najmniej 2 znaki.',
            'first_name.max' => 'Imię nie może być dłuższe niż 255 znaków.',
            'last_name.required' => 'Nazwisko jest wymagane.',
            'last_name.string' => 'Nazwisko musi być tekstem.',
            'last_name.min' => 'Nazwisko musi mieć co najmniej 2 znaki.',
            'last_name.max' => 'Nazwisko nie może być dłuższe niż 255 znaków.',
            'email.required' => 'Adres email jest wymagany.',
            'email.string' => 'Adres email musi być tekstem.',
            'email.email' => 'Adres email musi być prawidłowym adresem email.',
            'email.max' => 'Adres email nie może być dłuższy niż 255 znaków.',
            'email.unique' => 'Ten adres email jest już zajęty.',
            'password.required' => 'Hasło jest wymagane.',
            'password.confirmed' => 'Potwierdzenie hasła nie pasuje.',
            'password.min' => 'Hasło musi mieć co najmniej 8 znaków.',
            'password.regex' => 'Hasło musi zawierać co najmniej jedną małą literę, jedną wielką literę, jedną cyfrę i jeden znak specjalny.',
            'terms.required' => 'Musisz zaakceptować regulamin.',
            'terms.accepted' => 'Musisz zaakceptować regulamin.',
            'g-recaptcha-response.required' => 'Potwierdź, że nie jesteś robotem.',
            'g-recaptcha-response.captcha' => 'Weryfikacja captcha nie powiodła się. Spróbuj ponownie.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nazwa użytkownika',
            'first_name' => 'imię',
            'last_name' => 'nazwisko',
            'email' => 'adres email',
            'password' => 'hasło',
            'password_confirmation' => 'potwierdzenie hasła',
            'terms' => 'regulamin',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->has('email')) {
                $email = $this->input('email');
                $user = \App\Models\User::where('email', $email)->first();
                if ($user && !$user->hasVerifiedEmail()) {
                    $validator->errors()->add('email', 'Konto z tym adresem e-mail już istnieje, ale nie zostało zweryfikowane. <a href=\"/api/email/verification-notification\">Kliknij tutaj, aby ponownie wysłać link weryfikacyjny</a>.');
                }
            }
        });
    }
} 