<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    //
    use SoftDeletes;

    protected $fillable = ['company_name','first_name','last_name','phone','image_location','image','address_1','address_2','city','state','zip','country',
        'comments','account'];
}
