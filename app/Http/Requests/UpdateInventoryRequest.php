<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class UpdateInventoryRequest extends FormRequest
{
    /**
     TODO:
     */
    public function authorize(): bool
    {
        return true;
    }

    
    public function rules(): array
    {
        return [
            'quantity' => 'integer|min:1',
        ];
    }


    public function failedValidation(Validator $validator)
    {
        $result = array([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]);
        throw new HttpResponseException(response()->json($result));
    }


    public function messages()
    {
        return [
            'quantity.integer' => 'Quantity field has to be an integer',
            'quantity.min' => 'Quantity has to be a positive number',
        ];
    }
}
