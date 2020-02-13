<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\SoftDeletes;

class shop extends Model
{
    use Cachable, SoftDeletes;

    protected $dates = [
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'users_id');
    }

    public function purchase_orders()
    {
        return $this->hasMany('App\purchase_order', 'shop_id');
    }
}
