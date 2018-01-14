<?php

namespace App\Http\Transformers;

use App\Models\ProfileImage;
use League\Fractal\TransformerAbstract;

class ProfileImageTransformer extends TransformerAbstract
{
    public function transform(ProfileImage $profileImage = null): array
    {
        if (is_null($profileImage)) {
            return [];
        }

        return [
            'id' => $profileImage->id,
        ];
    }
}
