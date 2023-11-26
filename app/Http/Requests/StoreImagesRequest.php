<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class StoreImagesRequest extends FormRequest
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
            'images.*' => 'required|mimes:png,jpg,jpeg|max:2048',
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
            'images.required' => 'At least one image is required',
            'images.*.mimes' => 'Available file formats are png, jpg, jpeg',
            'images.*.max' => 'Maximum file size is 2MB',
        ];
    }
}