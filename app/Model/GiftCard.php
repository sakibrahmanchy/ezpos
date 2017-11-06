<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    protected $fillable= ['id','gift_card_number','description','value','customer_id','status'];

    public function Customer(){
        return $this->belongsTo('App\Model\Customer');
    }

}
