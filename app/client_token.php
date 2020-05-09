<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class client_token extends Model
{
    protected $fillable = [
        'token',
        'description'
    ];
}
