<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $casts = [
        'checked' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}

