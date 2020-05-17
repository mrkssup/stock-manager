<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class user_credits extends Model
{
    protected $table = 'user_credits';
    protected $primaryKey = 'credit_id';
    protected $fillable =
        [
            'user_id',
            'credit_amount',
        ];
}
