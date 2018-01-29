<?php

namespace App\Repositories;

use App\Models\ProfileImage;

class ProfileImageRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new ProfileImage());
    }
}