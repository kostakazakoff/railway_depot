<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
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
            'email.required' => 'Моля, въведете имейл!',
            'email.email' => 'Невалиден имейл!',
            'email.max' => 'Невалиден имейл!',
            'email.unique' => 'Този имейл вече съществува в базата данни!',
            'password.required' => 'Въведете парола!',
            'password.min' => 'Паролата трябва да съдържа минимум 6 символа!',
        ];
    }
}
