<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class adjust_stock extends Model
{
    protected $table = 'adjust_stock';
    protected $primaryKey = 'adjust_stock_id';
    protected $fillable =
        [
            'adjust_id',
            'stock_place_id',
            'adjust_stock_old',
            'adjust_stock_new',
        ];
}
