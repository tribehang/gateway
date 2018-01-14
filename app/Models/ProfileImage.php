<?php

namespace App\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileImage extends Model
{
    const IMAGE_JPG_TYPE = 'jpg';
    const IMAGE_UPLOAD_FOLDER = 'profile_images';

    use UuidModelTrait;

    public $incrementing = false;

    protected $fillable = [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
