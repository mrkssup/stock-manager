<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class users extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $fillable = 
        [
            'username',
            'password',
            'first_name',
            'last_name',
            'email',
            'tel',
            'token',
            'verify',
        ];
}
