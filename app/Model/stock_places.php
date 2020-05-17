<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class stock_places extends Model
{
    protected $table = 'stock_places';
    protected $primaryKey = 'stock_place_id';
    protected $fillable =
        [
            'stock_place_code',
            'stock_place_name',
            'stock_place_detail',
        ];
}
