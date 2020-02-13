<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class purchase_order extends Model
{
    //
    use Notifiable;

    public function purchase_details()
    {
        return $this->hasMany('App\purchase_detail', 'purchase_uuid', 'uuid');
    }

    
}
