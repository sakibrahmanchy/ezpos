<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    protected $fillable = [
        'name', 'description','counter_code','printer_connection_type','printer_ip','printer_port',"isDefault"
    ];

    public function sale(){
        return $this->hasMany('App\Model\Sale');
    }

    public function employees() {
        return $this->belongsToMany('App\Model\Employee');
    }
}
