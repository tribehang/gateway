<?php

namespace App\Http\Requests;

use Illuminate\Http\JsonResponse;
use SMSkin\LumenMake\Requests\FormRequest;

class ApiRequestValidation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function response(array $errors)
    {
        return new JsonResponse([
            'error' => current($errors)[0],
            'errors' => $errors,
        ], 422);
    }
}
