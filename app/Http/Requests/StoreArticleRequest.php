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
            'price' => 'required|integer|min:0',
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
            'inventory_number.required' => 'Inventory number field is required',
            'inventory_number.unique' => 'This inventory number allready exists',
            'description.required' => 'Description field is required',
            'price.required' => 'Price field is required',
            'price.integer' => 'Price has to be an integer',
            'price.min' => 'Price has to be a positive number',
        ];
    }
}
