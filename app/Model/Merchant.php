<?php

namespace App\Model;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
use \Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class Merchant extends Model implements Authenticatable
{
    // use HasFactory;
    use Notifiable;
    use AuthenticableTrait;
    use HasApiTokens;
    protected $hidden = ['password'];

    protected $fillable = [
        'name', 'phone', 'email', 'password', 'image', 'status'
    ];


    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function services()
    {
        return $this->hasMany(MerchantService::class);
    }
}
