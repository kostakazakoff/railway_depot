<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'email' => 'required|email',
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
            'email.required' => 'Трябва да въведете имейл адрес!',
            'email.email' => 'Невалиден имейл адрес!',
        ];
    }
}
