<?php

namespace App\Http\Transformers;

use App\Models\User;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'profileImage',
    ];

    public function transform(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }

    public function includeProfileImage(User $user): Item
    {
        return $this->item(
            $user->profileImage, new ProfileImageTransformer()
        );
    }
}
