<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class purchase extends Model
{
    protected $table = 'purchase';
    protected $primaryKey = 'purchase_id';
    protected $fillable =
        [
            'user_id',
            'purchase_code',
            'purchase_date',
            'purchase_reference',
            'customer_id',
            'purchase_tax',
            'purchase_total',
            'purchase_stock',
            'purchase_status_pay',
            'purchase_status_tranfer',
        ];
}
