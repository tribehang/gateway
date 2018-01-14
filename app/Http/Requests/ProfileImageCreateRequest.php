<?php

namespace App\Http\Requests;

class ProfileImageCreateRequest extends ApiRequestValidation
{
    public function rules()
    {
        return [
            'image' => 'required|string|base64Image',
        ];
    }
}
