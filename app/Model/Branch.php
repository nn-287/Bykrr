<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Branch extends Authenticatable
{
    use Notifiable;
    protected $fillable = [
        'name', 'store_id', 'email', 'password', 'service_type', 'address', 'status', 'featured', 'coverage', 'image'
    ];

    protected $casts = [
        'coverage' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function sub_branches()
    {
        return $this->hasMany(SubBranch::class);
    }
    
    public function service_prices()
    {
        return $this->hasMany(ServicePrice::class);
    }


   
}
