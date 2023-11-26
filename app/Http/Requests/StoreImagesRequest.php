<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}