<?php

namespace App\Http\Requests;

class UserUpdateRequest extends ApiRequestValidation
{
    public function rules()
    {
        return [
            'username' => 'string|max:255|unique:users,id,' . $this->user()->id,
        ];
    }
}
