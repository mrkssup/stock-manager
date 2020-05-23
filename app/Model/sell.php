<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class sell extends Model
{
    protected $table = 'sell';
    protected $primaryKey = 'sell_id';
    protected $fillable =
        [
            'user_id',
            'sell_code',
            'sell_date',
            'sell_reference',
            'customer_id',
            'sell_tax',
            'sell_total',
            'sell_stock',
            'sell_status',
        ];
}
