<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class shipment extends Model
{
    protected $table = 'shipment';
    protected $primaryKey = 'sell_shipment_id';
    protected $fillable =
        [
            'sell_id',
            'shipment_id',
            'tracking_number',
            'shipment_detail',
        ];
}
