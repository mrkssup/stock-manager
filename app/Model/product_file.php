<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class product_file extends Model
{
    protected $table = 'product_file';
    protected $primaryKey = 'product_file_id';
    protected $fillable =
        [
            'product_file_name',
            'product_file_server',
            'product_file_ext',
            'product_id',
        ];
}
