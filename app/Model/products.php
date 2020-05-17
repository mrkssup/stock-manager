<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class products extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    protected $fillable =
        [
            'product_code',
            'product_name',
            'product_status',
            'product_price_buy',
            'product_price_sell',
            'product_unit',
            'product_volume',
            'user_id',
            'category_id',
        ];
}
