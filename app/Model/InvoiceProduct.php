<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InvoiceProduct extends Model
{
    protected $casts = [
        'invoice_id' => 'integer',
        'price' => 'float',
        'branch_id' => 'integer',
        'product_id' => 'integer',
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}