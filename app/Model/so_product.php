<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class so_product extends Model
{
    protected $table = 'so_product';
    protected $primaryKey = 'so_product_id';
    protected $fillable =
        [
            'sell_id',
            'product_id',
            'product_number',
            'product_total',
        ];
}
