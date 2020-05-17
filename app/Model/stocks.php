<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class stocks extends Model
{
    protected $table = 'stocks';
    protected $primaryKey = 'stock_id';
    protected $fillable =
        [
            'stock_number',
            'stock_number_sale',
            'product_id',
            'stock_place_id',
        ];
}
