<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use App\Scopes\ZoneScope;
use Illuminate\Database\Eloquent\Builder;

class Zone extends Model
{
    use HasFactory;
    use HasSpatial;

    protected $casts = [
        'id'=>'integer',
        'status'=>'integer',
        'delivery_fee'=>'float',
        'coordinates' => Polygon::class,
    ];

    protected $fillable = [
        'coordinates'
    ];

    public function scopeContains($query,$abc){
        return $query->whereRaw("ST_Distance_Sphere(coordinates, POINT({$abc}))");
    }
   

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    protected static function booted()
    {
        static::addGlobalScope(new ZoneScope);
     
    }
 
    public static function query()
    {
        return parent::query();
    }
    public function getNameAttribute($value){

        return $value;
    }

}
