<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class po_product extends Model
{
    protected $table = 'po_product';
    protected $primaryKey = 'po_product_id';
    protected $fillable =
        [
            'purchase_id',
            'product_id',
            'product_number',
            'product_total',
        ];
}
