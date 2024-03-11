<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => 'required|unique',
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
            'name.required' => 'Трябва да въведете наименование на склада!',
            'name.unique' => 'Вече има склад с това наименование!'
        ];
    }
}
