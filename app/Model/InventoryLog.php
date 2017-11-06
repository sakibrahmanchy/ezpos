<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{

    public function Item(){
        return $this->belongsTo('App\Model\Item')->with('Category');
    }

    public function User(){
        return $this->belongsTo('App\Model\User');
    }


}
