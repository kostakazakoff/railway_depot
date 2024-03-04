<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    

    public function rules(): array
    {
        return [
            'inventory_number' => 'required',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
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
            'inventory_number.required' => 'Трябва да въведете инвентарен номер',
            'description.required' => 'Трябва да въведете описание',
            'price.required' => 'Трябва да въведете цена',
            'price.numeric' => 'Цената трябва да бъде число',
            'price.min' => 'Цената може да бъде само положително число',
        ];
    }
}
