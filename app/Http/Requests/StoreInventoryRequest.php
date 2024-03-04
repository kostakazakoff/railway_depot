<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class StoreInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    

    public function rules(): array
    {
        return [
            'store_id' => 'required',
            'quantity' => 'required|integer|min:1',
            'position' => 'required'
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
            'store_id.required' => 'Трябва да въведете склад',
            'quantity.required' => 'Трябва да въведете количество',
            'quantity.integer' => 'Количеството може да бъде само цяло число',
            'quantity.min' => 'Количеството може да бъде само положително число',
            'position.required' => 'Трябва да въведете позиция на артикула'
        ];
    }
}
