<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    //

    use SoftDeletes;

    protected $fillable = ['first_name','last_name','phone','image_token','image','address_1','address_2','city','state','zip','country',
                                'comments','hire_date','birthday','employee_number','user_id'];


    public function counters() {
        return $this->belongsToMany('App\Model\Counter');
    }

    public function user() {
        return $this->belongsTo('App\Model\User');
    }
}
