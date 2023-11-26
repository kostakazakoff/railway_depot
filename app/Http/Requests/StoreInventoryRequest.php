<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class StoreInventoryRequest extends FormRequest
{
    /**
     TODO: Determine if the user is authorized to make this request.
     */
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
            'store_id.required' => 'Store location field is required',
            'quantity.required' => 'Quantity field is required',
            'quantity.integer' => 'Quantity field has to be an integer',
            'quantity.min' => 'Quantity has to be a positive number',
            'position.required' => 'Position field is required'
        ];
    }
}
