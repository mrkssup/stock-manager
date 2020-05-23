<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class customer extends Model
{
    protected $table = 'customer';
    protected $primaryKey = 'customer_id';
    protected $fillable =
        [
            'customer_name',
            'customer_detail'
        ];
}
