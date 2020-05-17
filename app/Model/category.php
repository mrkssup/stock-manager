<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class category extends Model
{
    protected $table = 'category';
    protected $primaryKey = 'category_id';
    protected $fillable =
        [
            'category_name',
            'user_id'
        ];
}
