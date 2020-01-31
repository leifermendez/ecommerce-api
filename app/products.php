<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\SoftDeletes;

class products extends Model
{
    use Cachable, SoftDeletes;

    protected $dates = [
        'deleted_at'
    ];
}
