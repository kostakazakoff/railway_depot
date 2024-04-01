<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class ChangeForgotenPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    public function rules(): array
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password'
        ];
    }


    public function failedValidation(Validator $validator)
    {
        $result = [
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ];
        throw new HttpResponseException(response()->json($result));
    }


    public function messages()
    {
        return [
            'token.required' => 'Липсва токен за автентикация.',
            'email.required' => 'Трябва да въведете имейл адрес!',
            'email.email' => 'Невалиден имейл адрес!',
            'password.required' => 'Моля, въведете нова парола.',
            'password.min' => 'Паролата трябва да съдържа минимум 8 символа',
            'password_confirmation.required' => 'Моля, въведете потвърждение на паролата.',
            'password_confirmation.same' => 'Паролата и потвърждението не съвпадат. Моля, въведете ги наново.'
        ];
    }
}
