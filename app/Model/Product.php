<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\Branch;

class Product extends Model
{

    protected $fillable = ['merchant_id'];
    protected $casts = [
        'tax' => 'float',
        'price' => 'float',
        'status' => 'integer',
        'discount' => 'float',
        'set_menu' => 'integer',
        'is_medicine' => 'integer',
        'price_range_enabled' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', '=', 1);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }


    //Merchant Model
    public function merchants()
{
    return $this->hasOne(Merchant::class, 'id', 'merchant_id')->latest();
}
    //


    public function rating()
    {
        return $this->hasMany(Review::class)
            ->select(DB::raw('avg(rating) average, product_id'))
            ->groupBy('product_id');
    } 

    public function branches()//New
    {
        return $this->hasMany(Branch::class);
    }

}
