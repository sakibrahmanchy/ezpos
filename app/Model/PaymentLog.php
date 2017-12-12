<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    //

    public function Sales(){
        return $this->belongsToMany('App\Model\Sale');
    }
}
