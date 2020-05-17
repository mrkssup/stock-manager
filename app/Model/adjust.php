<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class adjust extends Model
{
    protected $table = 'adjust';
    protected $primaryKey = 'adjust_id';
    protected $fillable =
        [
            'product_id',
            'user_id',
            'adjust_code',
            'adjust_date',
            'adjust_stock',
            'adjust_old',
            'adjust_new'
        ];
}
