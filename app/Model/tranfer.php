<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class tranfer extends Model
{
    protected $table = 'tranfer';
    protected $primaryKey = 'tranfer_id';
    protected $fillable =
        [
            'product_id',
            'user_id',
            'tranfer_code',
            'tranfer_date',
            'tranfer_stock_old',
            'tranfer_stock_new',
            'tranfer_stock_number',
        ];
}
