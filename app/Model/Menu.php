<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name', 'description', 'logo_filename'
    ];

    public function products()
    {
        return $this->belongsToMany('App\Model\Item')->with('category', 'images')->orderBy('item_name');
    }

//    public function combos()
//    {
//        return $this->belongsToMany('App\Model\Combo')->orderBy('name');
//    }
}
