<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    

    public function rules(): array
    {
        return [
            'inventory_number' => 'required',
            'description' => 'required',
            'price' => 'required',
            'images' => 'max: 5048',
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
            'inventory_number.required' => 'inventory number field is required',
            'description.required' => 'Description field is required',
            'price.required' => 'Price field is required',
        ];
    }
}
