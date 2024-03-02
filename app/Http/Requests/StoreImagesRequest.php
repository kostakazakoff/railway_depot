<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class StoreImagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    

    public function rules(): array
    {
        return [
            'images.*' => 'required|mimes:png,jpg,jpeg,webp|max:5120',
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
            'images.*.max' => 'Maximum file size is 5MB',
        ];
    }
}