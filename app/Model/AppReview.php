<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AppReview extends Model
{
    protected $casts = [
        'user_id' => 'integer',
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}

