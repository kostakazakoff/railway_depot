<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    

    public function rules(): array
    {
        return [
            'price' => 'integer|min:0',
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
            'price.integer' => 'Price has to be an integer',
            'price.min' => 'Price has to be a positive number',
        ];
    }
}
